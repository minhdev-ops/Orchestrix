<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\DigitalPassportLog;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SellerOrderController
{
    public function index()
    {
        $user = auth()->user();

        $orders = Order::with(['product', 'buyer', 'store'])
            ->where('seller_id', $user->id)
            ->latest()
            ->paginate(15);

        return Inertia::render('Marketplace/Seller/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        if ($order->seller_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['product', 'buyer', 'store', 'statuses.user', 'transaction', 'contract', 'refunds']);

        return Inertia::render('Marketplace/Seller/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function confirm(Order $order)
    {
        if ($order->seller_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Đơn hàng không thể xác nhận ở trạng thái hiện tại.');
        }

        $order->update(['status' => 'confirmed']);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'confirmed',
            'note' => 'Người bán đã xác nhận đơn hàng.',
            'user_id' => auth()->id(),
        ]);

        DigitalPassportLog::create([
            'product_id' => $order->product_id,
            'action' => 'order_confirmed',
            'data' => ['order_id' => $order->id],
            'performed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Đã xác nhận đơn hàng.');
    }

    public function ship(Order $order)
    {
        if ($order->seller_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'confirmed') {
            return back()->with('error', 'Đơn hàng chưa được xác nhận hoặc đã được giao.');
        }

        $order->update(['status' => 'shipping']);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'shipping',
            'note' => 'Đơn hàng đang được giao.',
            'user_id' => auth()->id(),
        ]);

        DigitalPassportLog::create([
            'product_id' => $order->product_id,
            'action' => 'order_shipped',
            'data' => ['order_id' => $order->id],
            'performed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Đã chuyển trạng thái sang đang giao.');
    }

    public function deliver(Order $order)
    {
        if ($order->seller_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'shipping') {
            return back()->with('error', 'Đơn hàng chưa được giao hoặc đã được giao trước đó.');
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'delivered',
            'note' => 'Đơn hàng đã được giao thành công.',
            'user_id' => auth()->id(),
        ]);

        DigitalPassportLog::create([
            'product_id' => $order->product_id,
            'action' => 'order_delivered',
            'data' => ['order_id' => $order->id],
            'performed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Đã xác nhận giao hàng thành công.');
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->seller_id !== auth()->id()) {
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
            'note' => 'Người bán hủy: '.$request->reason,
            'user_id' => auth()->id(),
        ]);

        DigitalPassportLog::create([
            'product_id' => $order->product_id,
            'action' => 'order_cancelled',
            'data' => ['order_id' => $order->id, 'reason' => $request->reason],
            'performed_by' => auth()->id(),
        ]);

        $order->product?->increment('stock', $order->quantity);

        return back()->with('success', 'Đã hủy đơn hàng.');
    }
}
