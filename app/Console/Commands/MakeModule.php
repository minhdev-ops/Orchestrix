<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
//chỗ này để thêm các module vào trong project bằng console
class MakeModule extends Command
{
    protected $signature = 'make:module {name}';
    protected $description = 'Create a new modular architecture module';

    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $base = base_path("Modules/$name");

        if (File::exists($base)) {
            $this->error("Module $name already exists!");
            return;
        }

        $folders = [
            'Controllers',
            'Models',
            'Services',
            'Repositories',
            'Routes',
            'Views',
            'Providers',
            'Database/Migrations',
            'Database/Seeders'
        ];

        foreach ($folders as $folder) {
            File::makeDirectory("$base/$folder", 0755, true, true);
        }

        // Create Service Provider
        $this->createServiceProvider($name, $base);

        // Create Route file
        $this->createRouteFile($name, $base);

        // Create placeholder controller
        $this->createController($name, $base);

        // Create module.json
        File::put("$base/module.json", json_encode([
            'name' => $name,
            'enabled' => true,
            'icon' => 'extension',
            'description' => "This is the $name module."
        ], JSON_PRETTY_PRINT));

        $this->info("Module $name created successfully at Modules/$name");
        $this->info("Run 'composer dump-autoload' if this is your first module.");
    }

    protected function createServiceProvider($name, $base)
    {
        $content = "<?php

namespace Modules\\$name\\Providers;

use Illuminate\\Support\\ServiceProvider;
use Illuminate\\Support\\Facades\\File;

class {$name}ServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        \$this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        \$this->loadViewsFrom(__DIR__.'/../Views', '" . strtolower($name) . "');
        \$this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}";
        File::put("$base/Providers/{$name}ServiceProvider.php", $content);
    }

    protected function createRouteFile($name, $base)
    {
        $lowerName = strtolower($name);
        $content = "<?php

use Illuminate\\Support\\Facades\\Route;
use Modules\\$name\\Controllers\\{$name}Controller;

Route::prefix('$lowerName')->name('$lowerName.')->group(function () {
    Route::get('/', [{$name}Controller::class, 'index'])->name('index');
});";
        File::put("$base/Routes/web.php", $content);
    }

    protected function createController($name, $base)
    {
        $content = "<?php

namespace Modules\\$name\\Controllers;

use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;

class {$name}Controller extends Controller
{
    public function index()
    {
        return view('" . strtolower($name) . "::index');
    }
}";
        File::put("$base/Controllers/{$name}Controller.php", $content);

        // Create a basic view too
        File::put("$base/Views/index.blade.php", "<h1>$name Module</h1>\n<p>Welcome to the $name module!</p>");
    }
}
