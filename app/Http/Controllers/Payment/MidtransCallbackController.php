<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Jobs\ScanUploadedFileJob;
use App\Models\Download;
use App\Models\Pengguna;
use App\Models\Platform;
use App\Models\Transaksi;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    /**
     * Webhook Midtrans Snap.
     * Pasang URL ini di Dashboard Midtrans → Settings → Configuration → Payment Notification URL.
     */
    public function notify(Request $request, MidtransService $midtrans)
    {
        try {
            $parsed = $midtrans->handleNotification();
        } catch (\Throwable $e) {
            Log::error('Midtrans notify error: ' . $e->getMessage());
            return response()->json(['ok' => false], 200);
        }

        $result = DB::transaction(function () use ($parsed) {
            $transaksi = Transaksi::where('midtrans_order_id', $parsed['order_id'])
                ->lockForUpdate()
                ->first();

            if (!$transaksi) {
                return ['ok' => false, 'reason' => 'transaksi tidak ditemukan'];
            }

            if ($transaksi->status === 'paid') {
                return ['ok' => true, 'note' => 'already paid'];
            }

            $transaksi->update([
                'status'            => $parsed['status'],
                'metode'            => $parsed['payment_type'],
                'midtrans_response' => $parsed['raw_notification'],
                'paid_at'           => $parsed['status'] === 'paid' ? now() : null,
            ]);

            if ($parsed['status'] === 'paid') {
                $this->fulfill($transaksi);
            }

            return ['ok' => true];
        });

        return response()->json($result, 200);
    }

    /**
     * Aksi setelah pembayaran berhasil.
     * - upload_fee → trigger ScanUploadedFileJob
     * - purchase   → buat Download + kredit wallet developer
     */
    protected function fulfill(Transaksi $transaksi): void
    {
        DB::transaction(function () use ($transaksi) {
            if ($transaksi->tipe === 'upload_fee' && $transaksi->platform_id) {
                $platform = Platform::find($transaksi->platform_id);
                if ($platform) {
                    // Trigger scan asynchronous
                    ScanUploadedFileJob::dispatch($platform->id);
                }
                return;
            }

            if ($transaksi->tipe === 'purchase' && $transaksi->platform_id) {
                // Buat download (idempotent)
                Download::firstOrCreate(
                    ['user_id' => $transaksi->user_id, 'platform_id' => $transaksi->platform_id],
                    ['transaksi_id' => $transaksi->id]
                );

                // Kredit wallet developer
                $platform = Platform::find($transaksi->platform_id);
                if ($platform) {
                    if (WalletTransaction::where('transaksi_id', $transaksi->id)->where('tipe', 'credit')->exists()) {
                        return;
                    }

                    $wallet = Wallet::firstOrCreate(['dev_id' => $platform->dev_id], ['saldo' => 0]);
                    $wallet->credit(
                        (int) $transaksi->net_amount,
                        "Penjualan #{$transaksi->id} — {$platform->nama_platform}",
                        $transaksi->id,
                    );
                }
            }
        });
    }
}
