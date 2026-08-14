<?php

namespace App\Modules\AgriVerse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'product_id', 'buyer_id', 'seller_id', 'store_id',
        'coupon_id', 'quantity', 'unit_price', 'total_price',
        'discount_amount', 'total_amount',
        'commission_fee', 'status', 'shipping_address',
        'shipping_method', 'shipping_fee', 'tracking_number', 'tracking_url',
        'estimated_delivery', 'delivered_at',
        'notes', 'cancel_reason', 'cancelled_at', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'commission_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'estimated_delivery' => 'date',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class, 'order_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function statuses()
    {
        return $this->hasMany(OrderStatus::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
