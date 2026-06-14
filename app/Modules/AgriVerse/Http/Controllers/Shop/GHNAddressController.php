<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use App\Modules\AgriVerse\Models\Province;
use App\Modules\AgriVerse\Models\District;
use App\Modules\AgriVerse\Models\Ward;
use App\Modules\AgriVerse\Services\GHNService;

class GHNAddressController
{
    public function provinces(GHNService $ghn)
    {
        $provinces = Province::orderBy('province_name')->get(['province_id', 'province_name', 'code']);

        if ($provinces->isNotEmpty()) {
            return response()->json(['data' => $provinces]);
        }

        // Fallback to GHN API if local DB is empty
        $apiProvinces = $ghn->getProvinces();
        $mapped = array_map(fn ($p) => [
            'province_id' => $p['ProvinceID'],
            'province_name' => $p['ProvinceName'],
            'code' => $p['Code'] ?? null,
        ], $apiProvinces);

        return response()->json(['data' => $mapped]);
    }

    public function districts(Request $request, GHNService $ghn)
    {
        $request->validate(['province_id' => 'required|integer']);

        $districts = District::where('province_id', $request->province_id)
            ->orderBy('district_name')
            ->get(['district_id', 'district_name', 'province_id', 'code']);

        if ($districts->isNotEmpty()) {
            return response()->json(['data' => $districts]);
        }

        // Fallback to GHN API
        $apiDistricts = $ghn->getDistricts((int) $request->province_id);
        $mapped = array_map(fn ($d) => [
            'district_id' => $d['DistrictID'],
            'district_name' => $d['DistrictName'],
            'province_id' => $d['ProvinceID'],
            'code' => $d['Code'] ?? null,
        ], $apiDistricts);

        return response()->json(['data' => $mapped]);
    }

    public function wards(Request $request, GHNService $ghn)
    {
        $request->validate(['district_id' => 'required|integer']);

        $wards = Ward::where('district_id', $request->district_id)
            ->orderBy('ward_name')
            ->get(['ward_code', 'ward_name', 'district_id']);

        if ($wards->isNotEmpty()) {
            return response()->json(['data' => $wards]);
        }

        // Fallback to GHN API
        $apiWards = $ghn->getWards((int) $request->district_id);
        $mapped = array_map(fn ($w) => [
            'ward_code' => $w['WardCode'],
            'ward_name' => $w['WardName'],
            'district_id' => $request->district_id,
        ], $apiWards);

        return response()->json(['data' => $mapped]);
    }

    public function shippingFee(GHNService $ghn, Request $request)
    {
        $data = $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
            'weight' => 'required|integer|min:1',
            'amount' => 'required|integer|min:0',
        ]);

        $services = $ghn->getServices($data['to_district_id']);

        $fees = [];
        foreach ($services as $service) {
            $fee = $ghn->calculateFee(
                $data['to_district_id'],
                $data['to_ward_code'],
                $data['weight'],
                $data['amount'],
                $service['service_type_id'],
            );
            $fees[] = [
                'service_id' => $service['service_id'],
                'short_name' => $service['short_name'],
                'service_type_id' => $service['service_type_id'],
                'fee' => $fee['total'] ?? 0,
                'insurance_fee' => $fee['insurance_fee'] ?? 0,
            ];
        }

        return response()->json(['data' => $fees]);
    }
}
