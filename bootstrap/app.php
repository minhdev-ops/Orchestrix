<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuditLogMiddleware;
use App\Http\Middleware\CheckAdminRole;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CustomCKFinderAuth;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IpWhitelistMiddleware;
use App\Modules\AgriVerse\Http\Middleware\ApiRateLimit;
use App\Modules\AgriVerse\Http\Middleware\SeoMiddleware;
use App\Modules\AgriVerse\Providers\AgriVerseServiceProvider;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

$providers = [
    AuthServiceProvider::class,
    AgriVerseServiceProvider::class,
];

if (class_exists('Laravel\Telescope\TelescopeApplicationServiceProvider')) {
    $providers[] = 'App\Providers\TelescopeServiceProvider';
}

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders($providers)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            SeoMiddleware::class,
        ]);
        $middleware->api(prepend: [
            'throttle:api',
        ]);
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'role' => CheckRole::class,
            'checkAdmin' => CheckAdminRole::class,
            'check.permission' => CheckPermission::class,
            'permission' => PermissionMiddleware::class,
            'ip.whitelist' => IpWhitelistMiddleware::class,
            'audit.log' => AuditLogMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'ckfinder.auth' => CustomCKFinderAuth::class,
            'api.rate_limit' => ApiRateLimit::class,
            'seo' => SeoMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'ckfinder/*',
            'agriverse/dien-dan*',
            'login',
            'register',
            'password/*',
            'auth/*',
        ]);
        $middleware->encryptCookies(except: [
            'ckCsrfToken',
            'orchestrix-session',
            'XSRF-TOKEN',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->hasHeader('X-Inertia')) {
                return back()->withErrors([
                    'model' => 'Kích thước file quá lớn (' . ini_get('post_max_size') . ' tối đa). Vui lòng cấu hình lại php.ini (post_max_size và upload_max_filesize) hoặc chọn file nhỏ hơn.'
                ])->withInput();
            }
        });
    })->create();
