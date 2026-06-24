<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'affiliate_code', 'commission_rate', 'status',
        'total_earnings', 'total_referrals', 'payout_method',
        'payout_info', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'total_earnings' => 'decimal:2',
            'total_referrals' => 'integer',
            'payout_info' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(uniqid('AV'));
        } while (static::where('affiliate_code', $code)->exists());

        return $code;
    }

    public function getReferralLinkAttribute(): string
    {
        return route('agriverse.shop.home', ['ref' => $this->affiliate_code]);
    }

    public function getConversionRateAttribute(): float
    {
        if ($this->total_referrals === 0) return 0;
        $completed = $this->referrals()->where('status', 'completed')->count();
        return round(($completed / $this->total_referrals) * 100, 2);
    }
}
