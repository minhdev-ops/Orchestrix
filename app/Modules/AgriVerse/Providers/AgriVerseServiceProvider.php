<?php

namespace App\Modules\AgriVerse\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Modules\AgriVerse\Events\OrderCreated;
use App\Modules\AgriVerse\Listeners\SendOrderNotifications;

class AgriVerseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');

        \Illuminate\Support\Facades\Event::listen(
            OrderCreated::class,
            SendOrderNotifications::class,
        );

        Route::middleware('web')
            ->group(__DIR__.'/../Routes/admin.php');

        Route::middleware('web')
            ->group(__DIR__.'/../Routes/shop.php');

        $this->loadViewsFrom(__DIR__.'/../Resources/Views', 'agriverse');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Modules\AgriVerse\Console\FetchGHNAddresses::class,
                \App\Modules\AgriVerse\Console\TestPlantDoctorPipeline::class,
            ]);
        }
    }
}
