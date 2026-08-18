<?php

namespace App\Modules\AgriVerse\Services;

use App\Modules\AgriVerse\Models\SupportFaq;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIExpertService
{
    public function __construct(
        private PlantDiagnosisService $diagnosis
    ) {
    }

    public function chat(string $message): array
    {
        $apiKey = config('services.ai_plant_doctor.gemini_api_key');
        $model = config('services.ai_plant_doctor.gemini_model', 'gemini-2.0-flash');

        if (! $apiKey) {
            return $this->localAnswer($message);
        }

        $system = <<<PROMPT
Bạn là chuyên gia cây cảnh & bonsai Việt Nam (thuộc nền tảng AgriVerse). Trả lời bằng tiếng Việt, ngắn gọn và thực tế theo cấu trúc:
1) Nhận định ngắn gọn
2) Nguyên nhân có thể
3) Cách xử lý (liệt kê gạch đầu dòng)
Nếu câu hỏi vượt ngoài chuyên môn cây cảnh/bonsai hoặc bạn không chắc chắn về câu trả lời, hãy trả lời chính xác cụm từ: "Tôi chưa rõ, vui lòng hỏi chuyên gia trên diễn đàn."
PROMPT;

        try {
            $response = Http::timeout(60)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                [
                    'contents' => [[
                        'parts' => [
                            ['text' => $system."\n\nCâu hỏi của người dùng: ".$message],
                        ],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'maxOutputTokens' => 1024,
                    ],
                ]
            );

            if (! $response->successful()) {
                Log::error('AI Expert API error: '.$response->body());

                return ['answered' => false, 'reply' => null, 'reason' => 'error'];
            }

            $body = $response->json();
            $text = trim($body['candidates'][0]['content']['parts'][0]['text'] ?? '');

            if ($text === '' || mb_stripos($text, 'Tôi chưa rõ, vui lòng hỏi chuyên gia trên diễn đàn.') !== false) {
                return ['answered' => false, 'reply' => null, 'reason' => 'no_answer'];
            }

            return ['answered' => true, 'reply' => $text, 'reason' => null];
        } catch (\Throwable $e) {
            Log::error('AI Expert exception: '.$e->getMessage());

            return ['answered' => false, 'reply' => null, 'reason' => 'error'];
        }
    }

    /**
     * Offline decision-tree + FAQ fallback, used when the Gemini key is not configured.
     * Keeps common plant-care questions answerable even without external AI.
     */
    private function localAnswer(string $message): array
    {
        $norm = $this->normalize($message);

        // 1) Symptom-driven diagnosis via decision tree
        $symptomMap = [
            'vang' => 'Vàng Lá',
            'rung la' => 'Rụng Lá',
            'dau la nau|chay ket|kho dau' => 'Đầu Lá Nâu',
            'cham phat trien|lon cham|dung phat trien' => 'Chậm Phát Triển',
            'la dom|dom la' => 'Lá Đốm',
            'moc trang|nam trang|powdery' => 'Mốc Trắng',
        ];

        $detected = [];
        foreach ($symptomMap as $patterns => $symptom) {
            foreach (explode('|', $patterns) as $p) {
                if (str_contains($norm, trim($p))) {
                    $detected[] = $symptom;
                    break;
                }
            }
        }

        if (! empty($detected)) {
            $result = $this->diagnosis->analyze($detected);

            if (! empty($result['matched'])) {
                $d = $result['diagnosis'];
                $treatments = implode("\n", array_map(fn ($t) => "• {$t}", $d['treatments']));
                $reply = "1) Nhận định:\n{$d['disease_name']} được xác định với độ tin cậy ".round($d['confidence'] * 100)."%.\n\n"
                    ."2) Nguyên nhân có thể:\n".implode("\n", array_map(fn ($c) => "• {$c}", $d['causes']))."\n\n"
                    ."3) Cách xử lý:\n{$treatments}\n\n"
                    ."Lưu ý chăm sóc: {$d['care']}";

                return ['answered' => true, 'reply' => $reply, 'reason' => 'decision_tree'];
            }
        }

        // 2) FAQ keyword match
        $faq = SupportFaq::where('is_published', true)->orderBy('sort_order')->get();
        foreach ($faq as $q) {
            if ($this->questionMatches($q->question, $norm)) {
                return ['answered' => true, 'reply' => $q->answer, 'reason' => 'faq'];
            }
        }

        return ['answered' => false, 'reply' => null, 'reason' => 'no_local_answer'];
    }

    private function questionMatches(string $question, string $normMessage): bool
    {
        $qNorm = $this->normalize($question);
        // Use a couple of significant words from the question to match loosely.
        $words = array_values(array_unique(preg_split('/\s+/', $qNorm) ?: []));
        $significant = array_filter($words, fn ($w) => mb_strlen($w) > 2);

        $hits = 0;
        foreach ($significant as $w) {
            if (str_contains($normMessage, $w)) {
                $hits++;
            }
        }

        $need = max(1, (int) ceil(count($significant) * 0.4));

        return $hits >= $need;
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = mb_strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value);

        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }
}
