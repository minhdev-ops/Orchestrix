<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\Http;

class WAQIService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.waqi.base_url', 'https://api.waqi.info');
        $this->token = config('services.waqi.token', '');
    }

    public function getByCoordinates(float $lat, float $lng): ?array
    {
        if (empty($this->token)) {
            return null;
        }

        $res = Http::get("{$this->baseUrl}/feed/geo:{$lat};{$lng}/", [
            'token' => $this->token,
        ]);

        if (!$res->successful() || $res->json('status') !== 'ok') {
            return null;
        }

        return $this->format($res->json('data'));
    }

    public function getByCity(string $city): ?array
    {
        if (empty($this->token)) {
            return null;
        }

        $res = Http::get("{$this->baseUrl}/feed/{$city}/", [
            'token' => $this->token,
        ]);

        if (!$res->successful() || $res->json('status') !== 'ok') {
            return null;
        }

        return $this->format($res->json('data'));
    }

    protected function format(?array $data): ?array
    {
        if (!$data || !isset($data['aqi'])) {
            return null;
        }

        $iaqi = $data['iaqi'] ?? [];

        return [
            'aqi' => (int) $data['aqi'],
            'level' => $this->level((int) $data['aqi']),
            'color' => $this->color((int) $data['aqi']),
            'city' => $data['city']['name'] ?? 'Unknown',
            'lat' => $data['city']['geo'][0] ?? null,
            'lng' => $data['city']['geo'][1] ?? null,
            'pm25' => $iaqi['pm25']['v'] ?? null,
            'pm10' => $iaqi['pm10']['v'] ?? null,
            'o3' => $iaqi['o3']['v'] ?? null,
            'no2' => $iaqi['no2']['v'] ?? null,
            'so2' => $iaqi['so2']['v'] ?? null,
            'co' => $iaqi['co']['v'] ?? null,
            'temperature' => $iaqi['t']['v'] ?? null,
            'humidity' => $iaqi['h']['v'] ?? null,
            'wind' => $iaqi['w']['v'] ?? null,
            'updated_at' => $data['time']['iso'] ?? null,
        ];
    }

    protected function level(int $aqi): string
    {
        return match (true) {
            $aqi <= 50 => 'good',
            $aqi <= 100 => 'moderate',
            $aqi <= 150 => 'unhealthy_sensitive',
            $aqi <= 200 => 'unhealthy',
            $aqi <= 300 => 'very_unhealthy',
            default => 'hazardous',
        };
    }

    protected function color(int $aqi): string
    {
        return match (true) {
            $aqi <= 50 => '#00E400',
            $aqi <= 100 => '#FFFF00',
            $aqi <= 150 => '#FF7E00',
            $aqi <= 200 => '#FF0000',
            $aqi <= 300 => '#8F3F97',
            default => '#7E0023',
        };
    }
}
