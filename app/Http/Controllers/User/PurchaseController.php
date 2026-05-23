<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\Platform;
use App\Models\Transaksi;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PurchaseController extends Controller
{
    /**
     * Free download. Hanya untuk platform dengan harga = 0.
     */
    public function freeDownload($id)
    {
        $platform = Platform::available()->findOrFail($id);
        if ($platform->harga > 0) {
            return back()->with('error', 'Produk ini berbayar.');
        }

        Download::firstOrCreate([
            'user_id'     => Session::get('user_id'),
            'platform_id' => $platform->id,
        ]);

        return back()->with('success', 'Berhasil ditambahkan ke library Anda!');
    }

    /**
     * Inisiasi pembayaran via Midtrans Snap.
     */
    public function buy($id, MidtransService $midtrans)
    {
        $platform = Platform::available()->findOrFail($id);
        if ($platform->harga <= 0) {
            return back()->with('error', 'Produk gratis, gunakan tombol Get.');
        }

        $userId = Session::get('user_id');

        if (Download::where('user_id', $userId)->where('platform_id', $platform->id)->exists()) {
            return back()->with('error', 'Produk sudah ada di library Anda.');
        }

        $platformFeePercent = (int) config('dvnstore.platform_fee_percent');
        $amount     = (int) $platform->harga;
        $fee        = (int) floor($amount * $platformFeePercent / 100);
        $netDev     = $amount - $fee;

        $transaksi = DB::transaction(function () use ($userId, $platform, $amount, $fee, $netDev) {
            return Transaksi::create([
                'user_id'         => $userId,
                'platform_id'     => $platform->id,
                'tipe'            => 'purchase',
                'amount'          => $amount,
                'platform_fee'    => $fee,
                'net_amount'      => $netDev,
                'kode_transaksi'  => 'PUR-' . strtoupper(uniqid()),
                'status'          => 'pending',
            ]);
        });

        try {
            $snapToken = $midtrans->createSnapToken(
                $transaksi,
                [[
                    'id'       => $platform->id,
                    'price'    => $amount,
                    'quantity' => 1,
                    'name'     => substr($platform->nama_platform, 0, 50),
                ]],
                [
                    'first_name' => substr(Session::get('name'), 0, 20),
                    'email'      => optional(\App\Models\Pengguna::find($userId))->email,
                ],
            );
        } catch (\Throwable $e) {
            Log::error('Midtrans purchase token failed: ' . $e->getMessage());
            $transaksi->delete();

            return back()->with('error', 'Konfigurasi Midtrans belum valid. Periksa Server Key, Client Key, dan mode sandbox/production.');
        }

        return view('User.Checkout', [
            'platform'  => $platform,
            'transaksi' => $transaksi,
            'snapToken' => $snapToken,
            'clientKey' => config('dvnstore.midtrans.client_key'),
        ]);
    }

    /**
     * Halaman finish setelah customer kembali dari Midtrans.
     */
    public function finish(Request $request)
    {
        return view('User.PaymentFinish', [
            'orderId' => $request->input('order_id'),
            'status'  => $request->input('transaction_status'),
        ]);
    }
}
