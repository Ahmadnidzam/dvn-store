<?php

namespace App\Jobs;

use App\Models\Platform;
use App\Services\VirusTotalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Async job: scan PENDING file update developer ke VirusTotal.
 *
 * Berbeda dari ScanUploadedFileJob (scan upload pertama):
 *   - Job ini scan kolom pending_* (bukan kolom utama)
 *   - Selama scan, file_path LAMA tetap aktif untuk download (zero downtime)
 *   - Kalau clean: hapus file lama, swap pending_file_path → file_path,
 *     update file_updated_at = now() → user di library dapat badge "Update tersedia"
 *   - Kalau infected: hapus pending file, file lama tetap aktif, dev dapat log error
 *
 * Pola sama: 2 fase (upload → polling re-dispatch dengan delay).
 */
class ScanPendingUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public int $platformId,
        public ?string $analysisId = null,
        public int $pollAttempt = 0,
    ) {}

    public function handle(VirusTotalService $vt): void
    {
        $platform = Platform::find($this->platformId);
        if (!$platform || !$platform->pending_file_path) {
            return;
        }

        // Mode dev / VirusTotal tidak aktif → langsung swap (anggap clean)
        if (!$vt->isAvailable()) {
            $this->swapAsClean($platform, ['note' => 'VirusTotal disabled — auto clean (DEV mode).']);
            return;
        }

        // ============ FASE 1: UPLOAD ke VirusTotal ============
        if ($this->analysisId === null) {
            $platform->update(['pending_scan_status' => 'scanning']);

            $absolute = Storage::disk('public')->path($platform->pending_file_path);
            $upload = $vt->uploadFile($absolute);

            if (empty($upload['ok'])) {
                Log::warning('VirusTotal upload (update) gagal', [
                    'platform' => $platform->id, 'raw' => $upload['raw'],
                ]);
                $platform->update([
                    'pending_scan_status' => 'error',
                    'pending_scan_result' => $upload['raw'],
                ]);
                return;
            }

            static::dispatch($platform->id, $upload['id'], 1)
                ->delay(now()->addSeconds(30));
            return;
        }

        // ============ FASE 2: POLL hasil ============
        $analysis = $vt->getAnalysis($this->analysisId);
        $verdict  = $analysis['verdict'] ?? 'scanning';

        if ($verdict === 'clean') {
            $this->swapAsClean($platform, $analysis['stats']);
            return;
        }

        if ($verdict === 'infected') {
            $this->rejectAsInfected($platform, $analysis['stats']);
            return;
        }

        // Masih scanning → re-dispatch (max 6 percobaan ≈ 3 menit)
        if ($this->pollAttempt < 6) {
            static::dispatch($platform->id, $this->analysisId, $this->pollAttempt + 1)
                ->delay(now()->addSeconds(30));
            return;
        }

        $platform->update([
            'pending_scan_status' => 'error',
            'pending_scan_result' => [
                'note'  => 'Analysis tidak selesai dalam batas waktu polling.',
                'stats' => $analysis['stats'] ?? null,
            ],
        ]);
    }

    /**
     * Swap file lama dengan pending file (clean) — atomik:
     *   1) Hapus file lama dari storage
     *   2) Pindah pending_file_path → file_path, update size + file_updated_at
     *   3) Clear semua kolom pending_*
     */
    protected function swapAsClean(Platform $platform, $scanStats): void
    {
        DB::transaction(function () use ($platform, $scanStats) {
            $oldFile = $platform->file_path;

            $platform->refresh();

            // Pindahkan pending → active
            $platform->file_path        = $platform->pending_file_path;
            $platform->file_size        = $platform->pending_file_size;
            $platform->scan_status      = 'clean';
            $platform->scan_result      = is_array($scanStats) ? $scanStats : ['note' => $scanStats];
            $platform->is_published     = true;
            $platform->file_updated_at  = now();

            // Reset pending fields
            $platform->pending_file_path   = null;
            $platform->pending_file_size   = 0;
            $platform->pending_scan_status = null;
            $platform->pending_scan_result = null;
            $platform->pending_uploaded_at = null;

            $platform->save();

            // Hapus file lama dari storage SETELAH commit DB sukses
            if ($oldFile && $oldFile !== $platform->file_path) {
                Storage::disk('public')->delete($oldFile);
            }
        });
    }

    /**
     * Update di-reject karena infected:
     *   - Hapus file pending dari storage
     *   - Clear kolom pending_*
     *   - File lama TETAP aktif (zero downtime)
     */
    protected function rejectAsInfected(Platform $platform, $scanStats): void
    {
        $pendingFile = $platform->pending_file_path;

        $platform->update([
            'pending_file_path'   => null,
            'pending_file_size'   => 0,
            'pending_scan_status' => 'infected',  // log saja, akan di-clear di request berikutnya
            'pending_scan_result' => is_array($scanStats) ? $scanStats : ['note' => $scanStats],
            'pending_uploaded_at' => null,
        ]);

        if ($pendingFile) {
            Storage::disk('public')->delete($pendingFile);
        }

        Log::warning('Pending update infected', [
            'platform_id' => $platform->id,
            'stats'       => $scanStats,
        ]);
    }
}
