<?php

namespace App\Providers;

use App\Models\User;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use App\Services\ModuleManagerService;
use Dedoc\Scramble\Scramble;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleManagerService::class, function ($app) {
            return new ModuleManagerService;
        });

        if (class_exists('Laravel\Telescope\TelescopeApplicationServiceProvider')) {
            $this->app->register('App\Providers\TelescopeServiceProvider');
        }
    }

    public function boot(): void
    {
        $manager = app(ModuleManagerService::class);

        $manager->registerModule('agriverse', [
            'name' => 'AgriVerse',
            'description' => 'AgriVerse Hub — quản lý cây cảnh bonsai, mô hình 3D, cửa hàng, đơn hàng, hợp đồng và AI scanning.',
            'icon' => 'eco',
        ]);

        if (class_exists(Scramble::class)) {
            Scramble::routes(function (Route $route) {
                $uri = $route->uri();

                return Str::startsWith($uri, 'api') ||
                    Str::startsWith($uri, 'admin');
            });
        }

        $this->configureCaching();
        $this->configureRateLimiters();
        $this->registerCascadeSoftDeletes();
    }

    private function configureRateLimiters(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    private function configureCaching(): void
    {
        Cache::macro('rememberForever', function ($key, $callback) {
            return Cache::remember($key, 86400 * 30, $callback);
        });
    }

    private function registerCascadeSoftDeletes(): void
    {
        $models = [
            User::class => ['stores', 'products'],
            Store::class => ['products'],
            Product::class => ['reviews', 'orders', 'images'],
            Order::class => ['statuses', 'transaction', 'refunds'],
        ];

        foreach ($models as $modelClass => $relations) {
            $modelClass::deleting(function ($model) use ($relations) {
                foreach ($relations as $relation) {
                    if (method_exists($model, $relation)) {
                        $model->$relation()->each(fn ($related) => $related->delete());
                    }
                }
            });
        }
    }
}
