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
        // ── Blog ──
        [
            'name' => 'Blog Posts',
            'icon' => 'article',
            'route' => 'admin.blog.index',
            'module' => 'blog',
        ],
        [
            'name' => 'Categories',
            'icon' => 'category',
            'route' => 'admin.blog-categories.index',
            'module' => 'blog',
        ],
        [
            'name' => 'Comments',
            'icon' => 'comment',
            'route' => 'admin.blog-comments.index',
            'module' => 'blog',
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
