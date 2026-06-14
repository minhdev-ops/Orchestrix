<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Modules\AgriVerse\Providers\AgriVerseServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'checkAdmin' => \App\Http\Middleware\CheckAdminRole::class,
        'permission' => \App\Http\Middleware\CheckPermission::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        'ckfinder.auth' => \App\Http\Middleware\CustomCKFinderAuth::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'ckfinder/*',
            'logout',
            'agriverse/dien-dan',
        ]);
        $middleware->encryptCookies(except: [
            'ckCsrfToken',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
