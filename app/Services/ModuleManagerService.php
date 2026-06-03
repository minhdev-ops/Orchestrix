<?php

namespace App\Services;

class ModuleManagerService
{
    protected array $modules = [];

    public function __construct()
    {
        // Initial core modules
        $this->registerModule('blog', [
            'name' => 'Blog Engine',
            'icon' => 'edit_note',
            'description' => 'Quản lý các bài viết tin tức và kiến thức kỹ thuật.',
            'route' => 'blog.index', // Redirecting to existing blog for now
            'color' => '#4cd7f6',
        ]);

        $this->registerModule('contact', [
            'name' => 'Message Inbox',
            'icon' => 'inbox',
            'description' => 'Theo dõi và phản hồi các yêu cầu liên hệ từ người dùng.',
            'route' => 'admin.dashboard', // Placeholder
            'color' => '#00687a',
        ]);
    }

    public function registerModule(string $key, array $config): void
    {
        $this->modules[$key] = $config;
    }

    public function getAllModules(): array
    {
        $config = json_decode(\Illuminate\Support\Facades\File::get(base_path('modules.json')), true);
        $result = [];

        foreach ($this->modules as $key => $module) {
            $result[] = array_merge($module, [
                'id' => $key,
                'enabled' => $config[$key] ?? false,
            ]);
        }

        return $result;
    }

    public function getActiveModules(): array
    {
        $all = $this->getAllModules();
        return array_filter($all, fn($m) => $m['enabled']);
    }

    public function getModule(string $key): ?array
    {
        return $this->modules[$key] ?? null;
    }
}
