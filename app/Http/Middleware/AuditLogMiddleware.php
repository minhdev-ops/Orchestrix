<?php

namespace App\Http\Middleware;

use App\Modules\AgriVerse\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;

class AuditLogMiddleware
{
    public function __construct(protected AuditLogService $auditLog) {}

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->auditLog->logAdminAction(
                action: "{$request->method()} {$request->path()}",
                details: [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'input' => $request->except(['password', 'password_confirmation', '_token']),
                    'status' => $response->status(),
                ],
            );
        }

        return $response;
    }
}
