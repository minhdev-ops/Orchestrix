<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIPlantDoctorService
{
    private string $provider;

    public function __construct()
    {
        $this->provider = config('services.ai_plant_doctor.provider', 'gemini');
    }

    public function diagnose(UploadedFile $image, ?string $symptoms = null): array
    {
        // vit_local: model local best_vit.keras chạy trong microservice FastAPI.
        // Enhanced pipeline: Grounding DINO → Crop leaves → Ensemble prediction
        if ($this->provider === 'vit_local') {
            return $this->diagnoseWithVitLocalEnhanced($image, $symptoms);
        }

        $base64 = base64_encode(file_get_contents($image->getRealPath()));
        $mimeType = $image->getMimeType();

        return match ($this->provider) {
            'gemini' => $this->callGemini($base64, $mimeType, $symptoms),
            'openai' => $this->callOpenAI($base64, $mimeType, $symptoms),
            default => throw new \RuntimeException("Unsupported AI provider: {$this->provider}"),
        };
    }

    /**
     * Luồng ViT local nâng cao:
     * 1. Gửi ảnh gốc → /detect-leaves (Grounding DINO)
     * 2. Với每個 bbox lá → /crop để trích出一片 leaf
     * 3. Send từng ảnh leaf → /predict-leaf
     * 4. Aggregate kết quả từ tất cả các lá
     * 5. Bổ sung mô tả/biện pháp bằng cloud provider nếu cấu hình
     *
     * Trả về cấu trúc giống provider khác: plant_name, disease_name, confidence,
     * severity, description, treatments, prevention, raw_response, provider.
     */
    private function diagnoseWithVitLocalEnhanced(UploadedFile $image, ?string $symptoms): array
    {
        $serviceUrl = rtrim((string) config('services.ai_plant_doctor.vit_service_url', 'http://localhost:8501'), '/');
        $token = (string) config('services.ai_plant_doctor.vit_token', '');
        $topK = (int) config('services.ai_plant_doctor.vit_defaults.top_k', 5);
        $timeout = (int) config('services.ai_plant_doctor.vit_defaults.timeout', 30);
        $minConfidence = (float) config('services.ai_plant_doctor.vit_defaults.min_confidence', 0.45);
        $autoDetect = (bool) config('services.ai_plant_doctor.vit_auto_detect', true);

        $rawResponse = [];

        // Nếu không dùng auto-detect lá → predict trực tiếp (backward compatible)
        if (!$autoDetect) {
            return $this->diagnoseWithoutLeafDetection($serviceUrl, $token, $timeout, $image, $symptoms, $topK, $minConfidence);
        }

        // Bước 1: Detect all leaves using Grounding DINO
        $leavesResponse = $this->callDetectLeaves($serviceUrl, $token, $timeout, $image);

        if (!isset($leavesResponse['bboxes']) || empty($leavesResponse['bboxes'])) {
            Log::warning('Không tìm thấy lá nào trong ảnh, fallback predict trực tiếp');
            // Fallback: predict trên ảnh nguyên tem
            return $this->diagnoseWithoutLeafDetection($serviceUrl, $token, $timeout, $image, $symptoms, $topK, $minConfidence);
        }

        $bboxes = $leavesResponse['bboxes'];
        $leafPredictions = [];
        $unknownLeafCount = 0;

        // Bước 2 & 3: Crop each leaf và predict riêng lẻ
        foreach ($bboxes as $index => $bbox) {
            $cropResult = $this->callCropAndPredict($serviceUrl, $token, $timeout, $image, $bbox, $topK);

            $leafLabel = is_array($cropResult) ? ($cropResult['predicted_label'] ?? '') : '';
            $isLeafUnknown = $leafLabel === ''
                || stripos($leafLabel, 'unknown') !== false
                || stripos($leafLabel, 'không xác định') !== false;

            if ($isLeafUnknown) {
                $unknownLeafCount++;
                Log::warning("Leaf {$index}: không xác định được bệnh (unknown), khuyến nghị đưa cho người có thẩm quyền kiểm tra");
                continue;
            }

            $cropResult['leaf_id'] = $index;
            $cropResult['bbox'] = $bbox;
            $leafPredictions[] = $cropResult;
            Log::info("Leaf {$index}: {$cropResult['predicted_label']} @ {$cropResult['confidence']}");
        }

        // Bước 4: Aggregate predictions from all leaves
        $aggregated = $this->aggregateLeafPredictions($leafPredictions, $topK);

        if (!$aggregated['predicted_label']) {
            $description = $this->unknownLeafNotice($unknownLeafCount)
                . ' Hệ thống không đủ thông tin để đưa ra chẩn đoán tự động.';

            return [
                'plant_name' => 'Không xác định',
                'disease_name' => 'Không xác định',
                'confidence' => 0,
                'severity' => 'unknown',
                'description' => $description,
                'treatments' => [],
                'prevention' => [],
                'raw_response' => [
                    'vit' => [
                        'aggregated' => $aggregated,
                        'leaf_predictions' => $leafPredictions,
                        'bboxes_count' => count($bboxes),
                        'unknown_leaves' => $unknownLeafCount,
                    ],
                    'symptoms' => $symptoms,
                ],
                'provider' => 'vit_local_enhanced',
                'cross_validation' => null,
            ];
        }

        $predictedLabel = $aggregated['predicted_label'];
        $confidence = $aggregated['confidence'] ?? 0;
        $plantName = null;
        $diseaseName = null;

        // Tách plant/disease từ label
        [$plantName, $diseaseName] = $this->splitLabel($predictedLabel);

        // Xác định severity dựa trên độ tin cậy trung bình
        $severity = $this->deriveSeverity($confidence);

        // Trường hợp label là "healthy (...)" -> không bệnh (theo ViT)
        $isHealthyViT = stripos($predictedLabel, 'healthy') !== false
            || strcasecmp($diseaseName, 'healthy') === 0
            || strcasecmp($diseaseName, 'Khỏe mạnh') === 0;

        // Cây khỏe mạnh không thể có severity 'high' chỉ vì confidence cao
        if ($isHealthyViT) {
            $severity = 'none';
        }

        // Bổ sung mô tả + biện pháp:
        $description = '';
        $treatments = [];
        $prevention = [];
        $providerTag = 'vit_local_enhanced';
        $crossValidation = null;

        // Luôn chạy xác nhận chéo với AI cloud (kể cả khi ViT cho là khỏe mạnh)
        // để Gemini đóng vai trò bác sĩ thứ 2 quyết định khỏe/bệnh.
        $infoProvider = (string) config('services.ai_plant_doctor.info_provider', '');
        $kbInfo = $this->callDiseaseInfo($serviceUrl, $token, $timeout, $predictedLabel);

        $enriched = null;
        if (in_array($infoProvider, ['gemini', 'openai'], true)) {
            // Hybrid: ViT chẩn đoán chính + cloud (Gemini/OpenAI) đóng vai trò bác sĩ thứ 2
            $enriched = $this->enrichWithCloud($infoProvider, $image, [
                'predicted_label' => $predictedLabel,
                'confidence' => $confidence,
                'symptoms' => $symptoms,
                'leaf_predictions' => $leafPredictions,
                'top_k' => $aggregated['top_k_aggregated'] ?? [],
                'knowledge_base' => $kbInfo,
                'is_healthy' => $isHealthyViT,
            ]);
        }

        $isHealthy = $isHealthyViT;

        if ($enriched !== null) {
            $description = $enriched['description'] ?? '';
            $treatments = $enriched['treatments'] ?? [];
            $prevention = $enriched['prevention'] ?? [];
            if (!empty($enriched['plant_name'])) {
                $plantName = $enriched['plant_name'];
            }
            // Xác nhận chéo: ưu tiên kết quả AI cloud (Gemini) thay cho nhãn ViT tiếng Anh
            if (!empty($enriched['disease_name'])) {
                $diseaseName = $enriched['disease_name'];
            }
            if (!empty($enriched['severity']) && in_array($enriched['severity'], ['none', 'low', 'medium', 'high', 'critical'], true)) {
                $severity = $enriched['severity'];
            }
            // Cloud quyết định khỏe/bệnh, không theo ViT
            $isHealthy = $this->isHealthyVerdict($diseaseName, $severity);
            $providerTag = 'vit_local_enhanced+' . $infoProvider;
            $rawResponse['info'] = $enriched['raw'] ?? null;
            $crossValidation = [
                'provider' => $infoProvider,
                'agrees' => $enriched['agrees'] ?? null,
                'cloud_confidence' => $enriched['gemini_confidence'] ?? null,
                'vi_predict' => $predictedLabel,
                'cloud_disease' => $enriched['disease_name'] ?? null,
            ];

            // Cloud không đồng ý với ViT -> thông báo đã ưu tiên kết quả cloud
            if ($enriched['agrees'] === false) {
                $description = '[Lưu ý] AI cloud (' . $infoProvider . ') không đồng ý với mô hình local (dự đoán "'
                    . $predictedLabel . '") nên kết quả hiển thị theo AI cloud: "'
                    . ($enriched['disease_name'] ?? 'khác') . '". '
                    . $description;
            }
        } elseif ($isHealthyViT) {
            // Không có cloud (chưa cấu hình key): giữ kết quả khỏe mạnh từ ViT
            $description = 'Cây có vẻ khỏe mạnh, không phát hiện dấu hiệu bệnh trên các lá đã phân tích.';
            $treatments = [];
            $prevention = [
                'Duy trì tưới nước, ánh sáng và dinh dưỡng hợp lý.',
                'Kiểm tra định kỳ để phát hiện sớm dấu hiệu bất thường.',
            ];
        } else {
            // Fallback tầng 2: knowledge base; tầng 3: template
            [$description, $treatments, $prevention] = $this->fallbackDescriptionFor($kbInfo, $predictedLabel);
            $providerTag = 'vit_local_enhanced+kb';
        }

        if ($confidence < $minConfidence) {
            $description = 'Mô hình không tự tin cao về kết quả này (' . round($confidence * 100, 1) . '%). '
                . 'Hãy chụp ảnh rõ nét hơn, lấy gần lá/cành bị bệnh và thử lại. '
                . $description;
        }

        if ($unknownLeafCount > 0) {
            $description = $this->unknownLeafNotice($unknownLeafCount) . "\n\n" . $description;
        }

        $rawResponse['vit'] = [
            'aggregated' => $aggregated,
            'leaf_predictions' => $leafPredictions,
            'bboxes_count' => count($bboxes),
            'unknown_leaves' => $unknownLeafCount,
        ];
        $rawResponse['symptoms'] = $symptoms;
        if ($crossValidation !== null) {
            $rawResponse['cross_validation'] = $crossValidation;
        }

        return [
            'plant_name' => $plantName ?: 'Không xác định',
            'disease_name' => $isHealthy ? 'Khỏe mạnh' : ($diseaseName ?: 'Không xác định'),
            'confidence' => $confidence,
            'severity' => $severity,
            'description' => $description,
            'treatments' => $treatments,
            'prevention' => $prevention,
            'raw_response' => $rawResponse,
            'provider' => $providerTag,
            'cross_validation' => $crossValidation,
        ];
    }

    /**
     * Backward compatibility: Predict without leaf detection (old behavior)
     */
    private function diagnoseWithoutLeafDetection(string $serviceUrl, string $token, int $timeout, UploadedFile $image, ?string $symptoms, int $topK, float $minConfidence): array
    {
        $req = Http::timeout($timeout)->attach(
            'file',
            file_get_contents($image->getRealPath()),
            $image->getClientOriginalName() ?: 'upload.jpg',
            ['Content-Type' => $image->getMimeType() ?: 'image/jpeg'],
        );

        if ($token !== '') {
            $req = $req->withToken($token);
        }

        $response = $req->post("{$serviceUrl}/predict?top_k={$topK}");

        if (!$response->successful()) {
            Log::error('ViT local /predict failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('ViT inference service thất bại: HTTP ' . $response->status());
        }

        $vit = $response->json();
        $predictedLabel = $vit['predicted_label'] ?? null;
        $confidence = (float) ($vit['confidence'] ?? 0);

        if (!$predictedLabel) {
            throw new \RuntimeException('ViT trả về kết quả rỗng');
        }

        [$plantName, $diseaseName] = $this->splitLabel($predictedLabel);

        $rawResponse = [
            'vit' => $vit,
            'symptoms' => $symptoms,
        ];

        $severity = $this->deriveSeverity($confidence);

        // Trường hợp label là "healthy (...)" -> không bệnh (theo ViT)
        $isHealthyViT = stripos($predictedLabel, 'healthy') !== false
            || strcasecmp($diseaseName, 'healthy') === 0
            || strcasecmp($diseaseName, 'Khỏe mạnh') === 0;

        // Cây khỏe mạnh không thể có severity 'high' chỉ vì confidence cao
        if ($isHealthyViT) {
            $severity = 'none';
        }

        $description = '';
        $treatments = [];
        $prevention = [];
        $providerTag = 'vit_local';
        $crossValidation = null;

        // Luôn chạy xác nhận chéo với AI cloud (kể cả khi ViT cho là khỏe mạnh)
        // để Gemini đóng vai trò bác sĩ thứ 2 quyết định khỏe/bệnh.
        $infoProvider = (string) config('services.ai_plant_doctor.info_provider', '');
        $kbInfo = $this->callDiseaseInfo($serviceUrl, $token, $timeout, $predictedLabel);

        $enriched = null;
        if (in_array($infoProvider, ['gemini', 'openai'], true)) {
            $enriched = $this->enrichWithCloud($infoProvider, $image, [
                'predicted_label' => $predictedLabel,
                'confidence' => $confidence,
                'symptoms' => $symptoms,
                'leaf_predictions' => [],
                'top_k' => $vit['top'] ?? [],
                'knowledge_base' => $kbInfo,
                'is_healthy' => $isHealthyViT,
            ]);
        }

        $isHealthy = $isHealthyViT;

        if ($enriched !== null) {
            $description = $enriched['description'] ?? '';
            $treatments = $enriched['treatments'] ?? [];
            $prevention = $enriched['prevention'] ?? [];
            if (!empty($enriched['plant_name'])) {
                $plantName = $enriched['plant_name'];
            }
            // Xác nhận chéo: ưu tiên kết quả AI cloud (Gemini) thay cho nhãn ViT tiếng Anh
            if (!empty($enriched['disease_name'])) {
                $diseaseName = $enriched['disease_name'];
            }
            if (!empty($enriched['severity']) && in_array($enriched['severity'], ['none', 'low', 'medium', 'high', 'critical'], true)) {
                $severity = $enriched['severity'];
            }
            // Cloud quyết định khỏe/bệnh, không theo ViT
            $isHealthy = $this->isHealthyVerdict($diseaseName, $severity);
            $providerTag = 'vit_local+' . $infoProvider;
            $rawResponse['info'] = $enriched['raw'] ?? null;
            $crossValidation = [
                'provider' => $infoProvider,
                'agrees' => $enriched['agrees'] ?? null,
                'cloud_confidence' => $enriched['gemini_confidence'] ?? null,
                'vi_predict' => $predictedLabel,
                'cloud_disease' => $enriched['disease_name'] ?? null,
            ];

            // Cloud không đồng ý với ViT -> thông báo đã ưu tiên kết quả cloud
            if ($enriched['agrees'] === false) {
                $description = '[Lưu ý] AI cloud (' . $infoProvider . ') không đồng ý với mô hình local (dự đoán "'
                    . $predictedLabel . '") nên kết quả hiển thị theo AI cloud: "'
                    . ($enriched['disease_name'] ?? 'khác') . '". '
                    . $description;
            }
        } elseif ($isHealthyViT) {
            // Không có cloud (chưa cấu hình key): giữ kết quả khỏe mạnh từ ViT
            $description = 'Cây có vẻ khỏe mạnh, không phát hiện dấu hiệu bệnh trên ảnh.';
            $treatments = [];
            $prevention = [
                'Duy trì tưới nước, ánh sáng và dinh dưỡng hợp lý.',
                'Kiểm tra định kỳ để phát hiện sớm dấu hiệu bất thường.',
            ];
        } else {
            // Fallback tầng 2: knowledge base; tầng 3: template
            [$description, $treatments, $prevention] = $this->fallbackDescriptionFor($kbInfo, $predictedLabel);
            $providerTag = 'vit_local+kb';
        }

        if ($confidence < $minConfidence) {
            $description = 'Mô hình không tự tin cao về kết quả này (' . round($confidence * 100, 1) . '%). '
                . 'Hãy chụp ảnh rõ nét hơn, lấy gần lá/cành bị bệnh và thử lại. '
                . $description;
        }

        $isUnknown = stripos($predictedLabel, 'unknown') !== false
            || stripos($predictedLabel, 'không xác định') !== false;

        if ($isUnknown) {
            $severity = 'unknown';
            $description = $this->unknownLeafNotice(0) . "\n\n" . $description;
        }

        if ($crossValidation !== null) {
            $rawResponse['cross_validation'] = $crossValidation;
        }

        return [
            'plant_name' => $plantName,
            'disease_name' => $isHealthy ? 'Khỏe mạnh' : $diseaseName,
            'confidence' => $confidence,
            'severity' => $severity,
            'description' => $description,
            'treatments' => $treatments,
            'prevention' => $prevention,
            'raw_response' => $rawResponse,
            'provider' => $providerTag,
            'cross_validation' => $crossValidation,
        ];
    }

    /**
     * Gọi endpoint /detect-leaves để sử dụng Grounding DINO
     */
    private function callDetectLeaves(string $serviceUrl, string $token, int $timeout, UploadedFile $image): array
    {
        $req = Http::timeout($timeout)->attach(
            'file',
            file_get_contents($image->getRealPath()),
            $image->getClientOriginalName() ?: 'upload.jpg',
            ['Content-Type' => $image->getMimeType() ?: 'image/jpeg'],
        );

        if ($token !== '') {
            $req = $req->withToken($token);
        }

        $response = $req->post("{$serviceUrl}/detect-leaves");

        if (!$response->successful()) {
            Log::warning('Detect leaves failed', ['status' => $response->status(), 'body' => $response->body()]);
            return ['bboxes' => []];
        }

        $data = $response->json();

        return is_array($data) ? $data : ['bboxes' => []];
    }

    /**
     * Gọi endpoint /crop và sau đó /predict-leaf cho một bbox
     */
    private function callCropAndPredict(string $serviceUrl, string $token, int $timeout, UploadedFile $image, array $bbox, int $topK): ?array
    {
        // Step 1: Crop
        $cropResp = Http::timeout($timeout)->attach(
            'file',
            file_get_contents($image->getRealPath()),
            $image->getClientOriginalName() ?: 'upload.jpg',
            ['Content-Type' => $image->getMimeType() ?: 'image/jpeg'],
        );

        if ($token !== '') {
            $cropResp = $cropResp->withToken($token);
        }

        $cropBody = $cropResp->post("{$serviceUrl}/crop", [
            'bbox' => json_encode($bbox),
        ])->json();

        if (isset($cropBody['status']) && $cropBody['status'] !== 'ok') {
            return null;
        }

        if (!isset($cropBody['image_hex'])) {
            return null;
        }

        // Step 2: Predict on cropped image (as binary)
        $hex = $cropBody['image_hex'];
        $bytes = pack('H*', $hex);

        $predictResp = Http::timeout($timeout)->attach(
            'file',
            $bytes,
            'leaf.jpg',
            ['Content-Type' => 'image/jpeg'],
        );

        if ($token !== '') {
            $predictResp = $predictResp->withToken($token);
        }

        $predResp = $predictResp->post("{$serviceUrl}/predict-leaf?top_k={$topK}")->json();

        return [
            'predicted_label' => $predResp['predicted_label'] ?? null,
            'confidence' => $predResp['confidence'] ?? 0,
            'top' => $predResp['top'] ?? [],
        ];
    }

    /**
     * Hybrid enrichment: ViT chẩn đoán chính, cloud (Gemini/OpenAI) đóng vai trò
     * "bác sĩ thứ 2" — xác nhận/điều chỉnh dựa trên ảnh + top-k + lá + KB + triệu chứng.
     *
     * Trả về null khi cloud fail/JSON hỏng để caller fallback (KB → template).
     */
    private function enrichWithCloud(string $provider, UploadedFile $image, array $context): ?array
    {
        $base64 = base64_encode(file_get_contents($image->getRealPath()));
        $mimeType = $image->getMimeType();

        $prompt = $this->buildHybridPrompt($context);

        // Cache kết quả enrich theo provider + label + symptoms để tiết kiệm API.
        // Cache lỗi (redis down...) chỉ bị bỏ qua, không được làm hỏng diagnosis.
        $cacheKey = 'ai_plant_doctor:enrich:' . $provider . ':' . md5(json_encode([
            'label' => $context['predicted_label'] ?? '',
            'symptoms' => $context['symptoms'] ?? '',
        ]));

        try {
            if (Cache::has($cacheKey)) {
                $cached = Cache::get($cacheKey);
                if (is_array($cached)) {
                    return $cached;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AI Plant Doctor: cache read failed, bỏ qua cache', ['error' => $e->getMessage()]);
        }

        if ($provider === 'gemini') {
            $raw = $this->callGeminiWithPrompt($base64, $mimeType, $prompt);
        } else {
            $raw = $this->callOpenAIWithPrompt($base64, $mimeType, $prompt);
        }

        $parsed = $this->safeParseJson($raw['text'] ?? '');

        // Retry 1 lần nếu JSON hỏng
        if ($parsed === null && !empty($raw['text'])) {
            Log::warning("AI Plant Doctor: {$provider} trả về JSON không hợp lệ, retry lần 2", ['text' => $raw['text']]);
            $retryPrompt = $prompt . "\n\nCHÚ Ý: Phản hồi trước đó của bạn không phải JSON hợp lệ. "
                . 'Chỉ trả về duy nhất một JSON object hợp lệ, không dùng markdown, không kèm giải thích.';

            if ($provider === 'gemini') {
                $raw = $this->callGeminiWithPrompt($base64, $mimeType, $retryPrompt);
            } else {
                $raw = $this->callOpenAIWithPrompt($base64, $mimeType, $retryPrompt);
            }
            $parsed = $this->safeParseJson($raw['text'] ?? '');
        }

        // JSON hỏng hoàn toàn -> fallback (KB → template)
        if ($parsed === null || empty($parsed)) {
            Log::warning("AI Plant Doctor: Không lấy được kết quả từ {$provider}, dùng fallback");
            return null;
        }

        // Model có thể trả thiếu field -> lấp từ context để không mất dữ liệu
        [, $viDisease] = $this->splitLabel($context['predicted_label'] ?? '');
        $result = [
            'plant_name' => $parsed['plant_name'] ?? null,
            'disease_name' => $parsed['disease_name'] ?? $viDisease,
            'severity' => $parsed['severity'] ?? null,
            'description' => $parsed['description'] ?? '',
            'treatments' => is_array($parsed['treatments'] ?? null) ? $parsed['treatments'] : [],
            'prevention' => is_array($parsed['prevention'] ?? null) ? $parsed['prevention'] : [],
            'agrees' => $parsed['agrees'] ?? true,
            'gemini_confidence' => (float) ($parsed['gemini_confidence'] ?? 0),
            'raw' => $raw['body'] ?? null,
        ];

        // Cache kết quả đã parse (không cache raw body để tiết kiệm bộ nhớ)
        try {
            $cached = $result;
            unset($cached['raw']);
            Cache::put($cacheKey, $cached, now()->addDays(7));
        } catch (\Throwable $e) {
            Log::warning('AI Plant Doctor: cache write failed, bỏ qua cache', ['error' => $e->getMessage()]);
        }

        return $result;
    }

    /**
     * Xây dựng prompt giàu ngữ cảnh cho hybrid diagnosis.
     */
    private function buildHybridPrompt(array $context): string
    {
        $predictedLabel = $context['predicted_label'] ?? 'Không xác định';
        $confidence = round((float) ($context['confidence'] ?? 0) * 100, 1);
        $symptoms = $context['symptoms'] ?? null;
        $leafPredictions = $context['leaf_predictions'] ?? [];
        $topK = $context['top_k'] ?? [];
        $kb = $context['knowledge_base'] ?? null;

        $lines = [];
        $lines[] = 'Bạn là bác sĩ chuyên khoa bệnh học thực vật. Phân tích ảnh cây kèm kết quả dự đoán từ mô hình AI local dưới đây, sau đó xác nhận hoặc điều chỉnh chẩn đoán cho đúng nhất.';

        $lines[] = '';
        $lines[] = '=== Kết quả mô hình ViT local (best_vit.keras, 71 classes) ===';
        $lines[] = "- Dự đoán chính: {$predictedLabel} (độ tin cậy: {$confidence}%)";

        if (!empty($context['is_healthy'])) {
            $lines[] = '- Mô hình local cho rằng cây KHỎE MẠNH (không phát hiện bệnh). Hãy quan sát kỹ ảnh để xác nhận hoặc bác bỏ.';
        }

        if (!empty($topK)) {
            $lines[] = '- Top candidates:';
            foreach ($topK as $i => $cand) {
                $label = $cand['label'] ?? '';
                $avg = isset($cand['avg_confidence']) ? round((float) $cand['avg_confidence'] * 100, 1) . '%' : '';
                $votes = $cand['votes'] ?? 0;
                $lines[] = "  {$i}. {$label} (avg_confidence: {$avg}, votes: {$votes})";
            }
        }

        if (!empty($leafPredictions)) {
            $lines[] = '- Kết quả từng lá đã phân tích:';
            foreach ($leafPredictions as $lp) {
                $leafId = $lp['leaf_id'] ?? '?';
                $label = $lp['predicted_label'] ?? '?';
                $conf = isset($lp['confidence']) ? round((float) $lp['confidence'] * 100, 1) . '%' : '?';
                $lines[] = "  - Lá {$leafId}: {$label} ({$conf})";
            }
        }

        if (!empty($symptoms)) {
            $lines[] = '';
            $lines[] = "=== Triệu chứng người dùng mô tả ===\n{$symptoms}";
        }

        if (is_array($kb) && !empty($kb['found_in_kb'])) {
            $lines[] = '';
            $lines[] = '=== Tham khảo knowledge base nội bộ (chỉ mang tính tham khảo) ===';
            $lines[] = '- Tên tiếng Việt: ' . ($kb['disease_name'] ?? '');
            $lines[] = '- Mô tả: ' . ($kb['description'] ?? '');
            $lines[] = '- Biện pháp xử lý: ' . implode('; ', $kb['treatments'] ?? []);
            $lines[] = '- Phòng ngừa: ' . implode('; ', $kb['prevention'] ?? []);
        }

        $lines[] = '';
        $lines[] = 'Quan sát kỹ ảnh. Nếu hình ảnh không khớp với dự đoán của mô hình, hãy đưa ra chẩn đoán đúng nhất của bạn.';
        $lines[] = '';
        $lines[] = 'Trả về DUY NHẤT một JSON hợp lệ với cấu trúc:';
        $lines[] = '{';
        $lines[] = '    "plant_name": "Tên cây (tiếng Việt nếu có)",';
        $lines[] = '    "disease_name": "Tên bệnh hoặc Khỏe mạnh (tiếng Việt)",';
        $lines[] = '    "agrees": true,';
        $lines[] = '    "gemini_confidence": 0.0,';
        $lines[] = '    "severity": "none|low|medium|high|critical",';
        $lines[] = '    "description": "Mô tả chi tiết bằng tiếng Việt, dựa trên ảnh + kết quả mô hình + triệu chứng",';
        $lines[] = '    "treatments": ["Biện pháp 1", "Biện pháp 2"],';
        $lines[] = '    "prevention": ["Phòng ngừa 1", "Phòng ngừa 2"]';
        $lines[] = '}';
        $lines[] = '';
        $lines[] = '- "agrees": true nếu bạn đồng ý với dự đoán của mô hình local, false nếu bạn cho rằng mô hình sai.';
        $lines[] = '- "gemini_confidence": độ tin cậy của riêng bạn (0.0-1.0) khi quan sát ảnh.';
        $lines[] = '- Cụ thể, thực tế và an toàn. Nếu cây khỏe mạnh, nói rõ.';
        $lines[] = '- Không dùng markdown, không kèm text ngoài JSON.';

        return implode("\n", $lines);
    }

    /**
     * Parse JSON chịu lỗi: strip markdown code fence, trích JSON object đầu tiên.
     */
    private function safeParseJson(string $text): ?array
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }

        // Strip markdown code fences ```json ... ```
        if (preg_match('/```(?:json)?\s*(.*?)```/is', $text, $m)) {
            $text = trim($m[1]);
        }

        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Lấy JSON object đầu tiên nếu còn text thừa
        if (preg_match('/\{.*\}/s', $text, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Lấy thông tin bệnh từ knowledge base của ViT microservice.
     */
    private function callDiseaseInfo(string $serviceUrl, string $token, int $timeout, string $label): ?array
    {
        try {
            $req = Http::timeout($timeout)->acceptJson();
            if ($token !== '') {
                $req = $req->withToken($token);
            }

            $response = $req->get($serviceUrl . '/disease-info/' . rawurlencode($label));

            if (!$response->successful()) {
                Log::warning('Fetch disease-info failed', ['status' => $response->status(), 'label' => $label]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::warning('Fetch disease-info exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fallback 3 tầng: knowledge base (nếu có) → template chung.
     */
    private function fallbackDescriptionFor(?array $kbInfo, string $label): array
    {
        if (is_array($kbInfo) && !empty($kbInfo['found_in_kb'])) {
            return [
                $kbInfo['description'] ?? '',
                $kbInfo['treatments'] ?? [],
                $kbInfo['prevention'] ?? [],
            ];
        }

        return $this->defaultDescriptionFor($label);
    }

    /**
     * Aggregate predictions from multiple leaves using major voting
     */
    private function aggregateLeafPredictions(array $predictions, int $topK): array
    {
        if (empty($predictions)) {
            return ['predicted_label' => '', 'confidence' => 0];
        }

        // Count labels
        $labelCounts = [];
        $labelConfidences = [];

        foreach ($predictions as $pred) {
            $label = $pred['predicted_label'];
            $conf = $pred['confidence'] ?? 0;

            $labelCounts[$label] = ($labelCounts[$label] ?? 0) + 1;
            $labelConfidences[$label] = ($labelConfidences[$label] ?? 0) + $conf;
        }

        // Calculate average confidence per label
        $avgConfidences = [];
        foreach ($labelCounts as $label => $count) {
            $avgConfidences[$label] = $labelConfidences[$label] / $count;
        }

        // Find dominant (majority vote, tie-break by avg confidence)
        arsort($labelCounts);
        $mostCommon = array_keys($labelCounts);
        $dominantLabel = $mostCommon[0] ?? '';

        // Re-sort by avg confidence for tie-breaking
        arsort($avgConfidences);
        $sortedByConf = array_keys($avgConfidences);
        $finalLabel = $sortedByConf[0] ?? $dominantLabel;
        $finalConf = $avgConfidences[$finalLabel] ?? 0;

        return [
            'predicted_label' => $finalLabel,
            'confidence' => $finalConf,
            'top_k_aggregated' => $this->buildTopKAggregated($avgConfidences, $labelCounts, $predictions, $topK),
        ];
    }

    private function buildTopKAggregated(array $avgConfidences, array $labelCounts, array $predictions, int $topK): array
    {
        arsort($avgConfidences);
        $top = array_slice($avgConfidences, 0, $topK, true);

        return array_map(function($conf, $label) use ($labelCounts, $predictions) {
            $predLabels = array_column($predictions, 'predicted_label');
            $voteCount = array_count_values($predLabels)[$label] ?? 0;
            return ['label' => $label, 'avg_confidence' => $conf, 'votes' => $voteCount];
        }, $top, array_keys($top));
    }

    /**
     * Tách "Bacterial spot (Tomato)" -> [plant="Tomato", disease="Bacterial spot"].
     * Nếu label không có ngoặc -> plant=null, disease=string gọn.
     */
    private function splitLabel(string $label): array
    {
        if (preg_match('/^(.*?)\s*\((.+?)\)\s*$/', $label, $m)) {
            $disease = trim($m[1]);
            $plant = trim($m[2]);
            // Một số label dataset tên plant có dấu phẩy (vd "Pepper, bell")
            $plant = str_replace([',', '  '], ['', ' '], $plant);
            return [$plant, $disease];
        }
        return [null, $label];
    }

    private function deriveSeverity(float $confidence): string
    {
        if ($confidence >= 0.85) return 'high';
        if ($confidence >= 0.65) return 'medium';
        if ($confidence >= 0.45) return 'low';
        return 'unknown';
    }

    /**
     * Template fallback khi không có info_provider cloud.
     */
    private function defaultDescriptionFor(string $label): array
    {
        $description = "Mô hình phát hiện dấu hiệu của '{$label}' trên ảnh. ";
        $description .= 'Khuyến nghị tham khảo chuyên gia nông nghiệp hoặc phòng bảo vệ thực vật địa phương để xác nhận và có phác đồ xử lý phù hợp.';

        $treatments = [
            'Cách ly cây bị bệnh nhằm tránh lây lan sang cây khác.',
            'Loại bỏ lá/cánh bệnh nặng và tiêu hủy đúng cách.',
            'Tham khảo chuyên gia/phòng trừ sâu bệnh để chọn thuốc đặc trị phù hợp.',
        ];

        $prevention = [
            'Theo dõi cây thường xuyên để phát hiện sớm dấu hiệu bệnh.',
            'Đảm bảo thông gió, độ ẩm và ánh sáng hợp lý cho cây.',
            'Vệ sinh dụng cụ làm vườn sau khi tiếp xúc cây bệnh.',
        ];

        return [$description, $treatments, $prevention];
    }

    /**
     * Thông báo khi có lá không xác định được bệnh (unknown):
     * khuyến nghị đưa mẫu vật cho người có thẩm quyền kiểm tra.
     */
    private function unknownLeafNotice(int $count): string
    {
        $leafPart = $count > 0 ? "{$count} lá không xác định được bệnh (unknown). " : '';

        return $leafPart . 'Khuyến nghị đưa mẫu vật cho người có thẩm quyền '
            . '(chuyên gia nông nghiệp, cán bộ bảo vệ thực vật) để kiểm tra và xác nhận chẩn đoán.';
    }

    /**
     * Quyết định khỏe/bệnh theo phán quyết của AI cloud (Gemini/OpenAI).
     */
    private function isHealthyVerdict(string $diseaseName, string $severity): bool
    {
        return strcasecmp($diseaseName, 'Khỏe mạnh') === 0
            || strcasecmp($diseaseName, 'healthy') === 0
            || stripos($diseaseName, 'khỏe') !== false
            || ($severity === 'none' && strcasecmp($diseaseName, 'Không xác định') !== 0);
    }

    private function callGeminiWithPrompt(string $base64, string $mimeType, string $prompt): array
    {
        $apiKey = config('services.ai_plant_doctor.gemini_api_key');
        $model = config('services.ai_plant_doctor.gemini_model', 'gemini-3.1-flash-lite');
        if (!$apiKey) {
            return ['text' => '{}', 'body' => null];
        }

        $body = ['text' => '{}', 'body' => null];
        $maxAttempts = 2;
        $attempt = 0;

        do {
            $attempt++;
            try {
                $response = Http::timeout(90)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                    [
                        'contents' => [[
                            'parts' => [
                                ['text' => $prompt],
                                ['inline_data' => ['mime_type' => $mimeType, 'data' => $base64]],
                            ],
                        ]],
                        'generationConfig' => [
                            'temperature' => 0.4,
                            'maxOutputTokens' => 2048,
                            'responseMimeType' => 'application/json',
                            'responseSchema' => [
                                'type' => 'OBJECT',
                                'required' => ['plant_name', 'disease_name', 'agrees', 'severity', 'description', 'treatments', 'prevention'],
                                'properties' => [
                                    'plant_name' => ['type' => 'STRING'],
                                    'disease_name' => ['type' => 'STRING'],
                                    'agrees' => ['type' => 'BOOLEAN'],
                                    'gemini_confidence' => ['type' => 'NUMBER'],
                                    'severity' => ['type' => 'STRING', 'enum' => ['none', 'low', 'medium', 'high', 'critical']],
                                    'description' => ['type' => 'STRING'],
                                    'treatments' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                                    'prevention' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                                ],
                            ],
                        ],
                    ]
                );

                if ($response->successful()) {
                    $rbody = $response->json();
                    return [
                        'text' => $rbody['candidates'][0]['content']['parts'][0]['text'] ?? '{}',
                        'body' => $rbody,
                    ];
                }

                // 429/503 = quá tải -> thử lại sau 3 giây
                $retryable = in_array($response->status(), [429, 503], true);
                if ($retryable && $attempt < $maxAttempts) {
                    Log::warning("Gemini (enrich) quá tải HTTP {$response->status()}, thử lại lần {$attempt}", ['model' => $model]);
                    sleep(3);
                    continue;
                }

                Log::error('Gemini (enrich) error: ' . $response->body());
                $body = ['text' => '{}', 'body' => $response->json()];
            } catch (\Throwable $e) {
                Log::warning('Gemini (enrich) connection error: ' . $e->getMessage());
                if ($attempt < $maxAttempts) {
                    sleep(3);
                    continue;
                }
                $body = ['text' => '{}', 'body' => null];
            }
            break;
        } while ($attempt < $maxAttempts);

        return $body;
    }

    private function callOpenAIWithPrompt(string $base64, string $mimeType, string $prompt): array
    {
        $apiKey = config('services.ai_plant_doctor.openai_api_key');
        $model = config('services.ai_plant_doctor.openai_model', 'gpt-4o');
        if (!$apiKey) {
            return ['text' => '{}', 'body' => null];
        }

        $response = Http::withToken($apiKey)->timeout(60)->post(
            'https://api.openai.com/v1/chat/completions',
            [
                'model' => $model,
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        ['type' => 'image_url', 'image_url' => [
                            'url' => "data:{$mimeType};base64,{$base64}", 'detail' => 'high',
                        ]],
                    ],
                ]],
                'temperature' => 0.4,
                'max_tokens' => 1024,
                'response_format' => ['type' => 'json_object'],
            ]
        );

        if (!$response->successful()) {
            Log::error('OpenAI (enrich) error: ' . $response->body());
            return ['text' => '{}', 'body' => $response->json()];
        }

        $body = $response->json();
        return [
            'text' => $body['choices'][0]['message']['content'] ?? '{}',
            'body' => $body,
        ];
    }

    private function callGemini(string $base64, string $mimeType, ?string $symptoms): array
    {
        $apiKey = config('services.ai_plant_doctor.gemini_api_key');
        $model = config('services.ai_plant_doctor.gemini_model', 'gemini-3.1-flash-lite');

        if (!$apiKey) {
            throw new \RuntimeException('Gemini API key not configured (AI_PLANT_DOCTOR_GEMINI_API_KEY)');
        }

        $prompt = $this->buildPrompt($symptoms);

        $maxAttempts = 2;
        $attempt = 0;

        do {
            $attempt++;
            try {
                $response = Http::timeout(90)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                    [
                        'contents' => [[
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $base64,
                                    ],
                                ],
                            ],
                        ]],
                        'generationConfig' => [
                            'temperature' => 0.4,
                            'maxOutputTokens' => 2048,
                            'responseMimeType' => 'application/json',
                            'responseSchema' => [
                                'type' => 'OBJECT',
                                'properties' => [
                                    'plant_name' => ['type' => 'STRING'],
                                    'disease_name' => ['type' => 'STRING'],
                                    'confidence' => ['type' => 'NUMBER'],
                                    'severity' => ['type' => 'STRING', 'enum' => ['none', 'low', 'medium', 'high', 'critical']],
                                    'description' => ['type' => 'STRING'],
                                    'treatments' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                                    'prevention' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                                ],
                            ],
                        ],
                    ]
                );

                if ($response->successful()) {
                    $body = $response->json();
                    $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

                    return $this->parseResponse($text, 'gemini', $body);
                }

                // 429/503 = quá tải -> thử lại sau 3 giây
                if (in_array($response->status(), [429, 503], true) && $attempt < $maxAttempts) {
                    Log::warning("Gemini API quá tải HTTP {$response->status()}, thử lại lần {$attempt}", ['model' => $model]);
                    sleep(3);
                    continue;
                }

                Log::error('Gemini API error: ' . $response->body());
                throw new \RuntimeException('Gemini API request failed: ' . $response->status());
            } catch (\Throwable $e) {
                if ($e instanceof \RuntimeException) {
                    throw $e;
                }
                Log::warning('Gemini API connection error: ' . $e->getMessage());
                if ($attempt < $maxAttempts) {
                    sleep(3);
                    continue;
                }
                throw new \RuntimeException('Gemini API request failed: ' . $e->getMessage());
            }
            break;
        } while ($attempt < $maxAttempts);

        throw new \RuntimeException('Gemini API request failed');
    }

    private function callOpenAI(string $base64, string $mimeType, ?string $symptoms): array
    {
        $apiKey = config('services.ai_plant_doctor.openai_api_key');
        $model = config('services.ai_plant_doctor.openai_model', 'gpt-4o');

        if (! $apiKey) {
            throw new \RuntimeException('OpenAI API key not configured (AI_PLANT_DOCTOR_OPENAI_API_KEY)');
        }

        $prompt = $this->buildPrompt($symptoms);

        $response = Http::withToken($apiKey)->timeout(60)->post(
            'https://api.openai.com/v1/chat/completions',
            [
                'model' => $model,
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$base64}",
                                'detail' => 'high',
                            ],
                        ],
                    ],
                ]],
                'temperature' => 0.4,
                'max_tokens' => 2048,
                'response_format' => ['type' => 'json_object'],
            ]
        );

        if (!$response->successful()) {
            Log::error('OpenAI API error: ' . $response->body());
            throw new \RuntimeException('OpenAI API request failed: ' . $response->status());
        }

        $body = $response->json();
        $text = $body['choices'][0]['message']['content'] ?? '{}';

        return $this->parseResponse($text, 'openai', $body);
    }

    private function buildPrompt(?string $symptoms): string
    {
        $extra = $symptoms
            ? "\n\nAdditional symptoms described by the user: {$symptoms}"
            : '';

        return <<<PROMPT
You are an expert plant pathologist and agricultural advisor. Analyze this plant image and provide a diagnosis.

Respond in JSON format with the following structure:
{
    "plant_name": "Common name of the plant (in Vietnamese if possible)",
    "disease_name": "Name of the disease or 'Healthy' if no disease detected (in Vietnamese)",
    "confidence": 0.0-1.0,
    "severity": "none|low|medium|high|critical",
    "description": "Detailed description of the diagnosis in Vietnamese",
    "treatments": [
        "Treatment step 1 in Vietnamese",
        "Treatment step 2 in Vietnamese"
    ],
    "prevention": [
        "Prevention tip 1 in Vietnamese",
        "Prevention tip 2 in Vietnamese"
    ]
}
{$extra}

Be specific and practical. If the plant appears healthy, say so clearly.
PROMPT;
    }

    private function parseResponse(string $text, string $provider, array $rawResponse): array
    {
        $parsed = json_decode($text, true);

        if (!$parsed || !isset($parsed['disease_name'])) {
            Log::warning("AI Plant Doctor: Failed to parse {$provider} response", ['text' => $text]);

            return [
                'plant_name' => null,
                'disease_name' => 'Không thể phân tích',
                'confidence' => 0,
                'severity' => 'unknown',
                'description' => 'Không thể phân tích hình ảnh. Vui lòng thử lại với ảnh rõ nét hơn.',
                'treatments' => [],
                'prevention' => [],
                'raw_response' => $rawResponse,
                'provider' => $provider,
            ];
        }

        return [
            'plant_name' => $parsed['plant_name'] ?? null,
            'disease_name' => $parsed['disease_name'] ?? 'Không xác định',
            'confidence' => (float) ($parsed['confidence'] ?? 0),
            'severity' => $parsed['severity'] ?? 'unknown',
            'description' => $parsed['description'] ?? '',
            'treatments' => $parsed['treatments'] ?? [],
            'prevention' => $parsed['prevention'] ?? [],
            'raw_response' => $rawResponse,
            'provider' => $provider,
        ];
    }
}
