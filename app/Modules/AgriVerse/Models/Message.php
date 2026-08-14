<?php

namespace App\Modules\AgriVerse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['order_id', 'sender_id', 'message'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
