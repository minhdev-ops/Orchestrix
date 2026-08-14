<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Http\UploadedFile;
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
        $base64 = base64_encode(file_get_contents($image->getRealPath()));
        $mimeType = $image->getMimeType();

        return match ($this->provider) {
            'gemini' => $this->callGemini($base64, $mimeType, $symptoms),
            'openai' => $this->callOpenAI($base64, $mimeType, $symptoms),
            default => throw new \RuntimeException("Unsupported AI provider: {$this->provider}"),
        };
    }

    private function callGemini(string $base64, string $mimeType, ?string $symptoms): array
    {
        $apiKey = config('services.ai_plant_doctor.gemini_api_key');
        $model = config('services.ai_plant_doctor.gemini_model', 'gemini-2.0-flash');

        if (! $apiKey) {
            throw new \RuntimeException('Gemini API key not configured (AI_PLANT_DOCTOR_GEMINI_API_KEY)');
        }

        $prompt = $this->buildPrompt($symptoms);

        $response = Http::timeout(60)->post(
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
                ],
            ]
        );

        if (! $response->successful()) {
            Log::error('Gemini API error: '.$response->body());
            throw new \RuntimeException('Gemini API request failed: '.$response->status());
        }

        $body = $response->json();
        $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

        return $this->parseResponse($text, 'gemini', $body);
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

        if (! $response->successful()) {
            Log::error('OpenAI API error: '.$response->body());
            throw new \RuntimeException('OpenAI API request failed: '.$response->status());
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

        if (! $parsed || ! isset($parsed['disease_name'])) {
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
