<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Download;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    public function index()
    {
        $downloads = Download::with(['platform.developer'])
            ->where('user_id', Session::get('user_id'))
            ->latest()->get();

        return view('User.Unduhan', compact('downloads'));
    }

    public function destroy($id)
    {
        $d = Download::where('id', $id)->where('user_id', Session::get('user_id'))->firstOrFail();
        $d->delete();
        return back()->with('success', 'Item berhasil dihapus dari library.');
    }

    /**
     * Download file installer/APK setelah memiliki produk.
     * Setelah download sukses, touch downloads.updated_at supaya badge
     * "Update tersedia" di library hilang sampai dev rilis update berikutnya.
     */
    public function file($id)
    {
        $userId = Session::get('user_id');
        $download = Download::with('platform')->where('id', $id)->where('user_id', $userId)->firstOrFail();
        $platform = $download->platform;

        if (!$platform || !$platform->file_path || !Storage::disk('public')->exists($platform->file_path)) {
            return back()->with('error', 'File tidak tersedia.');
        }
        if ($platform->scan_status !== 'clean' || $platform->is_taken_down) {
            return back()->with('error', 'Produk ini tidak tersedia untuk diunduh.');
        }

        // Tandai user sudah punya versi terbaru → badge "Update tersedia" hilang
        $download->touch();

        return response()->download(Storage::disk('public')->path($platform->file_path));
    }
}
