<?php

namespace App\Modules\AgriVerse\Services;

use App\Modules\AgriVerse\Models\Product;

class PlantDiagnosisService
{
    /**
     * Decision-tree rules. Each rule lists the symptom(s) that trigger it
     * (normalized lowercase, diacritics-stripped keys) plus a diagnostic payload.
     */
    private array $rules = [
        [
            'keys' => ['moc trang'],
            'name' => 'Nấm phấn trắng (Powdery Mildew)',
            'severity' => 'medium',
            'confidence' => 0.92,
            'category_hint' => null,
            'causes' => [
                'Độ ẩm cao và thông gió kém tạo điều kiện cho nấm phát triển',
                'Lá đọng sương/nước lâu sau khi tưới',
                'Cây ở nơi thiếu ánh sáng gián tiếp',
            ],
            'treatments' => [
                'Tách cây bệnh khỏi các cây khỏe để tránh lây lan',
                'Cắt bỏ lá/đoạn bị mốc, vứt xa vườn',
                'Phun dung dịch chứa lưu huỳnh hoặc fungicide theo khuyến cáo',
                'Tăng thông gió và giảm độ ẩm quanh tán lá',
            ],
            'prevention' => [
                'Tưới vào gốc thay vì tưới phun lên lá',
                'Bố trí cây nơi thoáng, có gió nhẹ',
            ],
            'care' => 'Ánh sáng: gián tiếp, nhiều nắng nhẹ; tưới khi lớp đất mặt khô; thông gió tốt.',
        ],
        [
            'keys' => ['la dom'],
            'name' => 'Đốm lá do nấm / vi khuẩn',
            'severity' => 'medium',
            'confidence' => 0.85,
            'category_hint' => null,
            'causes' => [
                'Nấm hoặc vi khuẩn lây qua nước bắn khi tưới',
                'Lá ẩm ướt kéo dài',
                'Vết thương cơ học tạo cửa ngõ cho mầm bệnh',
            ],
            'treatments' => [
                'Cách ly cây và cắt bỏ lá có đốm',
                'Không tưới phun lên lá vào buổi chiều',
                'Phun thuốc trị nấm/vi khuẩn phù hợp nếu tái phát',
                'Dọn sạch lá rụng dưới gốc',
            ],
            'prevention' => [
                'Tưới gốc, tránh làm ướt lá',
                'Giữ vệ sinh dụng cụ cắt tỉa',
            ],
            'care' => 'Tránh để lá ẩm qua đêm; tưới vào sáng sớm; tuyệt đối không tưới đẫm lá.',
        ],
        [
            'keys' => ['vang la', 'rung la'],
            'name' => 'Thối rễ do tưới quá nhiều / thoát nước kém',
            'severity' => 'high',
            'confidence' => 0.9,
            'category_hint' => null,
            'causes' => [
                'Tưới quá thường xuyên khiến rễ ngập úng',
                'Giá thể thoát nước kém',
                'Chậu không có lỗ thoát nước',
            ],
            'treatments' => [
                'Ngừng tưới cho đến khi đất khô',
                'Kiểm tra và cắt bỏ rễ thối, màu nâu đen',
                'Thay giá thể mới thoát nước tốt (đá perlite + vỏ thông)',
                'Cắt bớt lá già để giảm thoát hơi nước',
            ],
            'prevention' => [
                'Tưới chỉ khi lớp đất mặt 2-3cm đã khô',
                'Đảm bảo chậu có lỗ thoát nước',
            ],
            'care' => 'Đất thoát nước nhanh; chỉ tưới khi khô; tránh để nước đọng ở đĩa chậu.',
        ],
        [
            'keys' => ['vang la'],
            'name' => 'Thiếu dinh dưỡng / sốc môi trường',
            'severity' => 'low',
            'confidence' => 0.8,
            'category_hint' => null,
            'causes' => [
                'Thiếu đạm hoặc vi lượng',
                'Vừa chuyển chậu / đổi vị trí',
                'Thiếu ánh sáng khiến lá mất màu',
            ],
            'treatments' => [
                'Bón phân cân đối (N-P-K) theo liều khuyến cáo',
                'Đưa cây về vị trí có ánh sáng gián tiếp phù hợp',
                'Điều chỉnh lịch tưới, tránh ngập úng',
            ],
            'prevention' => [
                'Bón phân định kỳ trong mùa sinh trưởng',
                'Đổi chậu nhẹ nhàng, hạn chế sốc rễ',
            ],
            'care' => 'Sáng gián tiếp nhiều giờ; tưới đều; bón phân loãng định kỳ.',
        ],
        [
            'keys' => ['dau la nau'],
            'name' => 'Khô mép / đầu lá do độ ẩm thấp',
            'severity' => 'low',
            'confidence' => 0.82,
            'category_hint' => null,
            'causes' => [
                'Độ ẩm không khí thấp',
                'Tưới không đều, đất khô hẳn rồi mới tưới',
                'Tích tụ muối khoáng trong giá thể',
            ],
            'treatments' => [
                'Tăng độ ẩm (phun sương, đặt khay nước, máy tạo ẩm)',
                'Tưới đều đặn, không để đất khô hoàn toàn',
                'Cắt bỏ phần đầu lá khô, rửa giá thể bớt muối',
            ],
            'prevention' => [
                'Duy trì độ ẩm ổn định quanh cây',
                'Dùng nước lọc nếu nước máy nhiều khoáng',
            ],
            'care' => 'Giữ ẩm không khí quanh cây; tưới đều; tránh nắng gắt buổi trưa.',
        ],
        [
            'keys' => ['cham phat trien'],
            'name' => 'Cây ngừng / chậm phát triển',
            'severity' => 'medium',
            'confidence' => 0.8,
            'category_hint' => null,
            'causes' => [
                'Thiếu ánh sáng',
                'Thiếu dinh dưỡng',
                'Chậu quá chật, rễ bó không phát triển',
            ],
            'treatments' => [
                'Tăng cường ánh sáng gián tiếp hoặc ánh sáng nhân tạo',
                'Bón phân cân đối',
                'Thay chậu lớn hơn nếu rễ đã bó',
            ],
            'prevention' => [
                'Đảm bảo cây nhận đủ quang kỳ mỗi ngày',
                'Kiểm tra bộ rễ định kỳ',
            ],
            'care' => 'Đủ nắng gián tiếp; bón phân mùa phát triển; thay chậu khi rễ bó.',
        ],
        [
            'keys' => ['rung la'],
            'name' => 'Sốc môi trường',
            'severity' => 'low',
            'confidence' => 0.75,
            'category_hint' => null,
            'causes' => [
                'Thay đổi đột ngột nhiệt độ / ánh sáng',
                'Vừa chuyển chậu hoặc thay đổi vị trí',
                'Di chuyển từ môi trường nhà kính về nhà',
            ],
            'treatments' => [
                'Đặt cây ở nơi ổn định, hạn chế di chuyển',
                'Duy trì độ ẩm và nhiệt độ ổn định',
                'Kiên nhẫn cho cây thích nghi 1-2 tuần',
            ],
            'prevention' => [
                'Chuyển chậu / đổi vị trí từ từ',
                'Tránh đặt gần điều hòa hoặc luồng gió lạnh',
            ],
            'care' => 'Vị trí ổn định, tránh sốc nhiệt; điều chỉnh dần môi trường mới.',
        ],
    ];

