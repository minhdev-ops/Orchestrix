# 🚀 Laravel Modular Multi-Project Architecture (Advanced)

## 🎯 Mục tiêu

* Tách hệ thống thành **Modules độc lập**
* Có **Admin trung tâm** quản lý:

    * Modules
    * Projects
* Hỗ trợ **multi-project (multi-tenant nhẹ)**
* Module được tạo tự động bằng:

```bash
php artisan make:module Blog
```

---

# 🏗️ 1. Cấu trúc tổng thể

```bash
laravel-root/
│
├── app/
│   ├── Core/                 # Base system
│   ├── Admin/                # Admin central
│   └── Console/
│       └── Commands/
│           └── MakeModule.php
│
├── Modules/                  # ⭐ MODULES RIÊNG (QUAN TRỌNG)
│   ├── User/
│   ├── Product/
│   └── ...
│
├── Projects/                 # Multi project
│   ├── ProjectA/
│   ├── ProjectB/
│   └── ...
│
├── routes/
│   ├── web.php
│   ├── api.php
│   └── admin.php
│
├── config/
├── database/
├── resources/
│   └── views/
│       ├── admin/
│       └── modules/
│
├── modules.json              # bật/tắt module
├── projects.json             # config project
│
└── composer.json
```

---

# 🧩 2. Cấu trúc 1 Module

```bash
Modules/Blog/
│
├── Controllers/
├── Models/
├── Services/
├── Repositories/
├── Routes/
│   └── web.php
├── Views/
├── Providers/
│   └── BlogServiceProvider.php
├── Database/
│   ├── Migrations/
│   └── Seeders/
└── module.json
```

---

# ⚙️ 3. Tạo Module bằng PHP (Artisan Command)

## Tạo command

```bash
php artisan make:command MakeModule
```

---

## Code: MakeModule.php

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModule extends Command
{
    protected $signature = 'make:module {name}';
    protected $description = 'Create module system';

    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $base = base_path("Modules/$name");

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

        // routes
        File::put("$base/Routes/web.php", "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::prefix('" . strtolower($name) . "')->group(function () {\n    Route::get('/', fn() => '$name module works');\n});");

        // provider
        File::put("$base/Providers/{$name}ServiceProvider.php", "<?php\n\nnamespace Modules\\$name\\Providers;\n\nuse Illuminate\\Support\\ServiceProvider;\n\nclass {$name}ServiceProvider extends ServiceProvider\n{\n    public function boot()\n    {\n        \$this->loadRoutesFrom(__DIR__.'/../Routes/web.php');\n    }\n}");

        // config
        File::put("$base/module.json", json_encode([
            'name' => $name,
            'enabled' => true
        ], JSON_PRETTY_PRINT));

        $this->info("Module $name created!");
    }
}
```

---

# 🔌 4. Auto Load Modules

## AppServiceProvider.php

```php
use Illuminate\Support\Facades\File;

public function register()
{
    $modules = base_path('Modules');

    foreach (File::directories($modules) as $module) {
        $name = basename($module);
        $provider = "Modules\\$name\\Providers\\{$name}ServiceProvider";

        if (class_exists($provider)) {
            $this->app->register($provider);
        }
    }
}
```

---

# 📦 5. Composer Autoload

```json
"autoload": {
  "psr-4": {
    "App\\": "app/",
    "Modules\\": "Modules/"
  }
}
```

```bash
composer dump-autoload
```

---

# 🧠 6. Admin Central System

## 📁 Structure

```bash
app/Admin/
│
├── Controllers/
│   ├── ModuleController.php
│   └── ProjectController.php
│
├── Services/
├── Views/
└── Routes/web.php
```

---

## 🎮 Module Controller

```php
class ModuleController extends Controller
{
    public function index()
    {
        return view('admin.modules.index', [
            'modules' => json_decode(file_get_contents(base_path('modules.json')), true)
        ]);
    }

    public function toggle($name)
    {
        $modules = json_decode(file_get_contents(base_path('modules.json')), true);
        $modules[$name] = !$modules[$name];

        file_put_contents(base_path('modules.json'), json_encode($modules, JSON_PRETTY_PRINT));

        return back();
    }
}
```

---

# 🏢 7. Multi Project System

## projects.json

```json
{
  "ProjectA": {
    "modules": ["User", "Blog"]
  },
  "ProjectB": {
    "modules": ["Product"]
  }
}
```

---

## Load module theo project

```php
$project = request()->getHost(); // domain

$config = json_decode(file_get_contents(base_path('projects.json')), true);

$modules = $config[$project]['modules'] ?? [];

foreach ($modules as $module) {
    // load module tương ứng
}
```

---

# 🌐 8. Routing

## routes/admin.php

```php
Route::prefix('admin')->group(function () {
    require app_path('Admin/Routes/web.php');
});
```

---

# 🔐 9. Flow hoạt động

```text
Admin → tạo module → bật module
      → gán module vào project
      → project sử dụng module
```

---

# 🔥 10. Nâng cấp mạnh hơn

* Multi database theo project
* Redis cache theo project
* Queue riêng module
* API Gateway
* Plugin system (giống WordPress)

---

# ✅ Tổng kết

Bạn có:

✔ Modules riêng (ngoài app)
✔ Tạo module bằng PHP (artisan)
✔ Admin quản lý module
✔ Multi-project
✔ Scale cực dễ

---

# 🎁 Nếu bạn muốn level PRO

Mình có thể làm tiếp:

* 🧩 Admin UI (giống SaaS dashboard)
* 🏢 Multi-tenant DB thật sự
* 🔐 Permission theo module
* ⚡ CRUD generator cho module

👉 chỉ cần nói: **"build full system cho tôi"**
