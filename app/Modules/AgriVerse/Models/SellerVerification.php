<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class SellerVerification extends Model
{
    protected $fillable = [
        'user_id', 'id_card_number', 'id_card_image_front', 'id_card_image_back',
        'portrait_image', 'phone', 'email', 'email_verification_code', 'email_verified_at',
        'status', 'reject_reason', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'email_verified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
