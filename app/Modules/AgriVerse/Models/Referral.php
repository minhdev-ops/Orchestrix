<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id', 'referred_user_id', 'affiliate_code',
        'status', 'first_order_id', 'commission_earned',
        'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'commission_earned' => 'decimal:2',
        ];
    }

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function referredUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'referred_user_id');
    }

    public function firstOrder()
    {
        return $this->belongsTo(Order::class, 'first_order_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
