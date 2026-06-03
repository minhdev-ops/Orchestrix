<?php

return [
    'title' => 'Orchestrix Admin',
    'prefix' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | Admin Navigation
    |--------------------------------------------------------------------------
    | Define your modular admin menu here. Each item should have:
    | name: display label
    | icon: Material Symbols identifier
    | route: named route
    | module: logical grouping
    */
    'navigation' => [
        [
            'name' => 'Dashboard',
            'icon' => 'dashboard',
            'route' => 'admin.dashboard',
            'module' => 'core',
        ],

        // ── Settings ──
        [
            'name' => 'Settings',
            'icon' => 'settings',
            'route' => 'admin.settings',
            'module' => 'core',
        ],
        [
            'name' => 'System Logs',
            'icon' => 'list_alt',
            'route' => 'admin.logs',
            'module' => 'system',
        ],
    ],
];
