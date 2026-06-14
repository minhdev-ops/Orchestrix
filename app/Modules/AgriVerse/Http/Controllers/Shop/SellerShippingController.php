<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Services\GHNService;

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

        // Try to get district from address metadata or parse address
        $metadata = $order->metadata ?? [];
        if (isset($metadata['to_district_id'])) {
            $toDistrictId = (int) $metadata['to_district_id'];
        }

        if ($toDistrictId) {
            try {
                $services = app(GHNService::class)->getServices($toDistrictId);
            } catch (\Exception) {
                $services = [];
            }
        }

        return Inertia::render('Marketplace/Seller/Orders/Shipping', [
            'order' => $order,
            'services' => $services,
            'toDistrictId' => $toDistrictId,
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

        $metadata = $order->metadata ?? [];
        $toDistrictId = $metadata['to_district_id'] ?? null;
        $toWardCode = $metadata['to_ward_code'] ?? '';

        if (!$toDistrictId || !$toWardCode) {
            return back()->with('error', 'Thiếu thông tin địa chỉ giao hàng để tạo vận đơn GHN.');
        }

        $params = [
            'service_id' => (int) $data['service_id'],
            'to_district_id' => (int) $toDistrictId,
            'to_ward_code' => (string) $toWardCode,
            'weight' => (int) $data['weight'],
            'length' => isset($data['length']) ? (int) $data['length'] : 10,
            'width' => isset($data['width']) ? (int) $data['width'] : 10,
            'height' => isset($data['height']) ? (int) $data['height'] : 10,
            'insurance_value' => (int) $order->total_amount,
            'cod_amount' => 0,
            'note' => $order->notes ?? '',
        ];

        try {
            $result = app(GHNService::class)->createOrder($params);

            if (isset($result['order_code'])) {
                $order->update([
                    'tracking_number' => $result['order_code'],
                    'tracking_url' => $result['tracking_url'] ?? null,
                    'status' => 'shipping',
                ]);

                OrderStatus::create([
                    'order_id' => $order->id,
                    'status' => 'shipping',
                    'note' => 'Đã tạo vận đơn GHN: ' . $result['order_code'],
                    'user_id' => auth()->id(),
                ]);

                return redirect()->route('agriverse.shop.seller.orders.show', $order->id)
                    ->with('success', 'Đã tạo vận đơn thành công.');
            }

            return back()->with('error', 'Không thể tạo vận đơn GHN. Vui lòng thử lại.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi GHN: ' . $e->getMessage());
        }
    }
}
