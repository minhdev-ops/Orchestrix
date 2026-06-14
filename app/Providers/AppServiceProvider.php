<?php

namespace App\Providers;

use App\Services\ModuleManagerService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleManagerService::class, function ($app) {
            return new ModuleManagerService();
        });
    }

    public function boot(): void
    {
        $manager = app(ModuleManagerService::class);

        $manager->registerModule('agriverse', [
            'name' => 'AgriVerse',
            'description' => 'AgriVerse Hub — quản lý cây cảnh bonsai, mô hình 3D, cửa hàng, đơn hàng, hợp đồng và AI scanning.',
            'icon' => 'eco',
        ]);

        if (class_exists(\Dedoc\Scramble\Scramble::class)) {
            \Dedoc\Scramble\Scramble::routes(function (\Illuminate\Routing\Route $route) {
                $uri = $route->uri();
                return \Illuminate\Support\Str::startsWith($uri, 'api') ||
                    \Illuminate\Support\Str::startsWith($uri, 'admin') ||
                    true;
            });
        }
    }
}
