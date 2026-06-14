<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CaptchaService
{
    private string $secretKey = '';
    private string $siteKey = '';

    public function __construct()
    {
        $this->secretKey = config('services.captcha.secret_key') ?? '';
        $this->siteKey = config('services.captcha.site_key') ?? '';
    }

    public function verify(string $token, string $remoteIp = null): bool
    {
        if (empty($this->secretKey)) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::timeout(5)->asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret' => $this->secretKey,
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]
            );

            $data = $response->json();

            return $data['success'] ?? false;

        } catch (\Exception $e) {
            \Log::warning('CAPTCHA verification failed: ' . $e->getMessage());
            return true;
        }
    }

    public function getSiteKey(): string
    {
        return $this->siteKey;
    }

    public function isEnabled(): bool
    {
        return !empty($this->secretKey);
    }
}
