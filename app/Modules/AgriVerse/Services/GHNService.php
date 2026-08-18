<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\Http;

class GHNService
{
    protected string $baseUrl;

    protected ?string $token;

    protected int $shopId;

    public function __construct()
    {
        $this->baseUrl = config('services.ghn.base_url', 'https://dev-online-gateway.ghn.vn/api/v2');
        $this->token = config('services.ghn.token', '');
        $this->shopId = (int) config('services.ghn.shop_id', 0);
    }

    public function getProvinces(): array
    {
        if (empty($this->token)) {
            // Fallback mock data if GHN Token is missing
            return [
                ['ProvinceID' => 201, 'ProvinceName' => 'Hà Nội', 'Code' => 'HN'],
                ['ProvinceID' => 202, 'ProvinceName' => 'Hồ Chí Minh', 'Code' => 'HCM'],
                ['ProvinceID' => 203, 'ProvinceName' => 'Đà Nẵng', 'Code' => 'DN'],
            ];
        }

        $res = Http::withToken($this->token)->get("{$this->baseUrl}/address/getProvince");

        return $res->successful() ? ($res->json()['data'] ?? []) : [];
    }

    public function getDistricts(int $provinceId): array
    {
        if (empty($this->token)) {
            return [
                ['DistrictID' => 1442, 'DistrictName' => 'Quận 1', 'ProvinceID' => 202, 'Code' => 'Q1'],
                ['DistrictID' => 1443, 'DistrictName' => 'Quận 2', 'ProvinceID' => 202, 'Code' => 'Q2'],
            ];
        }
        $res = Http::withToken($this->token)->post("{$this->baseUrl}/address/getDistrict", [
            'province_id' => $provinceId,
        ]);

        return $res->successful() ? ($res->json()['data'] ?? []) : [];
    }

    public function getWards(int $districtId): array
    {
        if (empty($this->token)) {
            return [
                ['WardCode' => '1A', 'WardName' => 'Phường Bến Nghé'],
                ['WardCode' => '1B', 'WardName' => 'Phường Bến Thành'],
            ];
        }
        $res = Http::withToken($this->token)->post("{$this->baseUrl}/address/getWard", [
            'district_id' => $districtId,
        ]);

        return $res->successful() ? ($res->json()['data'] ?? []) : [];
    }

    public function calculateFee(int $toDistrictId, string $toWardCode, int $weight, int $amount, ?int $serviceTypeId = null): array
    {
        if (empty($this->token)) {
            return ['total' => 25000, 'insurance_fee' => 5000];
        }
        $res = Http::withToken($this->token)->post("{$this->baseUrl}/shipping-order/fee", [
            'shop_id' => $this->shopId,
            'to_district_id' => $toDistrictId,
            'to_ward_code' => $toWardCode,
            'weight' => $weight,
            'insurance_value' => $amount,
            'service_type_id' => $serviceTypeId ?? 2,
        ]);

        return $res->successful() ? ($res->json()['data'] ?? []) : [];
    }

    public function createOrder(array $params): array
    {
        if (empty($this->token)) {
            return ['order_code' => 'MOCK_ORDER_'.time()];
        }
        $res = Http::withToken($this->token)->post("{$this->baseUrl}/shipping-order/create", array_merge([
            'shop_id' => $this->shopId,
            'service_type_id' => 2,
            'payment_type_id' => 1,
            'required_note' => 'CHOXEMHANGKHONGTHU',
        ], $params));

        return $res->successful() ? ($res->json()['data'] ?? []) : [];
    }

    public function trackOrder(string $orderCode): array
    {
        $res = Http::withToken($this->token)->post("{$this->baseUrl}/shipping-order/detail", [
            'order_code' => $orderCode,
        ]);

        return $res->successful() ? ($res->json()['data'] ?? []) : [];
    }

    public function getServices(int $toDistrictId): array
    {
        if (empty($this->token)) {
            return [
                ['service_id' => 1, 'short_name' => 'Chuyển phát nhanh', 'service_type_id' => 2],
                ['service_id' => 2, 'short_name' => 'Chuyển phát tiêu chuẩn', 'service_type_id' => 1],
            ];
        }
        $res = Http::withToken($this->token)->post("{$this->baseUrl}/shipping-order/available-services", [
            'shop_id' => $this->shopId,
            'to_district_id' => $toDistrictId,
        ]);

        return $res->successful() ? ($res->json()['data'] ?? []) : [];
    }
}
