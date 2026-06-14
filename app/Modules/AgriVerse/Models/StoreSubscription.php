<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class StoreSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'store_id', 'plan_id', 'start_date', 'end_date',
        'payment_status', 'amount_paid', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'amount_paid' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($sub) {
            if (empty($sub->uuid)) {
                $sub->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}
