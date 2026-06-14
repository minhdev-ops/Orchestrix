<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\ThreeDAsset;
use App\Modules\AgriVerse\Models\StoreSubscription;
use Illuminate\Support\Facades\DB;

class SellerDashboardController
{
    public function stats(Request $request): JsonResource
    {
        $user = $request->user();
        $store = Store::where('owner_id', $user->id)->first();

        if (!$store) {
            abort(404, 'You have no store. Create one first.');
        }

        // Revenue stats
        $totalRevenue = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalCommission = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->sum('commission_fee');

        $totalOrders = Order::where('seller_id', $user->id)->count();
        $pendingOrders = Order::where('seller_id', $user->id)->where('status', 'pending')->count();

        // Monthly revenue
        $monthlyRevenue = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        // Products stats
        $totalProducts = Product::where('user_id', $user->id)->count();
        $publishedProducts = Product::where('user_id', $user->id)->where('status', 'published')->count();

        // 3D assets stats
        $totalAssets = ThreeDAsset::where('user_id', $user->id)->count();
        $optimizedAssets = ThreeDAsset::where('user_id', $user->id)
            ->where('compression_status', 'completed')
            ->count();

        // Subscription info
        $subscription = StoreSubscription::with('plan')
            ->where('store_id', $store->id)
            ->latest()
            ->first();

        // Storage usage (estimated from file sizes)
        $storageUsed = ThreeDAsset::where('user_id', $user->id)
            ->sum(DB::raw('COALESCE(file_size, 0) + COALESCE(compressed_file_size, 0)'));

        return JsonResource::make([
            'store' => $store->only(['id', 'name', 'status']),
            'revenue' => [
                'total' => $totalRevenue,
                'monthly' => $monthlyRevenue,
                'commission' => $totalCommission,
            ],
            'orders' => [
                'total' => $totalOrders,
                'pending' => $pendingOrders,
            ],
            'products' => [
                'total' => $totalProducts,
                'published' => $publishedProducts,
            ],
            'assets' => [
                'total' => $totalAssets,
                'optimized' => $optimizedAssets,
                'storage_used_bytes' => $storageUsed,
            ],
            'subscription' => $subscription ? [
                'plan' => $subscription->plan->name ?? 'N/A',
                'start_date' => $subscription->start_date,
                'end_date' => $subscription->end_date,
                'status' => $subscription->status ?? 'active',
            ] : null,
        ]);
    }
}
