<?php

namespace App\Modules\AgriVerse\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Modules\AgriVerse\Services\RateLimitService;

class ApiRateLimit
{
    protected RateLimitService $rateLimitService;

    public function __construct(RateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

    public function handle(Request $request, Closure $next, string $key = 'api', int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        if ($this->rateLimitService->isLimited($request, $key, $maxAttempts, $decayMinutes)) {
            $retryAfter = $this->rateLimitService->getRetryAfter($request, $key);

            return response()->json([
                'message' => 'Quá nhiều yêu cầu. Vui lòng thử lại sau.',
                'retry_after' => $retryAfter,
            ], 429)->withHeaders([
                'Retry-After' => $retryAfter,
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        $this->rateLimitService->hit($request, $key, $decayMinutes);

        $response = $next($request);

        // Add rate limit headers
        $headers = $this->rateLimitService->getHeaders($request, $key, $maxAttempts);

        foreach ($headers as $header => $value) {
            $response->headers->set($header, $value);
        }

        return $response;
    }
}
