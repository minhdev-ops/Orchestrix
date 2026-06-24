<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'session_id', 'event_type', 'event_name',
        'properties', 'page_url', 'referrer_url',
        'ip_address', 'user_agent', 'device_type', 'browser',
        'country', 'city',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function scopePageView($query)
    {
        return $query->where('event_type', 'page_view');
    }

    public function scopeProductView($query)
    {
        return $query->where('event_type', 'product_view');
    }

    public function scopePurchase($query)
    {
        return $query->where('event_type', 'purchase');
    }

    public function scopeCart($query)
    {
        return $query->where('event_type', 'add_to_cart');
    }

    public function scopeSearch($query)
    {
        return $query->where('event_type', 'search');
    }

    public function scopeDateRange($query, string $start, string $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
