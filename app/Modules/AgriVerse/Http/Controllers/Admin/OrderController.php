<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Models\Product;
use App\Models\User;

class OrderController
{
    public function index(Request $request)
    {
        $query = Order::with(['product', 'buyer', 'seller', 'store']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('uuid', 'like', "%{$request->search}%")
                  ->orWhereHas('buyer', fn($b) => $b->where('name', 'like', "%{$request->search}%"));
            });
        }

        $orders = $query->latest()->paginate(15);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['product', 'buyer', 'seller', 'store', 'contract', 'transaction', 'statuses', 'refunds']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function create()
    {
        $products = Product::published()->with('store')->get();
        $buyers = User::whereIn('role', ['buyer', 'admin'])->get();

        return Inertia::render('Admin/Orders/Create', [
            'products' => $products,
            'buyers' => $buyers,
        ]);
    }

    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'buyer_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($data['product_id']);

        $totalPrice = $product->price * $data['quantity'];
        $commissionFee = round($totalPrice * 0.05, 2);

        $order = Order::create([
            'product_id' => $product->id,
            'buyer_id' => $data['buyer_id'],
            'seller_id' => $product->user_id,
            'store_id' => $product->store_id,
            'quantity' => $data['quantity'],
            'unit_price' => $product->price,
            'total_price' => $totalPrice,
            'total_amount' => $totalPrice,
            'commission_fee' => $commissionFee,
            'status' => 'confirmed',
            'shipping_address' => $data['shipping_address'],
            'notes' => $data['notes'] ?? null,
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'confirmed',
            'note' => 'Đơn hàng được tạo bởi admin.',
            'user_id' => auth()->id(),
        ]);

        $product->decrement('stock', $data['quantity']);

        return redirect()->route('admin.agriverse.orders.show', $order)
            ->with('success', 'Đơn hàng đã được tạo.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,shipping,delivered,completed,cancelled,refunded',
            'note' => 'nullable|string|max:500',
        ]);

        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'shipping') {
            $order->update(['estimated_delivery' => now()->addDays(3)]);
        }
        if ($validated['status'] === 'delivered') {
            $order->update(['delivered_at' => now()]);
        }
        if ($validated['status'] === 'cancelled') {
            $order->product?->increment('stock', $order->quantity);
        }

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.agriverse.orders.show', $order)
            ->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.agriverse.orders.index')
            ->with('success', 'Đơn hàng đã được xóa.');
    }
}
