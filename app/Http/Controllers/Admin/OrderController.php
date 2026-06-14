<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Models\Transaction;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['product', 'buyer', 'seller', 'store']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('product', fn($p) => $p->where('name', 'like', "%{$request->search}%"))
                  ->orWhereHas('buyer', fn($u) => $u->where('name', 'like', "%{$request->search}%"))
                  ->orWhere('uuid', 'like', "%{$request->search}%");
            });
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['product', 'buyer', 'seller', 'store', 'contract', 'transaction', 'statuses.user']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,delivered,completed,cancelled',
            'note' => 'nullable|string|max:500',
        ]);

        $order->update(['status' => $request->status]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => $request->status,
            'note' => $request->note ?? "Status updated to {$request->status}",
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Order status updated.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }
}
