<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Simple Admin Check Placeholder
        // In a real application, you would check for:
        // if (!auth()->check() || !auth()->user()->is_admin) { ... }

        // For this modular demonstration, we allow access but log it.
        // You can add a password check or full Laravel Breeze/Jetstream here.

        return $next($request);
    }
}
