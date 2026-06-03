<?php

namespace App\Providers;

use App\Services\ModuleManagerService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleManagerService::class, function ($app) {
            return new ModuleManagerService();
        });

        $modulesPath = base_path('Modules');
        $modulesConfigPath = base_path('modules.json');
        $projectsConfigPath = base_path('projects.json');

        if (File::exists($modulesConfigPath) && File::exists($projectsConfigPath)) {
            $modulesConfig = json_decode(File::get($modulesConfigPath), true);
            $projectsConfig = json_decode(File::get($projectsConfigPath), true);

            $host = request()->getHost();
            $projectModules = $projectsConfig[$host]['modules'] ?? $projectsConfig['localhost']['modules'] ?? $projectsConfig['orchestrix.test']['modules'] ?? [];

            if (app()->environment('testing') || app()->runningInConsole() || $host === 'localhost' || $host === '127.0.0.1') {
                $projectModules = ['Portfolio'];
            }

            foreach ($projectModules as $moduleName) {
                // Determine the config key (lowercase) and the actual module name
                $actualName = ucfirst($moduleName);
                $configKey = strtolower($moduleName);

                // Load if enabled in modules.json OR if in testing environment
                if (app()->environment('testing') || ($modulesConfig[$configKey] ?? false) === true) {
                    $provider = "Modules\\$actualName\\Providers\\{$actualName}ServiceProvider";
                    if (class_exists($provider)) {
                        $this->app->register($provider);
                    }
                }
            }
        } elseif (File::isDirectory($modulesPath)) {
            // Fallback to old behavior if config files are missing
            foreach (File::directories($modulesPath) as $moduleDir) {
                $moduleName = basename($moduleDir);
                $provider = "Modules\\$moduleName\\Providers\\{$moduleName}ServiceProvider";
                if (class_exists($provider)) {
                    $this->app->register($provider);
                }
            }
        }
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Validator::extend('unique_project_domain', function ($attribute, $value, $parameters, $validator) {
            $projects = json_decode(File::get(base_path('projects.json')), true) ?? [];
            return !isset($projects[$value]);
        });

        $moduleManager = app(ModuleManagerService::class);

        $moduleManager->registerModule('portfolio', [
            'name' => 'Portfolio Manager',
            'icon' => 'account_tree',
            'route' => 'admin.portfolio.index',
            'description' => 'Quản lý Dự án & Kỹ năng kỹ thuật'
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
