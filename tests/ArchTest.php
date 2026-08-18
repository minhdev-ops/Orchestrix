<?php

// ============================================
// Architecture Tests - Pest PHP
// ============================================
// These tests verify the project follows basic conventions.
// NOTE: Some tests use extensive ignoring() lists because
// many controllers/models don't follow strict Laravel conventions.
// ============================================

arch()->preset()->php();
arch()->preset()->security()->ignoring('md5');

/*************** Global Functions ***************/

arch('globals')->expect(['dd', 'dump', 'var_dump', 'die', 'exit'])->not->toBeUsed();

/*************** Models ***************/

arch('models are classes')
    ->expect('App\\Modules\\AgriVerse\\Models')
    ->toBeClasses();

arch('models extend Eloquent Model')
    ->expect('App\\Modules\\AgriVerse\\Models')
    ->toExtend('Illuminate\\Database\\Eloquent\\Model');

/*************** Controllers ***************/

arch('controllers have Controller suffix')
    ->expect('App\\Modules\\AgriVerse\\Http\\Controllers')
    ->toHaveSuffix('Controller');

/*************** Services ***************/

arch('services have Service suffix')
    ->expect('App\\Modules\\AgriVerse\\Services')
    ->toHaveSuffix('Service');
