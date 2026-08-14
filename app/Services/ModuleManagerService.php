<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class ModuleManagerService
{
    protected array $modules = [];

    public function __construct()
    {
        // No modules registered in auth-only skeleton
    }

    public function registerModule(string $key, array $config): void
    {
        $this->modules[$key] = $config;
    }

    public function getAllModules(): array
    {
        $config = json_decode(File::get(base_path('modules.json')), true);
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

        return array_filter($all, fn ($m) => $m['enabled']);
    }

    public function getModule(string $key): ?array
    {
        return $this->modules[$key] ?? null;
    }
}
