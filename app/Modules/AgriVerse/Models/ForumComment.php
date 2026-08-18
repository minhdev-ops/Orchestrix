<?php

namespace App\Modules\AgriVerse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'parent_id', 'content'];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumComment::class, 'parent_id')->with('user:id,name,avatar');
    }
}
