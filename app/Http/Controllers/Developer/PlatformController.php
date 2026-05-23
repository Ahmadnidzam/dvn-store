<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Jobs\ScanPendingUpdateJob;
use App\Jobs\ScanUploadedFileJob;
use App\Models\Platform;
use App\Models\Transaksi;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::with('uploadFeeTransaksi')
            ->where('dev_id', Session::get('user_id'))
            ->latest()->get();
        return view('Developer.Platforms.Index', compact('platforms'));
    }

    public function create()
    {
        $genres = config('dvnstore.genres');
        return view('Developer.Platforms.Create', compact('genres'));
    }

    /**
     * Step 1: simpan draft platform (is_published=false, scan_status=pending).
     * Step 2: redirect ke halaman bayar upload fee via Midtrans.
     */
    public function store(Request $request, MidtransService $midtrans)
    {
        $request->validate([
            'category'      => 'required|in:app,game',
            'nama_platform' => 'required|string|max:200|unique:platforms,nama_platform',
            'genre'         => ['required', 'string', 'max:100', Rule::in($this->allowedGenres())],
            'harga'         => 'required|integer|min:0',
            'deskripsi'     => 'required|string|min:50',
            'icon'          => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cuplikan'      => 'nullable|mimes:mp4,mkv,webm,mov|max:20480',
            'file'          => 'required|file|mimes:zip,apk,exe,7z,rar|max:204800',
        ]);

        $devId = Session::get('user_id');
        $iconPath  = $request->file('icon')->store('images/icons', 'public');
        $videoPath = $request->hasFile('cuplikan')
            ? $request->file('cuplikan')->store('videos', 'public')
            : null;
        $file      = $request->file('file');
        $filePath  = $file->store('platform-files', 'public');

        $platform = DB::transaction(function () use ($request, $devId, $iconPath, $videoPath, $file, $filePath) {
            return Platform::create([
                'dev_id'        => $devId,
                'category'      => $request->category,
                'nama_platform' => $request->nama_platform,
                'slug'          => Str::slug($request->nama_platform) . '-' . Str::random(6),
                'genre'         => $request->genre,
                'harga'         => $request->harga,
                'icon'          => $iconPath,
                'deskripsi'     => $request->deskripsi,
                'cuplikan'      => $videoPath,
                'file_path'     => $filePath,
                'file_size'     => $file->getSize(),
                'scan_status'   => 'pending',
                'is_published'  => false,
            ]);
        });

        return redirect()->route('developer.platforms.upload-fee', $platform->id);
    }

    /**
     * Halaman bayar upload fee Rp 10.000.
     */
    public function uploadFeePage($platformId, MidtransService $midtrans)
    {
        $platform = Platform::where('dev_id', Session::get('user_id'))->findOrFail($platformId);

        if ($platform->uploadFeeTransaksi) {
            $existing = $platform->uploadFeeTransaksi;

            if ($existing->status === 'paid') {
                return redirect()->route('developer.platforms.index')->with('info', 'Upload fee sudah diproses.');
            }

            if ($existing->status === 'pending' && $existing->snap_token) {
                return view('Developer.Platforms.UploadFee', [
                    'platform'  => $platform,
                    'transaksi' => $existing,
                    'snapToken' => $existing->snap_token,
                    'fee'       => (int) config('dvnstore.upload_fee'),
                    'clientKey' => config('dvnstore.midtrans.client_key'),
                ]);
            }

            $platform->update(['upload_fee_transaksi_id' => null]);
        }

        if ($platform->upload_fee_transaksi_id) {
            return redirect()->route('developer.platforms.index')->with('info', 'Upload fee sudah diproses.');
        }

        $fee = (int) config('dvnstore.upload_fee');

        $transaksi = Transaksi::create([
            'user_id'         => Session::get('user_id'),
            'platform_id'     => $platform->id,
            'tipe'            => 'upload_fee',
            'amount'          => $fee,
            'platform_fee'    => $fee,
            'net_amount'      => 0,
            'kode_transaksi'  => 'UPL-' . strtoupper(uniqid()),
            'status'          => 'pending',
        ]);

        try {
            $snapToken = $midtrans->createSnapToken(
                $transaksi,
                [[
                    'id'       => 'upload-fee-' . $platform->id,
                    'price'    => $fee,
                    'quantity' => 1,
                    'name'     => 'Upload Fee ' . substr($platform->nama_platform, 0, 40),
                ]],
                [
                    'first_name' => substr(Session::get('name'), 0, 20),
                    'email'      => optional(\App\Models\Pengguna::find(Session::get('user_id')))->email,
                ],
            );
        } catch (\Throwable $e) {
            Log::error('Midtrans upload fee token failed: ' . $e->getMessage());
            $transaksi->delete();

            return redirect()->route('developer.platforms.index')
                ->with('error', 'Konfigurasi Midtrans belum valid. Periksa Server Key, Client Key, dan mode sandbox/production.');
        }

        $platform->update(['upload_fee_transaksi_id' => $transaksi->id]);

        return view('Developer.Platforms.UploadFee', [
            'platform'  => $platform,
            'transaksi' => $transaksi,
            'snapToken' => $snapToken,
            'fee'       => $fee,
            'clientKey' => config('dvnstore.midtrans.client_key'),
        ]);
    }

    public function edit($id)
    {
        $platform = Platform::where('dev_id', Session::get('user_id'))->findOrFail($id);
        $genres = config('dvnstore.genres');
        return view('Developer.Platforms.Edit', compact('platform', 'genres'));
    }

    public function update(Request $request, $id)
    {
        $platform = Platform::where('dev_id', Session::get('user_id'))->findOrFail($id);
        $request->validate([
            'nama_platform' => "required|string|max:200|unique:platforms,nama_platform,{$id}",
            'genre'         => ['required', 'string', 'max:100', Rule::in($this->allowedGenres())],
            'harga'         => 'required|integer|min:0',
            'deskripsi'     => 'required|string|min:50',
            'icon'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cuplikan'      => 'nullable|mimes:mp4,mkv,webm,mov|max:20480',
        ]);

        $data = $request->only(['nama_platform', 'genre', 'harga', 'deskripsi']);

        if ($request->hasFile('icon')) {
            if ($platform->icon) Storage::disk('public')->delete($platform->icon);
            $data['icon'] = $request->file('icon')->store('images/icons', 'public');
        }
        if ($request->hasFile('cuplikan')) {
            if ($platform->cuplikan) Storage::disk('public')->delete($platform->cuplikan);
            $data['cuplikan'] = $request->file('cuplikan')->store('videos', 'public');
        }

        $platform->update($data);
        return redirect()->route('developer.platforms.index')->with('success', 'Produk diperbarui!');
    }

    public function destroy($id)
    {
        $platform = Platform::where('dev_id', Session::get('user_id'))->findOrFail($id);
        if ($platform->icon)              Storage::disk('public')->delete($platform->icon);
        if ($platform->cuplikan)          Storage::disk('public')->delete($platform->cuplikan);
        if ($platform->file_path)         Storage::disk('public')->delete($platform->file_path);
        if ($platform->pending_file_path) Storage::disk('public')->delete($platform->pending_file_path);
        $platform->delete();
        return redirect()->route('developer.platforms.index')->with('success', 'Produk dihapus.');
    }

    // ============================================================
    // FITUR UPDATE FILE (safe replace in-place)
    // ============================================================

    /**
     * Halaman form upload file installer baru untuk produk yang sudah published.
     * Hanya bisa diakses kalau produk sudah punya file aktif yang clean.
     */
    public function updateFileForm($id)
    {
        $platform = Platform::where('dev_id', Session::get('user_id'))->findOrFail($id);

        if ($platform->scan_status !== 'clean' || !$platform->is_published) {
            return redirect()->route('developer.platforms.index')
                ->with('error', 'Produk belum aktif. Selesaikan upload awal dulu sebelum mengganti file.');
        }

        return view('Developer.Platforms.UpdateFile', compact('platform'));
    }

    /**
     * Submit file baru → simpan ke pending_*, dispatch ScanPendingUpdateJob.
     * Tolak kalau sudah ada pending aktif (1 update per produk pada satu waktu).
     */
    public function updateFileStore(Request $request, $id)
    {
        $platform = Platform::where('dev_id', Session::get('user_id'))->findOrFail($id);

        if ($platform->scan_status !== 'clean' || !$platform->is_published) {
            return redirect()->route('developer.platforms.index')
                ->with('error', 'Produk belum aktif.');
        }

        // Hanya 1 pending aktif: cek kalau ada file pending yg sedang/akan discan
        if ($platform->hasPendingUpdate()
            && in_array($platform->pending_scan_status, ['pending', 'scanning'], true)) {
            return back()->with('error', 'Sudah ada pembaruan file yang sedang diproses. Tunggu hasil scan atau batalkan dulu.');
        }

        $request->validate([
            'file' => 'required|file|mimes:zip,apk,exe,7z,rar|max:204800',
        ]);

        $file       = $request->file('file');
        $pendingPath = $file->store('platform-files', 'public');

        // Bersihkan sisa pending lama (mis. status infected/error dari attempt sebelumnya)
        if ($platform->pending_file_path && $platform->pending_file_path !== $pendingPath) {
            Storage::disk('public')->delete($platform->pending_file_path);
        }

        $platform->update([
            'pending_file_path'   => $pendingPath,
            'pending_file_size'   => $file->getSize(),
            'pending_scan_status' => 'pending',
            'pending_scan_result' => null,
            'pending_uploaded_at' => now(),
        ]);

        ScanPendingUpdateJob::dispatch($platform->id);

        return redirect()->route('developer.platforms.index')
            ->with('success', 'File baru diunggah. Sedang dipindai keamanan oleh VirusTotal — file lama tetap aktif sampai pemindaian selesai.');
    }

    /**
     * Developer batalkan pending update — hapus file pending + clear kolom.
     */
    public function cancelPendingUpdate($id)
    {
        $platform = Platform::where('dev_id', Session::get('user_id'))->findOrFail($id);

        if (!$platform->hasPendingUpdate()) {
            return back()->with('info', 'Tidak ada pembaruan yang sedang diproses.');
        }

        // Hanya boleh cancel kalau scan belum selesai atau error
        if (!in_array($platform->pending_scan_status, ['pending', 'scanning', 'error'], true)) {
            return back()->with('error', 'Status saat ini tidak bisa dibatalkan.');
        }

        if ($platform->pending_file_path) {
            Storage::disk('public')->delete($platform->pending_file_path);
        }

        $platform->update([
            'pending_file_path'   => null,
            'pending_file_size'   => 0,
            'pending_scan_status' => null,
            'pending_scan_result' => null,
            'pending_uploaded_at' => null,
        ]);

        return redirect()->route('developer.platforms.index')
            ->with('success', 'Pembaruan dibatalkan.');
    }

    private function allowedGenres(): array
    {
        return collect(config('dvnstore.genres'))->flatten()->all();
    }
}
