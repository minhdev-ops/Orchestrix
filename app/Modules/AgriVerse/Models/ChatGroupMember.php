<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class ChatGroupMember extends Model
{
    public $timestamps = false;

    protected $fillable = ['group_id', 'user_id', 'status', 'joined_at'];

    protected $casts = [
        'joined_at' => 'datetime',
    ];
}
