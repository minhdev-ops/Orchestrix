<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\District;
use App\Modules\AgriVerse\Models\Province;
use App\Modules\AgriVerse\Models\Ward;
use App\Modules\AgriVerse\Services\GHTKService;
use Illuminate\Http\Request;

class GHTKAddressController
{
    /**
     * Danh sách tỉnh/thành (dùng local DB đã có từ GHN)
     */
    public function provinces()
    {
        $provinces = Province::orderBy('province_name')->get(['province_id', 'province_name', 'code']);

        return response()->json(['data' => $provinces]);
    }

    /**
     * Danh sách quận/huyện
     */
    public function districts(Request $request)
    {
        $request->validate(['province_id' => 'required|integer']);

        $districts = District::where('province_id', $request->province_id)
            ->orderBy('district_name')
            ->get(['district_id', 'district_name', 'province_id', 'code']);

        if ($districts->isNotEmpty()) {
            return response()->json(['data' => $districts]);
        }

        return response()->json(['data' => []]);
    }

    /**
     * Danh sách phường/xã
     */
    public function wards(Request $request)
    {
        $request->validate(['district_id' => 'required|integer']);

        $wards = Ward::where('district_id', $request->district_id)
            ->orderBy('ward_name')
            ->get(['ward_code', 'ward_name', 'district_id']);

        if ($wards->isNotEmpty()) {
            return response()->json(['data' => $wards]);
        }

        return response()->json(['data' => []]);
    }

    /**
     * Tính phí vận chuyển qua GHTK
     * Chuyển đổi ID thành tên để gọi GHTK API
     */
    public function shippingFee(GHTKService $ghtk, Request $request)
    {
        $data = $request->validate([
            'pick_province' => 'nullable|string|max:100',
            'deliver_province_name' => 'nullable|string|max:100',
            'deliver_district_name' => 'nullable|string|max:100',
            'weight' => 'required|numeric|min:1',
            'amount' => 'required|numeric|min:0',
        ]);

        $data['weight'] = (int) $data['weight'];
        $data['amount'] = (int) $data['amount'];

        $pickProvince = $data['pick_province'] ?? 'Hồ Chí Minh';
        $deliverProvince = $data['deliver_province_name'] ?? 'Hồ Chí Minh';
        $deliverDistrict = $data['deliver_district_name'] ?? 'Quận 1';

        $services = $ghtk->getServices();
        $fees = [];
        foreach ($services as $service) {
            $fee = $ghtk->calculateFee(
                $pickProvince,
                'Quận 1',
                $deliverProvince,
                $deliverDistrict,
                $data['weight'],
                $data['amount'],
            );
            $fees[] = [
                'service_id' => $service['service_id'],
                'short_name' => $service['short_name'],
                'transport' => $service['transport'],
                'fee' => $fee['fee'],
                'insurance_fee' => $fee['insurance_fee'],
            ];
        }

        return response()->json(['data' => $fees]);
    }
}
