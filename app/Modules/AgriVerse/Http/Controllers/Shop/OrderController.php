<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\DigitalPassportLog;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Models\Refund;
use App\Modules\AgriVerse\Models\Transaction;
use App\Modules\AgriVerse\Services\GHNService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController
{
    public function index()
    {
        $orders = Order::with(['product', 'store'])
            ->where('buyer_id', auth()->id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Marketplace/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {

        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['product.passportLogs.performer', 'store', 'statuses.user', 'contract', 'transaction', 'refunds']);

        return Inertia::render('Marketplace/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function cancel(Request $request, Order $order)
    {

        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        if (! in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Đơn hàng không thể hủy ở trạng thái hiện tại.');
        }

        $request->validate(['reason' => 'required|string|max:500']);

        $order->update([
            'status' => 'cancelled',
            'cancel_reason' => $request->reason,
            'cancelled_at' => now(),
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'note' => 'Khách hủy: '.$request->reason,
            'user_id' => auth()->id(),
        ]);

        DigitalPassportLog::create([
            'product_id' => $order->product_id,
            'action' => 'order_cancelled',
            'data' => ['order_id' => $order->id, 'reason' => $request->reason],
            'performed_by' => auth()->id(),
        ]);

        // Restore stock
        $order->product?->increment('stock', $order->quantity);

        return redirect()->route('agriverse.shop.orders.show', $order->id)
            ->with('success', 'Đơn hàng đã được hủy.');
    }

    public function requestRefund(Request $request, Order $order)
    {

        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        if (! in_array($order->status, ['delivered', 'completed'])) {
            return back()->with('error', 'Chỉ có thể yêu cầu hoàn tiền cho đơn đã giao.');
        }

        $data = $request->validate([
            'reason' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
        ]);

        $existing = Refund::where('order_id', $order->id)->whereIn('status', ['pending', 'approved'])->first();
        if ($existing) {
            return back()->with('error', 'Đã có yêu cầu hoàn tiền cho đơn hàng này.');
        }

        Refund::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'amount' => $order->total_amount,
            'reason' => $data['reason'],
            'description' => $data['description'] ?? null,
            'status' => 'pending',
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'note' => 'Yêu cầu hoàn tiền: '.$data['reason'],
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Yêu cầu hoàn tiền đã được gửi.');
    }

    public function confirmReceived(Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'delivered') {
            return back()->with('error', 'Đơn hàng chưa được giao hoặc đã được xác nhận.');
        }

        $order->update([
            'status' => 'completed',
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'completed',
            'note' => 'Người mua đã xác nhận nhận hàng.',
            'user_id' => auth()->id(),
        ]);

        if (! $order->transaction()->where('payment_status', 'paid')->exists()) {
            Transaction::create([
                'order_id' => $order->id,
                'user_id' => $order->seller_id,
                'amount' => $order->total_amount,
                'commission_fee' => $order->commission_fee,
                'seller_amount' => $order->total_amount - $order->commission_fee,
                'payment_method' => 'offline',
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);
        }

        DigitalPassportLog::create([
            'product_id' => $order->product_id,
            'action' => 'order_completed',
            'data' => ['order_id' => $order->id],
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('agriverse.shop.orders.show', $order->id)
            ->with('success', 'Cảm ơn bạn đã xác nhận nhận hàng!');
    }

    public function tracking()
    {
        $recentOrders = [];
        if (auth()->check()) {
            $recentOrders = Order::with('product')
                ->where('buyer_id', auth()->id())
                ->latest()
                ->limit(5)
                ->get()
                ->toArray();
        }

        return Inertia::render('Marketplace/Tracking/Index', [
            'recentOrders' => $recentOrders,
        ]);
    }

    public function lookupTracking(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:100',
        ]);

        $order = Order::with(['product', 'store'])
            ->where(function ($q) use ($request) {
                $q->where('tracking_number', $request->code)
                    ->orWhere('uuid', $request->code)
                    ->orWhere('id', is_numeric($request->code) ? $request->code : 0);
            })
            ->where('buyer_id', auth()->id())
            ->first();

        if (! $order) {
            return response()->json(['error' => 'Không tìm thấy đơn hàng.'], 404);
        }

        $ghnTracking = [];
        if ($order->tracking_number) {
            try {
                $ghnTracking = app(GHNService::class)->trackOrder($order->tracking_number);
            } catch (\Exception) {
                $ghnTracking = [];
            }
        }

        return response()->json([
            'order' => $order,
            'ghn_tracking' => $ghnTracking,
        ]);
    }
}
