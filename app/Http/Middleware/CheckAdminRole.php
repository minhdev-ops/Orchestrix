<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            $error = 'Bạn không có quyền truy cập trang quản trị.';
            if ($request->inertia()) {
                return Inertia::location(redirect()->route('home')->with('error', $error));
            }
            return redirect()->route('home')->with('error', $error);
        }

        return $next($request);
    }
}
