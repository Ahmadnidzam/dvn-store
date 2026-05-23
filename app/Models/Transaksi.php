<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksis';

    protected $fillable = [
        'user_id',
        'platform_id',
        'tipe',
        'amount',
        'platform_fee',
        'net_amount',
        'metode',
        'kode_transaksi',
        'midtrans_order_id',
        'snap_token',
        'status',
        'paid_at',
        'midtrans_response',
    ];

    protected $casts = [
        'paid_at'           => 'datetime',
        'midtrans_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(Pengguna::class, 'user_id');
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }
}
