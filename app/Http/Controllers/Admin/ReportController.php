<?php

/**
 * @deprecated Use App\Modules\AgriVerse\Http\Controllers\Admin\ReportController instead.
 * This controller uses Blade views; the module version uses Inertia SPA.
 * Kept for backward compatibility. Will be removed in next major version.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function revenue(Request $request)
    {
        $period = $request->period ?? 'month';

        $query = Transaction::where('status', 'completed');

        $groupFormat = match ($period) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'year' => '%Y',
            default => '%Y-%m',
        };

        $revenueData = $query->selectRaw("DATE_FORMAT(created_at, ?) as period", [$groupFormat])
            ->selectRaw('SUM(amount) as total')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit(12)
            ->get();

        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
        $totalCommission = Order::where('status', 'completed')->sum('commission_fee');
        $totalOrders = Order::count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return view('admin.reports.revenue', compact(
            'revenueData', 'totalRevenue', 'totalCommission',
            'totalOrders', 'avgOrderValue', 'period'
        ));
    }

    public function commission(Request $request)
    {
        $commissionBySeller = User::whereHas('roles', function ($q) {
            $q->where('name', 'seller');
        })
            ->withCount(['orders as total_orders' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['orders as total_commission' => function ($q) {
                $q->where('status', 'completed');
            }], 'commission_fee')
            ->withSum(['orders as total_revenue' => function ($q) {
                $q->where('status', 'completed');
            }], 'total_amount')
            ->get();

        $totalCommission = $commissionBySeller->sum('total_commission');

        return view('admin.reports.commission', compact('commissionBySeller', 'totalCommission'));
    }

    public function sellers()
    {
        $sellers = User::whereHas('roles', function ($q) {
            $q->where('name', 'seller');
        })
            ->withCount(['products', 'stores'])
            ->withSum(['orders as total_revenue' => function ($q) {
                $q->where('status', 'completed');
            }], 'total_amount')
            ->withCount(['orders as completed_orders' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->get();

        $topSellers = $sellers->sortByDesc('total_revenue')->take(5);

        return view('admin.reports.sellers', compact('sellers', 'topSellers'));
    }
}
