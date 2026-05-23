<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $table = 'downloads';

    protected $fillable = ['user_id', 'platform_id', 'transaksi_id'];

    public function user()
    {
        return $this->belongsTo(Pengguna::class, 'user_id');
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}
