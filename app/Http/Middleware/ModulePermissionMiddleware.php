<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModulePermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = auth()->user();

        // Admin có toàn quyền
        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        // Kiểm tra quyền cụ thể trong JSON permissions
        $permissions = $user->user_permissions ?? [];
        $requiredPermission = $module.'.manage';

        if (in_array($requiredPermission, $permissions)) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập module này.');
    }
}
