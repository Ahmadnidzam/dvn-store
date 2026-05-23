<?php

namespace App\Jobs;

use App\Models\Platform;
use App\Services\VirusTotalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Async job: scan file upload developer ke VirusTotal.
 *
 * Job ini dipecah jadi 2 fase agar tidak memblok worker:
 *   - Mode UPLOAD (default): set scanning, upload file, dispatch ulang dirinya
 *     sendiri dengan delay 30 detik untuk mode POLL.
 *   - Mode POLL: ambil hasil analisis. Kalau belum selesai, re-dispatch dengan
 *     delay sampai maks 6x percobaan (~3 menit). Kalau masih scanning juga,
 *     tandai error.
 */
class ScanUploadedFileJob implements ShouldQueue
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
        if (!$platform || !$platform->file_path) {
            return;
        }

        if (!$vt->isAvailable()) {
            $platform->update([
                'scan_status'  => 'clean',
                'scan_result'  => ['note' => 'VirusTotal disabled — auto clean (DEV mode).'],
                'is_published' => true,
            ]);
            return;
        }

        // ============ FASE 1: UPLOAD ============
        if ($this->analysisId === null) {
            $platform->update(['scan_status' => 'scanning']);

            $absolute = Storage::disk('public')->path($platform->file_path);
            $upload = $vt->uploadFile($absolute);

            if (empty($upload['ok'])) {
                Log::warning('VirusTotal upload gagal', ['platform' => $platform->id, 'raw' => $upload['raw']]);
                $platform->update([
                    'scan_status' => 'error',
                    'scan_result' => $upload['raw'],
                ]);
                return;
            }

            // Re-dispatch untuk polling pertama setelah 30 detik
            static::dispatch($platform->id, $upload['id'], 1)->delay(now()->addSeconds(30));
            return;
        }

        // ============ FASE 2: POLL ============
        $analysis = $vt->getAnalysis($this->analysisId);
        $verdict  = $analysis['verdict'] ?? 'scanning';

        if ($verdict === 'clean') {
            $platform->update([
                'scan_status'  => 'clean',
                'scan_result'  => $analysis['stats'],
                'is_published' => true,
            ]);
            return;
        }

        if ($verdict === 'infected') {
            Storage::disk('public')->delete($platform->file_path);
            $platform->update([
                'scan_status'  => 'infected',
                'scan_result'  => $analysis['stats'],
                'is_published' => false,
                'file_path'    => null,
            ]);
            return;
        }

        // Masih scanning → re-dispatch sampai 6x percobaan total (~3 menit dengan delay 30s)
        if ($this->pollAttempt < 6) {
            static::dispatch($platform->id, $this->analysisId, $this->pollAttempt + 1)
                ->delay(now()->addSeconds(30));
            return;
        }

        // Sudah habis percobaan → tandai error
        $platform->update([
            'scan_status' => 'error',
            'scan_result' => [
                'note'  => 'Analysis tidak selesai dalam batas waktu polling.',
                'stats' => $analysis['stats'] ?? null,
            ],
        ]);
    }
}
