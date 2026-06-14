<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\Cart;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Models\DigitalPassportLog;
use App\Modules\AgriVerse\Models\Province;
use App\Modules\AgriVerse\Services\GHNService;
use App\Modules\AgriVerse\Events\OrderCreated;

class CheckoutController
{
    public function index()
    {
        $cartItems = Cart::with(['product', 'store'])
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('agriverse.shop.cart.index');
        }

        $addresses = \App\Modules\AgriVerse\Models\UserAddress::where('user_id', auth()->id())
            ->latest('is_default')
            ->latest()
            ->get()
            ->map(fn ($a) => array_merge($a->toArray(), [
                'ghn_district_id' => $a->ghn_district_id,
                'ghn_ward_code' => $a->ghn_ward_code,
            ]));

        $provinces = Province::orderBy('province_name')->get(['province_id', 'province_name', 'code']);
        if ($provinces->isEmpty()) {
            $ghn = app(GHNService::class);
            $apiProvinces = $ghn->getProvinces();
            $provinces = array_map(fn ($p) => [
                'province_id' => $p['ProvinceID'],
                'province_name' => $p['ProvinceName'],
                'code' => $p['Code'] ?? null,
            ], $apiProvinces);
        }

        return Inertia::render('Marketplace/Checkout/Index', [
            'cartItems' => $cartItems->map(fn ($item) => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'image' => $item->product->image,
                    'user_id' => $item->product->user_id,
                ] : null,
            ]),
            'addresses' => $addresses,
            'provinces' => $provinces,
        ]);
    }

    public function process(Request $request)
    {
        $cartItems = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Giỏ hàng trống.');
        }

        $request->validate([
            'shipping_address' => 'required|string',
            'shipping_method' => 'nullable|string|max:50',
            'shipping_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $lastOrder = null;

        foreach ($cartItems as $index => $item) {
            if (!$item->product || $item->product->stock < $item->quantity) {
                return back()->with('error', "Sản phẩm '{$item->product?->name}' không đủ hàng.");
            }

            $unitPrice = $item->product->price;
            $totalPrice = $unitPrice * $item->quantity;
            $shippingFee = $request->shipping_fee ?? 0;
            $orderTotal = $totalPrice + ($index === 0 ? (float) $shippingFee : 0);

            $item->product->decrement('stock', $item->quantity);

            $order = Order::create([
                'product_id' => $item->product_id,
                'buyer_id' => auth()->id(),
                'seller_id' => $item->product->user_id,
                'store_id' => $item->store_id,
                'quantity' => $item->quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'discount_amount' => 0,
                'total_amount' => $orderTotal,
                'commission_fee' => 0,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'shipping_method' => $request->shipping_method,
                'shipping_fee' => $shippingFee,
                'notes' => $request->notes,
            ]);

            $lastOrder = $order;

            OrderStatus::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'note' => 'Đơn hàng đã được tạo.',
                'user_id' => auth()->id(),
            ]);

            DigitalPassportLog::create([
                'product_id' => $item->product_id,
                'action' => 'ordered',
                'data' => ['order_id' => $order->id],
                'performed_by' => auth()->id(),
            ]);
        }

        Cart::where('user_id', auth()->id())->delete();

        if ($lastOrder) {
            OrderCreated::dispatch($lastOrder);
        }

        return redirect()->route('agriverse.shop.checkout.success', $lastOrder)
            ->with('success', 'Đặt hàng thành công!');
    }

    public function success(Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['seller', 'store', 'product']);

        return Inertia::render('Marketplace/Checkout/Success', [
            'orderId' => $order->uuid ?? (string) $order->id,
            'seller' => $order->seller ? [
                'name' => $order->seller->name,
                'email' => $order->seller->email,
                'phone' => $order->seller->phone,
            ] : null,
            'store' => $order->store ? [
                'name' => $order->store->name,
                'phone' => $order->store->phone,
            ] : null,
            'product' => $order->product ? [
                'name' => $order->product->name,
                'price' => $order->product->price,
            ] : null,
            'total' => $order->total_amount,
            'shippingMethod' => $order->shipping_method,
        ]);
    }
}
