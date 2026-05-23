<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'penggunas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'blocked_at',
        'blocked_reason',
        'kode_unik',
        'avatar',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'blocked_at'        => 'datetime',
        'password'          => 'hashed',
    ];

    // ---------------- Role helpers ----------------
    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isDeveloper(): bool { return $this->role === 'developer'; }
    public function isUser(): bool      { return $this->role === 'user'; }
    public function isBlocked(): bool   { return $this->status === 'blocked'; }

    // ---------------- Relations ----------------
    public function developerProfile()
    {
        return $this->hasOne(DeveloperProfile::class, 'pengguna_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'dev_id');
    }

    public function platforms()
    {
        return $this->hasMany(Platform::class, 'dev_id');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class, 'user_id');
    }

    public function reviewHelpfuls()
    {
        return $this->hasMany(ReviewHelpful::class, 'user_id');
    }

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class, 'dev_id');
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class, 'user_id');
    }
}
