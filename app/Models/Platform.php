<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $table = 'platforms';

    protected $fillable = [
        'dev_id',
        'category',
        'nama_platform',
        'slug',
        'genre',
        'harga',
        'rating',
        'icon',
        'deskripsi',
        'cuplikan',
        'file_path',
        'file_size',
        'scan_status',
        'scan_result',
        'is_published',
        'is_taken_down',
        'taken_down_at',
        'taken_down_reason',
        'upload_fee_transaksi_id',
        // Fields untuk fitur Update File (safe replace in-place)
        'pending_file_path',
        'pending_file_size',
        'pending_scan_status',
        'pending_scan_result',
        'pending_uploaded_at',
        'file_updated_at',
    ];

    protected $casts = [
        'scan_result'         => 'array',
        'is_published'        => 'boolean',
        'is_taken_down'       => 'boolean',
        'taken_down_at'       => 'datetime',
        'pending_scan_result' => 'array',
        'pending_uploaded_at' => 'datetime',
        'file_updated_at'     => 'datetime',
    ];

    /**
     * Apakah platform punya pending update yang sedang/akan di-scan.
     */
    public function hasPendingUpdate(): bool
    {
        return !empty($this->pending_file_path);
    }

    // Visible to public catalog ⇔ published + clean scan + tidak takedown +
    // developer-nya tidak di-block oleh admin
    public function scopeAvailable($query)
    {
        return $query->where('is_published', true)
                     ->where('is_taken_down', false)
                     ->where('scan_status', 'clean')
                     ->whereHas('developer', function ($q) {
                         $q->where('status', 'active');
                     });
    }

    // ---------------- Relations ----------------
    public function developer()
    {
        return $this->belongsTo(Pengguna::class, 'dev_id');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'platform_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'platform_id');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class, 'platform_id');
    }

    public function uploadFeeTransaksi()
    {
        return $this->belongsTo(Transaksi::class, 'upload_fee_transaksi_id');
    }
}
