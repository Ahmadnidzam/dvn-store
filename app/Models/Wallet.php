<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    protected $table = 'wallets';

    protected $fillable = ['dev_id', 'saldo'];

    public function developer()
    {
        return $this->belongsTo(Pengguna::class, 'dev_id');
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id');
    }

    /**
     * Tambah saldo + catat mutasi (atomik).
     */
    public function credit(int $amount, string $description, ?int $transaksiId = null): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $description, $transaksiId) {
            $wallet = self::whereKey($this->id)->lockForUpdate()->firstOrFail();
            $wallet->saldo += $amount;
            $wallet->save();

            $this->setRawAttributes($wallet->getAttributes(), true);

            return WalletTransaction::create([
                'wallet_id'    => $wallet->id,
                'transaksi_id' => $transaksiId,
                'tipe'         => 'credit',
                'amount'       => $amount,
                'saldo_after'  => $wallet->saldo,
                'description'  => $description,
            ]);
        });
    }

    /**
     * Kurangi saldo + catat mutasi (atomik). Throw kalau saldo kurang.
     */
    public function debit(int $amount, string $description, ?int $transaksiId = null): WalletTransaction
    {
        return DB::transaction(function () use ($amount, $description, $transaksiId) {
            $wallet = self::whereKey($this->id)->lockForUpdate()->firstOrFail();
            if ($wallet->saldo < $amount) {
                throw new \RuntimeException('Saldo tidak cukup.');
            }
            $wallet->saldo -= $amount;
            $wallet->save();

            $this->setRawAttributes($wallet->getAttributes(), true);

            return WalletTransaction::create([
                'wallet_id'    => $wallet->id,
                'transaksi_id' => $transaksiId,
                'tipe'         => 'debit',
                'amount'       => $amount,
                'saldo_after'  => $wallet->saldo,
                'description'  => $description,
            ]);
        });
    }
}
