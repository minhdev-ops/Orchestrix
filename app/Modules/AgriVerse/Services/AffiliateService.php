<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Modules\AgriVerse\Models\Affiliate;
use App\Modules\AgriVerse\Models\Referral;
use App\Modules\AgriVerse\Models\Commission;
use App\Modules\AgriVerse\Models\Order;

class AffiliateService
{
    /**
     * Register user as affiliate
     */
    public function register(User $user, array $data = []): Affiliate
    {
        $existing = Affiliate::where('user_id', $user->id)->first();
        if ($existing) {
            throw new \Exception('Bạn đã đăng ký affiliate rồi.');
        }

        return Affiliate::create([
            'user_id' => $user->id,
            'affiliate_code' => Affiliate::generateCode(),
            'commission_rate' => $data['commission_rate'] ?? 5.00,
            'status' => 'active',
            'payout_method' => $data['payout_method'] ?? null,
            'payout_info' => $data['payout_info'] ?? null,
        ]);
    }

    /**
     * Track referral from link
     */
    public function trackReferral(string $affiliateCode, User $referredUser): ?Referral
    {
        $affiliate = Affiliate::where('affiliate_code', $affiliateCode)
            ->active()
            ->first();

        if (!$affiliate) {
            return null;
        }

        // Can't refer yourself
        if ($affiliate->user_id === $referredUser->id) {
            return null;
        }

        // Check if already referred
        $existing = Referral::where('referred_user_id', $referredUser->id)->first();
        if ($existing) {
            return null;
        }

        $referral = Referral::create([
            'affiliate_id' => $affiliate->id,
            'referred_user_id' => $referredUser->id,
            'affiliate_code' => $affiliateCode,
            'status' => 'pending',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $affiliate->increment('total_referrals');

        return $referral;
    }

    /**
     * Process commission after order completion
     */
    public function processCommission(Order $order): ?Commission
    {
        $referral = Referral::where('referred_user_id', $order->buyer_id)
            ->where('status', 'pending')
            ->first();

        if (!$referral) {
            return null;
        }

        $affiliate = $referral->affiliate;
        if (!$affiliate || $affiliate->status !== 'active') {
            return null;
        }

        $commissionAmount = round($order->total_amount * ($affiliate->commission_rate / 100), 2);

        $commission = Commission::create([
            'affiliate_id' => $affiliate->id,
            'order_id' => $order->id,
            'referred_user_id' => $order->buyer_id,
            'order_amount' => $order->total_amount,
            'commission_rate' => $affiliate->commission_rate,
            'commission_amount' => $commissionAmount,
            'status' => 'pending',
        ]);

        // Update referral
        $referral->update([
            'status' => 'completed',
            'first_order_id' => $order->id,
            'commission_earned' => $commissionAmount,
        ]);

        // Update affiliate earnings
        $affiliate->increment('total_earnings', $commissionAmount);

        return $commission;
    }

    /**
     * Get affiliate dashboard stats
     */
    public function getDashboard(Affiliate $affiliate): array
    {
        $pendingCommission = Commission::where('affiliate_id', $affiliate->id)
            ->where('status', 'pending')
            ->sum('commission_amount');

        $paidCommission = Commission::where('affiliate_id', $affiliate->id)
            ->where('status', 'paid')
            ->sum('commission_amount');

        $recentReferrals = Referral::where('affiliate_id', $affiliate->id)
            ->with('referredUser')
            ->latest()
            ->limit(10)
            ->get();

        $recentCommissions = Commission::where('affiliate_id', $affiliate->id)
            ->with('order')
            ->latest()
            ->limit(10)
            ->get();

        return [
            'affiliate' => $affiliate,
            'total_earnings' => $affiliate->total_earnings,
            'pending_commission' => $pendingCommission,
            'paid_commission' => $paidCommission,
            'total_referrals' => $affiliate->total_referrals,
            'conversion_rate' => $affiliate->conversion_rate,
            'referral_link' => $affiliate->referral_link,
            'recent_referrals' => $recentReferrals,
            'recent_commissions' => $recentCommissions,
        ];
    }

    /**
     * Approve commission
     */
    public function approveCommission(Commission $commission): Commission
    {
        $commission->update(['status' => 'approved']);
        return $commission->fresh();
    }

    /**
     * Pay commission
     */
    public function payCommission(Commission $commission): Commission
    {
        $commission->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
        return $commission->fresh();
    }

    /**
     * Get affiliate stats for admin
     */
    public function getAdminStats(): array
    {
        return [
            'total_affiliates' => Affiliate::count(),
            'active_affiliates' => Affiliate::active()->count(),
            'total_referrals' => Referral::count(),
            'completed_referrals' => Referral::completed()->count(),
            'total_commissions' => Commission::sum('commission_amount'),
            'pending_commissions' => Commission::pending()->sum('commission_amount'),
            'paid_commissions' => Commission::paid()->sum('commission_amount'),
        ];
    }

    /**
     * Get top affiliates
     */
    public function getTopAffiliates(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Affiliate::with('user')
            ->active()
            ->orderByDesc('total_earnings')
            ->limit($limit)
            ->get();
    }
}
