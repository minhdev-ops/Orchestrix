<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;

class ThrottleLoginMiddleware
{
    public function handle(Request $request, Closure $next, int $maxAttempts = 5, int $decayMinutes = 15)
    {
        $key = 'login:'.($request->input('email') ?? $request->ip());

        $limiter = app(RateLimiter::class);

        if ($limiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $limiter->availableIn($key);

            return back()->withErrors([
                'email' => "Quá nhiều lần thử. Vui lòng thử lại sau {$seconds} giây.",
            ])->with('retry_after', $seconds);
        }

        $response = $next($request);

        if ($response->status() === 302 && session('errors')?->has('email')) {
            $limiter->hit($key, $decayMinutes * 60);
        }

        return $response;
    }
}
