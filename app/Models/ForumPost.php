<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $table = 'forum_posts';

    protected $fillable = ['user_id', 'content', 'helpful_count', 'is_hidden'];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(Pengguna::class, 'user_id');
    }

    public function helpfuls()
    {
        return $this->hasMany(ForumPostHelpful::class, 'post_id');
    }
}
