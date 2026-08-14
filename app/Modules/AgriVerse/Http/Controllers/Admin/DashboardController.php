<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Models\User;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController
{
    public function index(Request $request)
    {
        $storesCount = Store::count();
        $productsCount = Product::count();
        $usersCount = User::count();
        $ordersCount = Order::count();

        $revenueThisMonth = Transaction::where('payment_status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $recentOrders = Order::with(['product', 'buyer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $topProducts = Product::withCount(['orders as total_sold' => function ($q) {
            $q->whereIn('status', ['completed', 'delivered']);
        }])->orderByDesc('total_sold')->limit(5)->get();

        $revenueChart = Transaction::where('payment_status', 'paid')
            ->where('paid_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d/m');
            $chartData[] = (float) ($revenueChart[$date]->total ?? 0);
        }

        $ordersByStatus = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        return Inertia::render('Admin/Dashboard', [
            'storesCount' => $storesCount,
            'productsCount' => $productsCount,
            'usersCount' => $usersCount,
            'ordersCount' => $ordersCount,
            'revenueThisMonth' => $revenueThisMonth,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'ordersByStatus' => $ordersByStatus,
        ]);
    }
}
