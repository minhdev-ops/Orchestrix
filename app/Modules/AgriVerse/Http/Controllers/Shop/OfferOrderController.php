<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Tạo & xác nhận đơn hàng từ Offer được admin duyệt (APPROVED).
 *  - initialize : (sau APPROVED) tạo đơn trạng thái pending_confirmation, deadline 12h.
 *  - confirm    : người mua xác nhận lại thông tin -> đơn confirmed.
 *  - show       : lấy đơn đang chờ xác nhận của một offer (cho trang Offer).
 *  - cancel     : hủy thủ công (buyer) khi đang pending_confirmation.
 * Auto-hủy sau 12h (status=pending_confirmation, confirm_deadline qua) do scheduler.
 */
class OfferOrderController
{
    public function initialize(Request $request, string $offerId)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
            'product_name' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'seller_id' => 'required|integer',
            'product_image' => 'nullable|string',
        ]);

        $existing = Order::where('offer_id', $offerId)->first();
        if ($existing) {
            return response()->json([
                'ok' => true,
                'order' => $this->orderPayload($existing),
                'already' => true,
            ]);
        }

        $buyerId = auth()->id();
        $product = Product::find($data['product_id']);

        $quantity = (int) $data['quantity'];
        $unitPrice = (float) $data['price'];
        $total = $unitPrice * $quantity;

        $order = Order::create([
            'offer_id' => $offerId,
            'product_id' => $data['product_id'],
            'buyer_id' => $buyerId,
            'seller_id' => $data['seller_id'],
            'store_id' => $product?->store_id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $total,
            'discount_amount' => 0,
            'total_amount' => $total,
            'commission_fee' => 0,
            'status' => 'pending_confirmation',
            'confirm_deadline' => Carbon::now()->addHours(12),
            'metadata' => [
                'offer' => [
                    'offer_id' => $offerId,
                    'product_name' => $data['product_name'] ?? $product?->name,
                    'product_image' => $data['product_image'] ?? $product?->image,
                    'seller_id' => $data['seller_id'],
                    'buyer_id' => $buyerId,
                ],
            ],
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'pending_confirmation',
            'note' => 'Đơn hàng từ đề xuất giá đã tạo. Vui lòng xác nhận trong 12h.',
            'user_id' => $buyerId,
        ]);

        return response()->json([
            'ok' => true,
            'order' => $this->orderPayload($order),
            'already' => false,
        ]);
    }

    public function show(Request $request, string $offerId)
    {
        $order = Order::where('offer_id', $offerId)->first();
        if (! $order) {
            return response()->json(['ok' => false, 'order' => null]);
        }
        $isParty = $order->buyer_id === auth()->id() || $order->seller_id === auth()->id();
        abort_unless($isParty || auth()->user()?->role === 'admin', 403);

        return response()->json(['ok' => true, 'order' => $this->orderPayload($order)]);
    }

    public function confirm(Request $request, Order $order)
    {
        abort_unless($order->buyer_id === auth()->id(), 403);

        if ($order->status !== 'pending_confirmation') {
            return response()->json(['ok' => false, 'message' => 'Đơn hàng không ở trạng thái cần xác nhận.'], 422);
        }
        if ($order->confirm_deadline && Carbon::parse($order->confirm_deadline)->isPast()) {
            return response()->json(['ok' => false, 'message' => 'Đơn hàng đã hết hạn xác nhận (12h).'], 422);
        }

        $data = $request->validate([
            'shipping_address' => 'nullable|string|max:255',
            'shipping_phone' => 'nullable|string|max:20',
        ]);

        $address = $data['shipping_address'];
        if (! $address) {
            $default = UserAddress::where('user_id', auth()->id())->where('is_default', true)->first()
                ?? UserAddress::where('user_id', auth()->id())->first();
            if ($default) {
                $address = trim(
                    ($default->address_detail ?? '')
                    . ($default->ward ? ', '.$default->ward : '')
                    . ($default->province ? ', '.$default->province : '')
                );
            }
        }

        $order->update([
            'status' => 'confirmed',
            'shipping_address' => $address ?: $order->shipping_address,
            'confirmed_at' => Carbon::now(),
            'metadata' => array_merge($order->metadata ?? [], ['shipping_phone' => $data['shipping_phone'] ?? null]),
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'confirmed',
            'note' => 'Người mua đã xác nhận thông tin đơn hàng.',
            'user_id' => auth()->id(),
        ]);

        return response()->json(['ok' => true, 'order' => $this->orderPayload($order)]);
    }

    public function cancel(Request $request, Order $order)
    {
        $isBuyer = $order->buyer_id === auth()->id();
        $isSeller = $order->seller_id === auth()->id();
        $isAdmin = auth()->user()?->role === 'admin';
        abort_unless($isBuyer || $isSeller || $isAdmin, 403);

        if ($order->status !== 'pending_confirmation') {
            return response()->json(['ok' => false, 'message' => 'Đơn hàng không ở trạng thái chờ xác nhận.'], 422);
        }

        $order->update([
            'status' => 'cancelled',
            'cancel_reason' => $request->input('reason') ?: 'Người mua không xác nhận đơn hàng.',
            'cancelled_at' => Carbon::now(),
        ]);

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'note' => $order->cancel_reason,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['ok' => true, 'order' => $this->orderPayload($order)]);
    }

    private function orderPayload(Order $order): array
    {
        return [
            'id' => $order->id,
            'offer_id' => $order->offer_id,
            'product_id' => $order->product_id,
            'status' => $order->status,
            'quantity' => $order->quantity,
            'total_amount' => (float) $order->total_amount,
            'unit_price' => (float) $order->unit_price,
            'shipping_address' => $order->shipping_address,
            'confirm_deadline' => $order->confirm_deadline?->toIso8601String(),
            'confirmed_at' => $order->confirmed_at?->toIso8601String(),
            'created_at' => $order->created_at?->toIso8601String(),
            'metadata' => $order->metadata,
        ];
    }
}