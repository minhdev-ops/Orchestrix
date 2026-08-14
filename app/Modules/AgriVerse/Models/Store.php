<?php

namespace App\Modules\AgriVerse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'owner_id', 'name', 'description', 'logo', 'status', 'metadata',
        'bank_name', 'bank_account_name', 'bank_account_number', 'phone', 'address',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($store) {
            if (empty($store->uuid)) {
                $store->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(StoreSubscription::class, 'store_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(StoreSubscription::class, 'store_id')
            ->where('payment_status', 'paid')
            ->where('end_date', '>', now())
            ->latest();
    }

    public function scanningJobs()
    {
        return $this->hasMany(AiScanningJob::class, 'store_id');
    }
}
