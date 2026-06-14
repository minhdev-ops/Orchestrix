<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    public function posts()
    {
        return $this->hasMany(ForumPost::class, 'category_id');
    }

    public function approvedPosts()
    {
        return $this->hasMany(ForumPost::class, 'category_id')->where('status', 'approved');
    }
}
