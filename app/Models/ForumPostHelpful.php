<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPostHelpful extends Model
{
    protected $table = 'forum_post_helpfuls';

    protected $fillable = ['post_id', 'user_id'];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(Pengguna::class, 'user_id');
    }
}
