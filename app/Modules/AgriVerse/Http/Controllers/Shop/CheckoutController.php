<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Events\OrderCreated;
use App\Modules\AgriVerse\Exceptions\InsufficientStockException;
use App\Modules\AgriVerse\Models\Cart;
use App\Modules\AgriVerse\Models\DigitalPassportLog;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Province;
use App\Modules\AgriVerse\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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

        $addresses = UserAddress::where('user_id', auth()->id())
            ->latest('is_default')
            ->latest()
            ->get()
            ->map(fn ($a) => array_merge($a->toArray(), [
                'ghn_district_id' => $a->ghn_district_id,
                'ghn_ward_code' => $a->ghn_ward_code,
            ]));

        $provinces = Province::orderBy('province_name')->get(['province_id', 'province_name', 'code']);

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
            'new_address' => 'nullable|array',
            'new_address.recipient_name' => 'required_with:new_address|string|max:255',
            'new_address.phone' => 'required_with:new_address|string|max:20',
            'new_address.province_id' => 'required_with:new_address',
            'new_address.district_id' => 'required_with:new_address',
            'new_address.ward_code' => 'required_with:new_address',
            'new_address.address_detail' => 'required_with:new_address|string|max:500',
        ]);

        // Save new address if provided
        if ($request->new_address) {
            $na = $request->new_address;
            $provinceName = is_string($na['province_name'] ?? null) ? $na['province_name'] : '';
            $districtName = is_string($na['district_name'] ?? null) ? $na['district_name'] : '';
            $wardName = is_string($na['ward_name'] ?? null) ? $na['ward_name'] : '';
            $provinceId = is_numeric($na['province_id'] ?? null) ? (int) $na['province_id'] : 0;
            $districtId = is_numeric($na['district_id'] ?? null) ? (int) $na['district_id'] : 0;
            $wardCode = is_string($na['ward_code'] ?? null) ? $na['ward_code'] : (is_numeric($na['ward_code'] ?? null) ? (string) $na['ward_code'] : '');

            UserAddress::create([
                'user_id' => auth()->id(),
                'recipient_name' => $na['recipient_name'] ?? '',
                'phone' => $na['phone'] ?? '',
                'province' => $provinceName ?: ($provinceId ? "ID:{$provinceId}" : ''),
                'district' => $districtName ?: ($districtId ? "ID:{$districtId}" : ''),
                'ward' => $wardName ?: ($wardCode ? "Code:{$wardCode}" : ''),
                'province_id' => $provinceId,
                'district_id' => $districtId,
                'ward_code' => $wardCode,
                'address_detail' => $na['address_detail'] ?? '',
                'is_default' => UserAddress::where('user_id', auth()->id())->count() === 0,
            ]);
        }

        $lastOrder = null;

        try {
            $lastOrder = DB::transaction(function () use ($cartItems, $request) {
                $lastOrder = null;

                foreach ($cartItems as $index => $item) {
                    $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if (! $product || $product->stock < $item->quantity) {
                        throw new InsufficientStockException(
                            "Sản phẩm '{$product?->name}' không đủ hàng."
                        );
                    }

                    $unitPrice = $product->price;
                    $totalPrice = $unitPrice * $item->quantity;
                    $shippingFee = $request->shipping_fee ?? 0;
                    $orderTotal = $totalPrice + ($index === 0 ? (float) $shippingFee : 0);

                    $product->decrement('stock', $item->quantity);

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

                return $lastOrder;
            });
        } catch (InsufficientStockException $e) {
            return back()->with('error', $e->getMessage());
        }

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
            'orderId' => (string) $order->id,
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
