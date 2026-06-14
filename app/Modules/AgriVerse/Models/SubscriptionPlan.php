<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'limit_3d_models', 'price_per_month', 'features', 'status',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'price_per_month' => 'decimal:2',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($plan) {
            if (empty($plan->uuid)) {
                $plan->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function storeSubscriptions()
    {
        return $this->hasMany(StoreSubscription::class, 'plan_id');
    }
}
