<?php

namespace App\Modules\AgriVerse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    protected $fillable = ['buyer_id', 'seller_id', 'product_id', 'last_message', 'last_message_at', 'last_sender_id', 'buyer_unread', 'seller_unread'];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function lastSender()
    {
        return $this->belongsTo(User::class, 'last_sender_id');
    }
}
