<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitService
{
    /**
     * Check if request is rate limited
     */
    public function isLimited(Request $request, string $key, int $maxAttempts = 60, int $decayMinutes = 1): bool
    {
        return RateLimiter::tooManyAttempts($this->getKey($request, $key), $maxAttempts);
    }

    /**
     * Get remaining attempts
     */
    public function getRemainingAttempts(Request $request, string $key, int $maxAttempts = 60): int
    {
        return RateLimiter::remaining($this->getKey($request, $key), $maxAttempts);
    }

    /**
     * Increment rate limiter
     */
    public function hit(Request $request, string $key, int $decayMinutes = 1): void
    {
        RateLimiter::hit($this->getKey($request, $key), $decayMinutes * 60);
    }

    /**
     * Get retry after seconds
     */
    public function getRetryAfter(Request $request, string $key): int
    {
        return RateLimiter::availableIn($this->getKey($request, $key));
    }

    /**
     * Clear rate limit
     */
    public function clear(Request $request, string $key): void
    {
        RateLimiter::clear($this->getKey($request, $key));
    }

    /**
     * Get rate limit key
     */
    protected function getKey(Request $request, string $key): string
    {
        $userId = $request->user()?->id;
        $ip = $request->ip();

        return "{$key}:".($userId ?: $ip);
    }

    /**
     * Login rate limiting (5 attempts per 15 minutes)
     */
    public function isLoginLimited(Request $request): bool
    {
        return $this->isLimited($request, 'login', 5, 15);
    }

    /**
     * API rate limiting (60 requests per minute)
     */
    public function isApiLimited(Request $request): bool
    {
        return $this->isLimited($request, 'api', 60, 1);
    }

    /**
     * Checkout rate limiting (10 requests per minute)
     */
    public function isCheckoutLimited(Request $request): bool
    {
        return $this->isLimited($request, 'checkout', 10, 1);
    }

    /**
     * Password reset rate limiting (3 attempts per hour)
     */
    public function isPasswordResetLimited(Request $request): bool
    {
        return $this->isLimited($request, 'password-reset', 3, 60);
    }

    /**
     * Send verification code rate limiting (3 attempts per 5 minutes)
     */
    public function isVerificationLimited(Request $request): bool
    {
        return $this->isLimited($request, 'verification', 3, 5);
    }

    /**
     * Get rate limit headers
     */
    public function getHeaders(Request $request, string $key, int $maxAttempts = 60): array
    {
        $remaining = $this->getRemainingAttempts($request, $key, $maxAttempts);

        return [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remaining,
            'X-RateLimit-Reset' => now()->addMinutes(1)->timestamp,
        ];
    }
}
