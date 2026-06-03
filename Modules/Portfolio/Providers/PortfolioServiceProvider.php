<?php

namespace Modules\Portfolio\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class PortfolioServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        if (File::exists(__DIR__.'/../Routes/admin.php')) {
            $this->loadRoutesFrom(__DIR__.'/../Routes/admin.php');
        }
        $this->loadViewsFrom(__DIR__.'/../Views', 'portfolio');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}