    public function analyze(array $symptomNames, ?string $plantName = null): array
    {
        $norm = array_values(array_unique(array_map(
            fn ($s) => $this->normalize((string) $s),
            $symptomNames
        )));

        $best = null;
        $bestScore = -1;

        foreach ($this->rules as $rule) {
            $ruleKeys = array_map(fn ($k) => $this->normalize($k), $rule['keys']);

            // Trigger only if ALL rule keys are present among selected symptoms.
            $matched = count(array_intersect($ruleKeys, $norm));
            if ($matched !== count($ruleKeys) || $matched === 0) {
                continue;
            }

            // More specific rules (overlapping more selected symptoms) win.
            $overlap = count(array_intersect($ruleKeys, $norm));
            $score = $overlap * 10 + $matched;
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $rule;
            }
        }

        if (! $best) {
            return [
                'matched' => false,
                'message' => 'Chưa đủ thông tin để chẩn đoán. Vui lòng chọn thêm triệu chứng hoặc mô tả chi tiết hơn.',
                'diagnosis' => null,
                'products' => [],
            ];
        }

        $products = $this->relatedProducts($best['category_hint']);

        return [
            'matched' => true,
            'diagnosis' => [
                'code' => 'CHANDOAN-' . strtoupper(substr(hash('crc32b', implode($best['keys'])), 0, 4)),
                'plant_name' => $plantName,
                'disease_name' => $best['name'],
                'severity' => $best['severity'],
                'confidence' => $best['confidence'],
                'causes' => $best['causes'],
                'treatments' => $best['treatments'],
                'prevention' => $best['prevention'],
                'care' => $best['care'],
                'symptoms' => $symptomNames,
            ],
            'products' => $products,
        ];
    }

    /**
     * Returns real products to pair with the diagnosis so recommendations
     * stay anchored to actual catalogue data.
     */
    private function relatedProducts(?string $hint = null, int $limit = 3): array
    {
        $query = Product::query()->where('status', 'published');

        if ($hint) {
            $query->where('category', $hint);
        }

        $list = $query->orderBy('stock', 'desc')->inRandomOrder()->limit($limit)->get();

        return $list->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'slug' => $p->slug,
            'price' => $p->price,
            'image' => $p->image,
        ])->values()->all();
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value);

        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }
}