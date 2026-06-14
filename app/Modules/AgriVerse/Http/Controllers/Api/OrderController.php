<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Models\Coupon;
use App\Modules\AgriVerse\Http\Resources\OrderResource;
use App\Modules\AgriVerse\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $query = Order::query()->with(['product', 'buyer', 'seller', 'store']);

        if ($user->hasRole('buyer')) {
            $query->where('buyer_id', $user->id);
        } elseif ($user->hasRole('seller')) {
            $query->where('seller_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return OrderResource::collection($query->latest()->paginate($request->per_page ?? 15));
    }

    public function show(Order $order): OrderResource
    {
        $this->authorizeView($order);

        return OrderResource::make($order->load(['product', 'buyer', 'seller', 'store', 'contract']));
    }

    public function store(StoreOrderRequest $request): OrderResource
    {
        $totalPrice = $request->quantity * $request->unit_price;

        // Apply coupon if provided
        $discountAmount = 0;
        $couponId = null;
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid() && $totalPrice >= $coupon->min_order_amount) {
                $discountAmount = $coupon->calculateDiscount($totalPrice);
                $couponId = $coupon->id;
                $coupon->increment('used_count');
            }
        }

        $afterDiscount = $totalPrice - $discountAmount;
        $commissionFee = round($afterDiscount * 0.05, 2);

        $order = Order::create([
            'product_id' => $request->product_id,
            'buyer_id' => $request->user()->id,
            'seller_id' => $request->seller_id,
            'store_id' => $request->store_id,
            'coupon_id' => $couponId,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_price' => $totalPrice,
            'discount_amount' => $discountAmount,
            'total_amount' => $afterDiscount,
            'commission_fee' => $commissionFee,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'notes' => $request->notes,
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'note' => 'Order created',
            'user_id' => $request->user()->id,
        ]);

        return OrderResource::make($order->load(['product', 'buyer', 'seller', 'store']));
    }

    public function cancel(Request $request, Order $order)
    {
        $this->authorizeView($order);

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'Order cannot be cancelled.'], 422);
        }

        $order->update(['status' => 'cancelled']);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'note' => $request->input('reason', 'Cancelled by user'),
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Order cancelled.', 'data' => OrderResource::make($order->load(['product', 'buyer', 'seller', 'store']))]);
    }

    public function confirm(Request $request, Order $order)
    {
        if ($order->seller_id !== $request->user()->id && !$request->user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }
        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Order cannot be confirmed.'], 422);
        }

        $order->update(['status' => 'confirmed']);
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'confirmed',
            'note' => 'Order confirmed by seller',
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Order confirmed.', 'data' => OrderResource::make($order->load(['product', 'buyer', 'seller', 'store']))]);
    }

    public function deliver(Request $request, Order $order)
    {
        if ($order->seller_id !== $request->user()->id && !$request->user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }
        if ($order->status !== 'confirmed') {
            return response()->json(['message' => 'Order cannot be delivered.'], 422);
        }

        $order->update(['status' => 'delivered']);
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'delivered',
            'note' => 'Order delivered',
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Order delivered.', 'data' => OrderResource::make($order->load(['product', 'buyer', 'seller', 'store']))]);
    }

    public function complete(Request $request, Order $order)
    {
        if ($order->buyer_id !== $request->user()->id && !$request->user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }
        if ($order->status !== 'delivered') {
            return response()->json(['message' => 'Order cannot be completed.'], 422);
        }

        $order->update(['status' => 'completed']);
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'completed',
            'note' => 'Order completed by buyer',
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Order completed.', 'data' => OrderResource::make($order->load(['product', 'buyer', 'seller', 'store']))]);
    }

    private function authorizeView(Order $order): void
    {
        $user = request()->user();
        if ($user->hasRole('buyer') && $order->buyer_id !== $user->id) {
            abort(403, 'Forbidden');
        }
        if ($user->hasRole('seller') && $order->seller_id !== $user->id) {
            abort(403, 'Forbidden');
        }
    }
}
