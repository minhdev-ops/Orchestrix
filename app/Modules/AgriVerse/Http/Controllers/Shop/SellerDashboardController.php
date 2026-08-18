<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use Inertia\Inertia;

class SellerDashboardController
{
    public function index()
    {
        $user = auth()->user();
        $store = Store::where('owner_id', $user->id)->firstOrFail();

        $totalProducts = Product::where('store_id', $store->id)->count();
        $totalOrders = Order::where('seller_id', $user->id)->count();
        $pendingOrders = Order::where('seller_id', $user->id)->where('status', 'pending')->count();
        $revenue = Order::where('seller_id', $user->id)->where('status', 'completed')->sum('total_amount');
        $revenueThisMonth = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        $recentOrders = Order::with(['product', 'buyer'])
            ->where('seller_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $lowStockProducts = Product::where('store_id', $store->id)
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->get();

        $topProducts = Product::where('store_id', $store->id)
            ->withCount(['orders as sold_count' => fn ($q) => $q->whereIn('status', ['completed', 'delivered'])])
            ->orderByDesc('sold_count')
            ->take(5)
            ->get();

        return Inertia::render('Marketplace/Seller/Dashboard', [
            'store' => $store->only(['id', 'name', 'logo', 'description']),
            'stats' => [
                'total_products' => $totalProducts,
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'revenue' => $revenue,
                'revenue_this_month' => $revenueThisMonth,
            ],
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'topProducts' => $topProducts,
        ]);
    }
}
