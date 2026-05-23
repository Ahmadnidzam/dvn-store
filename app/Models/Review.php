<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'platform_id', 'user_id', 'rating', 'komentar', 'helpful_count',
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }

    public function user()
    {
        return $this->belongsTo(Pengguna::class, 'user_id');
    }

    public function helpfuls()
    {
        return $this->hasMany(ReviewHelpful::class, 'review_id');
    }
}
