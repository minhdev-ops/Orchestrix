<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IpWhitelistMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $whitelist = explode(',', config('app.admin_ip_whitelist', ''));

        if (! empty($whitelist[0]) && ! in_array($request->ip(), $whitelist)) {
            abort(403, 'Access denied: IP not whitelisted.');
        }

        return $next($request);
    }
}
