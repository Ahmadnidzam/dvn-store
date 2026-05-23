<?php

namespace App\Services;

use App\Models\Transaksi;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

/**
 * Wrapper Midtrans Snap.
 * Dipakai untuk 2 use case:
 *   1) Customer membeli platform (tipe = purchase)
 *   2) Developer membayar upload fee Rp 10.000 (tipe = upload_fee)
 *
 * Memerlukan composer require midtrans/midtrans-php
 */
class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('dvnstore.midtrans.server_key');
        Config::$isProduction = config('dvnstore.midtrans.is_production');
        Config::$isSanitized  = config('dvnstore.midtrans.is_sanitized');
        Config::$is3ds        = config('dvnstore.midtrans.is_3ds');
    }

    /**
     * Buat Snap token untuk sebuah Transaksi yang sudah disimpan ke DB.
     * Caller harus menyimpan snap_token & midtrans_order_id ke kolom transaksis.
     */
    public function createSnapToken(Transaksi $transaksi, array $itemDetails, array $customer): string
    {
        $orderId = 'DVN-' . $transaksi->id . '-' . strtoupper(uniqid());

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $transaksi->amount,
            ],
            'item_details'    => $itemDetails,
            'customer_details'=> $customer,
            'callbacks'       => [
                'finish' => url('/payment/finish'),
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $transaksi->update([
            'midtrans_order_id' => $orderId,
            'snap_token'        => $snapToken,
        ]);

        return $snapToken;
    }

    /**
     * Parse notifikasi webhook Midtrans → return array status terstandar.
     * Caller (controller) memutuskan apa yang dilakukan dengan transaksi.
     */
    public function handleNotification(): array
    {
        $notif = new Notification();

        $status  = $notif->transaction_status;
        $fraud   = $notif->fraud_status ?? null;
        $orderId = $notif->order_id;
        $type    = $notif->payment_type ?? null;

        $finalStatus = match (true) {
            in_array($status, ['capture', 'settlement']) && $fraud !== 'deny' => 'paid',
            in_array($status, ['deny', 'cancel', 'failure'])                  => 'failed',
            $status === 'expire'                                              => 'expired',
            $status === 'pending'                                             => 'pending',
            default                                                           => 'pending',
        };

        return [
            'order_id'        => $orderId,
            'status'          => $finalStatus,
            'payment_type'    => $type,
            'raw_status'      => $status,
            'fraud'           => $fraud,
            'raw_notification'=> json_decode(json_encode($notif), true),
        ];
    }
}
