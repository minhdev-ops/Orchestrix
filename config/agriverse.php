<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Optimization Configuration
    |--------------------------------------------------------------------------
    */
    'image' => [
        'max_size' => 5120, // 5MB
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
        'sizes' => [
            'thumbnail' => ['width' => 150, 'height' => 150, 'crop' => true],
            'small' => ['width' => 300, 'height' => 300, 'crop' => false],
            'medium' => ['width' => 600, 'height' => 600, 'crop' => false],
            'large' => ['width' => 1200, 'height' => 1200, 'crop' => false],
        ],
        'quality' => [
            'webp' => 85,
            'jpeg' => 90,
        ],
        'watermark' => [
            'enabled' => false,
            'text' => 'AgriVerse',
            'font_size' => 16,
            'color' => 'rgba(255, 255, 255, 0.7)',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'ttl' => [
            'product' => 3600, // 1 hour
            'category' => 86400, // 24 hours
            'store' => 3600, // 1 hour
            'home' => 300, // 5 minutes
            'search' => 300, // 5 minutes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Configuration
    |--------------------------------------------------------------------------
    */
    'seo' => [
        'default_title' => 'AgriVerse - Sàn thương mại điện tử nông nghiệp',
        'default_description' => 'AgriVerse - Nền tảng thương mại điện tử nông nghiệp hàng đầu Việt Nam',
        'default_image' => '/images/og-default.jpg',
        'max_title_length' => 60,
        'max_description_length' => 160,
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    */
    'search' => [
        'min_query_length' => 2,
        'max_results' => 50,
        'autocomplete_limit' => 10,
        'cache_ttl' => 300, // 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    */
    'rate_limit' => [
        'api' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'login' => [
            'max_attempts' => 5,
            'decay_minutes' => 15,
        ],
        'checkout' => [
            'max_attempts' => 10,
            'decay_minutes' => 1,
        ],
        'password_reset' => [
            'max_attempts' => 3,
            'decay_minutes' => 60,
        ],
    ],

];
