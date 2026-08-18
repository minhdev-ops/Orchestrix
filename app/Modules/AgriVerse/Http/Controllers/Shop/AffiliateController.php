<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Modules\AgriVerse\Models\Affiliate;
use App\Modules\AgriVerse\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AffiliateController extends Controller
{
    protected AffiliateService $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Show affiliate dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $affiliate = Affiliate::where('user_id', $user->id)->first();

        if (! $affiliate) {
            return Inertia::render('Marketplace/Affiliate/Register');
        }

        $dashboard = $this->affiliateService->getDashboard($affiliate);

        return Inertia::render('Marketplace/Affiliate/Dashboard', $dashboard);
    }

    /**
     * Register as affiliate
     */
    public function register(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'payout_method' => 'nullable|string|in:banking,momo',
            'bank_name' => 'required_if:payout_method,banking',
            'account_number' => 'required_if:payout_method,banking',
            'account_name' => 'required_if:payout_method,banking',
        ]);

        try {
            $affiliate = $this->affiliateService->register($user, [
                'payout_method' => $request->payout_method,
                'payout_info' => [
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name,
                ],
            ]);

            return redirect()->route('agriverse.shop.affiliate.index')
                ->with('success', 'Đăng ký affiliate thành công!');
        } catch (\Exception $e) {
            Log::error('Affiliate error: '.$e->getMessage());
            return back()->withErrors(['error' => 'Đã xảy ra lỗi. Vui lòng thử lại sau.']);
        }
    }

    /**
     * Get affiliate link
     */
    public function getLink(Request $request)
    {
        $affiliate = Affiliate::where('user_id', $request->user()->id)->first();

        if (! $affiliate) {
            return response()->json(['message' => 'Chưa đăng ký affiliate'], 404);
        }

        return response()->json([
            'link' => $affiliate->referral_link,
            'code' => $affiliate->affiliate_code,
        ]);
    }

    /**
     * Get referral stats
     */
    public function stats(Request $request)
    {
        $affiliate = Affiliate::where('user_id', $request->user()->id)->first();

        if (! $affiliate) {
            return response()->json(['message' => 'Chưa đăng ký affiliate'], 404);
        }

        return response()->json($this->affiliateService->getDashboard($affiliate));
    }
}
