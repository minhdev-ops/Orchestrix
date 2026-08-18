<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Services\GHTKService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SellerShippingController
{
    public function services(Request $request, Order $order)
    {
        if ($order->seller_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['product', 'buyer']);

        // Parse shipping address to get district info
        $address = $order->shipping_address;
        $services = [];
        $toDistrictId = null;

        $services = app(GHTKService::class)->getServices();

        return Inertia::render('Marketplace/Seller/Orders/Shipping', [
            'order' => $order,
            'services' => $services,
            'toDistrictId' => null,
        ]);
    }

    public function createShipment(Request $request, Order $order)
    {
        if ($order->seller_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'confirmed') {
            return back()->with('error', 'Đơn hàng cần được xác nhận trước khi tạo vận đơn.');
        }

        $data = $request->validate([
            'service_id' => 'required|integer',
            'weight' => 'required|numeric|min:100',
            'length' => 'nullable|numeric|min:1',
            'width' => 'nullable|numeric|min:1',
            'height' => 'nullable|numeric|min:1',
        ]);

        $pickProvince = 'Hồ Chí Minh';
        $pickDistrict = 'Quận 1';

        // Parse địa chỉ giao hàng để lấy tên tỉnh/quận
        $addressParts = explode(', ', $order->shipping_address ?? '');
        $deliverProvince = end($addressParts);
        $deliverDistrict = count($addressParts) > 1 ? $addressParts[count($addressParts) - 2] : 'Quận 1';

        $params = [
            'order_id' => 'ORD_'.$order->id.'_'.time(),
            'pick_name' => auth()->user()->name ?? 'Người bán',
            'pick_address' => 'Địa chỉ lấy hàng',
            'pick_province' => $pickProvince,
            'pick_district' => $pickDistrict,
            'pick_tel' => auth()->user()->phone ?? '',
            'name' => $order->buyer->name ?? '',
            'address' => $order->shipping_address ?? '',
            'province' => $deliverProvince,
            'district' => $deliverDistrict,
            'tel' => $order->buyer->phone ?? '',
            'note' => $order->notes ?? '',
            'value' => (int) $order->total_amount,
            'cod_amount' => 0,
            'transport' => 'road',
            'products' => [
                [
                    'name' => $order->product->name ?? 'Sản phẩm',
                    'weight' => (float) $data['weight'],
                    'quantity' => (int) $order->quantity,
                    'product_code' => (string) $order->product_id,
                ],
            ],
        ];

        try {
            $result = app(GHTKService::class)->createOrder($params);

            if (isset($result['order_code']) && ! empty($result['order_code'])) {
                $order->update([
                    'tracking_number' => $result['order_code'],
                    'tracking_url' => null,
                    'status' => 'shipping',
                ]);

                OrderStatus::create([
                    'order_id' => $order->id,
                    'status' => 'shipping',
                    'note' => 'Đã tạo vận đơn GHTK: '.$result['order_code'].' - Nhãn: '.($result['label'] ?? ''),
                    'user_id' => auth()->id(),
                ]);

                return redirect()->route('agriverse.shop.seller.orders.show', $order->id)
                    ->with('success', 'Đã tạo vận đơn GHTK thành công.');
            }

            $errorMsg = $result['error'] ?? 'Không thể tạo vận đơn GHTK. Vui lòng thử lại.';

            return back()->with('error', $errorMsg);
        } catch (\Exception $e) {
            Log::error('GHTK error: '.$e->getMessage());
            return back()->withErrors(['error' => 'Đã xảy ra lỗi khi tính phí vận chuyển.']);
        }
    }
}
