<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\EmailVerificationMail;

class EmailVerificationService
{
    protected int $codeLength = 6;
    protected int $codeExpiry = 60; // minutes
    protected int $maxAttempts = 5;

    /**
     * Generate and send verification code
     */
    public function sendVerificationCode(User $user): bool
    {
        $code = $this->generateCode();
        $cacheKey = "email_verification:{$user->id}";

        Cache::put($cacheKey, [
            'code' => $code,
            'attempts' => 0,
            'created_at' => now()->timestamp,
        ], now()->addMinutes($this->codeExpiry));

        try {
            Mail::to($user->email)->send(new EmailVerificationMail($user, $code));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verify the code
     */
    public function verifyCode(User $user, string $code): array
    {
        $cacheKey = "email_verification:{$user->id}";
        $data = Cache::get($cacheKey);

        if (!$data) {
            return ['success' => false, 'message' => 'Mã xác thực đã hết hạn. Vui lòng yêu cầu mã mới.'];
        }

        if ($data['attempts'] >= $this->maxAttempts) {
            Cache::forget($cacheKey);
            return ['success' => false, 'message' => 'Đã hết số lần thử. Vui lòng yêu cầu mã mới.'];
        }

        if ($data['code'] !== $code) {
            Cache::put($cacheKey, [
                ...$data,
                'attempts' => $data['attempts'] + 1,
            ], now()->addMinutes($this->codeExpiry));

            $remaining = $this->maxAttempts - ($data['attempts'] + 1);
            return [
                'success' => false,
                'message' => "Mã xác thực không đúng. Còn {$remaining} lần thử.",
            ];
        }

        // Mark user as verified
        $user->update(['email_verified_at' => now()]);
        Cache::forget($cacheKey);

        return ['success' => true, 'message' => 'Xác thực email thành công!'];
    }

    /**
     * Check if user is verified
     */
    public function isVerified(User $user): bool
    {
        return !is_null($user->email_verified_at);
    }

    /**
     * Generate random code
     */
    protected function generateCode(): string
    {
        $min = pow(10, $this->codeLength - 1);
        $max = pow(10, $this->codeLength) - 1;
        return (string) random_int($min, $max);
    }
}
