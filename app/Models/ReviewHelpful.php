<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewHelpful extends Model
{
    protected $table = 'review_helpfuls';

    protected $fillable = ['review_id', 'user_id'];

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id');
    }

    public function user()
    {
        return $this->belongsTo(Pengguna::class, 'user_id');
    }
}
