<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationBooking extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'topic',
        'preferred_date',
        'preferred_time',
        'message',
        'status',
        'reference',
    ];

    protected $casts = [
        'preferred_date' => 'date',
    ];
}
