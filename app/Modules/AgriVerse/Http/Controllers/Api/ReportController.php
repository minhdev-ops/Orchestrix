<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportController
{
    public function sellerRevenue(Request $request)
    {
        $user = $request->user();

        $totalRevenue = $user->ordersAsSeller()
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalCommission = $user->ordersAsSeller()
            ->where('status', 'completed')
            ->sum('commission_fee');

        $totalOrders = $user->ordersAsSeller()
            ->where('status', 'completed')
            ->count();

        $monthlyRevenue = $user->ordersAsSeller()
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        $recentOrders = $user->ordersAsSeller()
            ->with('product')
            ->latest()
            ->take(5)
            ->get();

        return JsonResource::make([
            'total_revenue' => $totalRevenue,
            'total_commission' => $totalCommission,
            'total_orders' => $totalOrders,
            'monthly_revenue' => $monthlyRevenue,
            'recent_orders' => $recentOrders,
        ]);
    }

    public function buyerStats(Request $request)
    {
        $user = $request->user();

        $totalSpent = $user->ordersAsBuyer()
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalOrders = $user->ordersAsBuyer()->count();
        $pendingOrders = $user->ordersAsBuyer()->where('status', 'pending')->count();

        return JsonResource::make([
            'total_spent' => $totalSpent,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
        ]);
    }
}
