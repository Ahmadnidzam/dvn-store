<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $table = 'withdraws';

    protected $fillable = [
        'dev_id', 'amount', 'bank_snapshot',
        'iris_payout_reference_no', 'status',
        'failure_reason', 'processed_at', 'iris_response',
    ];

    protected $casts = [
        'bank_snapshot' => 'array',
        'iris_response' => 'array',
        'processed_at'  => 'datetime',
    ];

    public function developer()
    {
        return $this->belongsTo(Pengguna::class, 'dev_id');
    }
}
