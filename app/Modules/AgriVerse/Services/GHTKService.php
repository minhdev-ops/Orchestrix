<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\Http;

class GHTKService
{
    protected string $baseUrl;

    protected ?string $token;

    protected ?string $clientSource;

    public function __construct()
    {
        $this->baseUrl = config('services.ghtk.base_url', 'https://services.giaohangtietkiem.vn');
        $this->token = config('services.ghtk.token', '');
        $this->clientSource = config('services.ghtk.client_source', '');
    }

    protected function getHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        if (! empty($this->token)) {
            $headers['Token'] = $this->token;
        }
        if (! empty($this->clientSource)) {
            $headers['X-Client-Source'] = $this->clientSource;
        }

        return $headers;
    }

    /**
     * Tính phí vận chuyển
     * GHTK không có API tính phí riêng, phí được trả về khi tạo đơn.
     * Phương thức này trả về phí ước tính dựa trên mock/kinh nghiệm.
     */
    public function calculateFee(
        string $pickProvince,
        string $pickDistrict,
        string $deliverProvince,
        string $deliverDistrict,
        int $weight,
        int $value
    ): array {
        if (empty($this->token)) {
            $baseFee = 25000;
            $isSameCity = trim($pickProvince) === trim($deliverProvince);
            $isSameDistrict = trim($pickDistrict) === trim($deliverDistrict);
            $fee = $isSameDistrict
                ? $baseFee
                : ($isSameCity ? $baseFee + 10000 : $baseFee + 25000);
            $insuranceFee = max(0, (int) round($value * 0.005));

            return [
                'fee' => $fee,
                'insurance_fee' => $insuranceFee,
                'estimated_delivery' => $isSameCity ? '1-2 ngày' : '3-5 ngày',
                'is_mock' => true,
            ];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())->post("{$this->baseUrl}/services/shipment/fee", [
                'pick_province' => $pickProvince,
                'pick_district' => $pickDistrict,
                'province' => $deliverProvince,
                'district' => $deliverDistrict,
                'weight' => $weight,
                'value' => $value,
                'transport' => 'road',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $feeData = $data['fee'] ?? [];
                if (is_array($feeData)) {
                    $deliveryDays = match ((int) ($feeData['delivery'] ?? 1)) {
                        1 => '1-2 ngày',
                        2 => '2-3 ngày',
                        default => '3-5 ngày',
                    };
                    return [
                        'fee' => (int) ($feeData['fee'] ?? $feeData['ship_fee_only'] ?? 0),
                        'insurance_fee' => (int) ($feeData['insurance_fee'] ?? 0),
                        'estimated_delivery' => $deliveryDays,
                        'is_mock' => false,
                    ];
                }

                return [
                    'fee' => (int) $feeData,
                    'insurance_fee' => (int) ($data['insurance_fee'] ?? 0),
                    'estimated_delivery' => $data['estimated_delivery'] ?? '2-4 ngày',
                    'is_mock' => false,
                ];
            }
        } catch (\Exception $e) {
            // Fallback
        }

        return ['fee' => 30000, 'insurance_fee' => 5000, 'is_mock' => true];
    }

    /**
     * Tạo đơn hàng trên GHTK
     */
    public function createOrder(array $params): array
    {
        if (empty($this->token)) {
            return [
                'order_code' => 'GHTK_MOCK_'.time(),
                'label' => 'MOCK.LABEL.'.time(),
                'fee' => 25000,
                'is_mock' => true,
            ];
        }

        $payload = [
            'products' => $params['products'] ?? [],
            'order' => [
                'id' => $params['order_id'] ?? 'ORD_'.time(),
                'pick_name' => $params['pick_name'] ?? '',
                'pick_address' => $params['pick_address'] ?? '',
                'pick_province' => $params['pick_province'] ?? '',
                'pick_district' => $params['pick_district'] ?? '',
                'pick_ward' => $params['pick_ward'] ?? '',
                'pick_tel' => $params['pick_tel'] ?? '',
                'name' => $params['name'] ?? '',
                'address' => $params['address'] ?? '',
                'province' => $params['province'] ?? '',
                'district' => $params['district'] ?? '',
                'ward' => $params['ward'] ?? '',
                'hamlet' => $params['hamlet'] ?? 'Khác',
                'tel' => $params['tel'] ?? '',
                'note' => $params['note'] ?? '',
                'value' => $params['value'] ?? 0,
                'pick_money' => $params['cod_amount'] ?? 0,
                'is_freeship' => $params['is_freeship'] ?? 0,
                'transport' => $params['transport'] ?? 'road',
                'pick_option' => 'cod',
            ],
        ];

        $response = Http::withHeaders($this->getHeaders())
            ->post("{$this->baseUrl}/services/shipment/order/?ver=1.5", $payload);

        if ($response->successful()) {
            $data = $response->json();
            if (($data['success'] ?? false) && isset($data['order'])) {
                return [
                    'order_code' => $data['order']['partner_id'] ?? '',
                    'label' => $data['order']['label'] ?? '',
                    'fee' => $data['order']['fee'] ?? 0,
                    'insurance_fee' => $data['order']['insurance_fee'] ?? 0,
                    'estimated_pick_time' => $data['order']['estimated_pick_time'] ?? '',
                    'estimated_deliver_time' => $data['order']['estimated_deliver_time'] ?? '',
                    'is_mock' => false,
                ];
            }

            return [
                'error' => $data['message'] ?? 'Lỗi không xác định từ GHTK',
                'is_mock' => false,
            ];
        }

        return ['error' => 'Không thể kết nối GHTK', 'is_mock' => false];
    }

    /**
     * Tra cứu đơn hàng
     */
    public function trackOrder(string $orderCode): array
    {
        if (empty($this->token)) {
            return [
                'status' => 'Đang giao',
                'status_id' => 2,
                'is_mock' => true,
            ];
        }

        $response = Http::withHeaders($this->getHeaders())
            ->post("{$this->baseUrl}/services/shipment/v2/{$orderCode}");

        if ($response->successful()) {
            return $response->json();
        }

        return ['status' => 'Không xác định'];
    }

    /**
     * Lấy danh sách dịch vụ vận chuyển khả dụng
     */
    public function getServices(): array
    {
        return [
            ['service_id' => 2, 'short_name' => 'Giao hàng tiết kiệm', 'transport' => 'road'],
        ];
    }
}
