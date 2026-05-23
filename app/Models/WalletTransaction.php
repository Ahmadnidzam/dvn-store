<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $table = 'wallet_transactions';

    protected $fillable = [
        'wallet_id', 'transaksi_id', 'tipe',
        'amount', 'saldo_after', 'description',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
