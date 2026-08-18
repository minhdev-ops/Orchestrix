<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Models\User;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController
{
    public function index(Request $request)
    {
        $storesCount = Store::count();
        $productsCount = Product::count();
        $ordersCount = Order::count();
        $usersCount = User::count();

        $totalRevenue = Transaction::where('payment_status', 'paid')->sum('amount');
        $totalCommission = Transaction::where('payment_status', 'paid')->sum('commission_fee');
        $pendingOrders = Order::whereIn('status', ['pending', 'confirmed'])->count();

        $revenueByMonth = Transaction::where('payment_status', 'paid')
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        $topProducts = Product::withCount(['orders as total_sold' => function ($q) {
            $q->whereIn('status', ['completed', 'delivered']);
        }])->orderByDesc('total_sold')->limit(10)->get();

        $topStores = Store::withCount(['products'])
            ->withCount(['products as total_revenue' => function ($q) {
                $q->whereHas('orders', fn ($o) => $o->whereIn('status', ['completed', 'delivered']));
            }])
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Revenue data for chart
        $monthlyRevenue = Transaction::where('payment_status', 'paid')
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as month, SUM(amount) as total, SUM(commission_fee) as commission, SUM(seller_amount) as payout')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->limit(12)
            ->get();

        $sellerPayouts = Transaction::where('payment_status', 'paid')
            ->sum('seller_amount');

        // Top sellers by revenue
        $topSellers = User::whereHas('stores')
            ->withCount(['stores'])
            ->withSum(['ordersAsSeller as seller_revenue' => fn ($q) => $q->whereIn('status', ['completed', 'delivered'])], 'total_amount')
            ->orderByDesc('seller_revenue')
            ->limit(10)
            ->get();

        $tab = $request->get('tab', 'overview');

        return Inertia::render('Admin/Reports/Index', [
            'storesCount' => $storesCount,
            'productsCount' => $productsCount,
            'ordersCount' => $ordersCount,
            'usersCount' => $usersCount,
            'totalRevenue' => $totalRevenue,
            'totalCommission' => $totalCommission,
            'pendingOrders' => $pendingOrders,
            'revenueByMonth' => $revenueByMonth,
            'topProducts' => $topProducts,
            'topStores' => $topStores,
            'monthlyRevenue' => $monthlyRevenue,
            'sellerPayouts' => $sellerPayouts,
            'topSellers' => $topSellers,
            'tab' => $tab,
        ]);
    }
}
