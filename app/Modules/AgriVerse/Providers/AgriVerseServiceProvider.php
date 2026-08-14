<?php

namespace App\Modules\AgriVerse\Providers;

use App\Console\Commands\GenerateDailyReports;
use App\Modules\AgriVerse\Console\FetchGHNAddresses;
use App\Modules\AgriVerse\Events\OrderCancelled;
use App\Modules\AgriVerse\Events\OrderConfirmed;
use App\Modules\AgriVerse\Events\OrderCreated;
use App\Modules\AgriVerse\Listeners\RestoreInventoryOnCancel;
use App\Modules\AgriVerse\Listeners\SendOrderNotifications;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Policies\OrderPolicy;
use App\Modules\AgriVerse\Policies\ProductPolicy;
use App\Modules\AgriVerse\Policies\StorePolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AgriVerseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::middleware('api')
            ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../Routes/api.php'));

        Event::listen(
            OrderCreated::class,
            SendOrderNotifications::class,
        );

        Event::listen(
            OrderCancelled::class,
            RestoreInventoryOnCancel::class,
        );

        Event::listen(
            OrderCancelled::class,
            SendOrderNotifications::class,
        );

        Event::listen(
            OrderConfirmed::class,
            SendOrderNotifications::class,
        );

        Gate::policy(
            Order::class,
            OrderPolicy::class,
        );
        Gate::policy(
            Product::class,
            ProductPolicy::class,
        );
        Gate::policy(
            Store::class,
            StorePolicy::class,
        );

        Route::middleware('web')
            ->group(__DIR__.'/../Routes/admin.php');

        Route::middleware('web')
            ->group(__DIR__.'/../Routes/shop.php');

        $this->loadViewsFrom(__DIR__.'/../Resources/Views', 'agriverse');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                FetchGHNAddresses::class,
                \App\Modules\AgriVerse\Console\Commands\FetchProductImages::class,
                \App\Modules\AgriVerse\Console\Commands\ScrapeBonsaiArticles::class,
                GenerateDailyReports::class,
            ]);
        }
    }
}
