<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeveloperProfile extends Model
{
    protected $table = 'developer_profiles';

    protected $fillable = [
        'pengguna_id',
        'nama_studio',
        'deskripsi',
        'website',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }
}
