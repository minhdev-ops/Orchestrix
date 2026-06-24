<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id', 'gateway_transaction_id', 'order_id', 'user_id',
        'amount', 'commission_fee', 'seller_amount',
        'payment_method', 'payment_status',
        'payment_proof', 'gateway_response', 'notes', 'failure_reason', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'commission_fee' => 'decimal:2',
            'seller_amount' => 'decimal:2',
            'gateway_response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
