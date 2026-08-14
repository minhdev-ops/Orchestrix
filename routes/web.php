<?php

use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Auth::routes(['register' => false]);

// Explicit password reset routes (avoid relying solely on Auth::routes)
Route::post('/password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\WebAuthController;
use Illuminate\Http\Request;
use Inertia\Inertia;

// Override login/register to use Inertia SPA pages
Route::get('/login', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin') {
            return redirect('/admin/agriverse');
        }

        return redirect()->route('agriverse.shop.home');
    }

    return inertia('Auth/Login');
})->name('login');

Route::post('/login', [WebAuthController::class, 'login'])->middleware('throttle:5,1');

Route::get('/register', function () {
    if (auth()->check()) {
        return redirect()->route('agriverse.shop.home');
    }

    return inertia('Auth/Register');
})->name('register');

Route::post('/register', [WebAuthController::class, 'register']);

// Social Login
Route::get('/auth/google/callback', [SocialAuthController::class, 'googleCallback'])->name('social.google.callback');
Route::post('/auth/google', [SocialAuthController::class, 'google'])->name('social.google');
Route::post('/auth/facebook', [SocialAuthController::class, 'facebook'])->name('social.facebook');

// Email Verification
require __DIR__.'/verification.php';

Route::get('password/reset', function () {
    return Inertia::render('Auth/ForgotPassword', [
        'status' => session('status'),
    ]);
})->name('password.request');

Route::get('password/reset/{token}', function (Request $request, $token) {
    return Inertia::render('Auth/ResetPassword', [
        'token' => $token,
        'email' => $request->email,
    ]);
})->name('password.reset');

// Route for legacy verify view — redirects to app's custom verification
Route::post('/email/resend', function () {
    return redirect()->route('auth.verify-email.send-code');
})->name('verification.resend');

// SEO (sitemap, robots.txt)
require __DIR__.'/seo.php';

// Push Notifications
require __DIR__.'/push_notifications.php';

// Home Route — role-based redirect
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin') {
            return redirect()->route('admin.agriverse.dashboard');
        }

        return redirect()->route('agriverse.shop.home');
    }

    return redirect()->route('agriverse.shop.home');
})->name('home');

Route::middleware(['auth', 'ckfinder.auth'])->group(function () {
    Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
        ->name('ckfinder_connector');

    Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
        ->name('ckfinder_browser');
});

Route::middleware(['auth', 'checkAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [ModuleController::class, 'dashboard'])->name('dashboard');
    Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::post('/modules/{module}/toggle', [ModuleController::class, 'toggle'])->name('modules.toggle');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Legacy redirects: all resource routes redirect to module's Inertia SPA pages
    Route::redirect('/users', '/admin/agriverse/users')->name('users.index');
    Route::redirect('/users/create', '/admin/agriverse/users')->name('users.create');
    Route::get('/users/{user}/edit', fn () => redirect('/admin/agriverse/users'))->name('users.edit');
    Route::put('/users/{user}', fn () => redirect('/admin/agriverse/users'))->name('users.update');
    Route::post('/users', fn () => redirect('/admin/agriverse/users'))->name('users.store');
    Route::delete('/users/{user}', fn () => redirect('/admin/agriverse/users'))->name('users.destroy');
    Route::redirect('/stores', '/admin/agriverse/stores')->name('stores.index');
    Route::redirect('/products', '/admin/agriverse/products')->name('products.index');
    Route::redirect('/orders', '/admin/agriverse/orders')->name('orders.index');
    Route::redirect('/coupons', '/admin/agriverse/coupons')->name('coupons.index');
    Route::redirect('/reports/revenue', '/admin/agriverse/reports')->name('reports.revenue');
    Route::redirect('/reports/commission', '/admin/agriverse/reports')->name('reports.commission');
    Route::redirect('/reports/sellers', '/admin/agriverse/reports')->name('reports.sellers');

    // Stores
    Route::get('/stores/{store}/edit', fn () => redirect('/admin/agriverse/stores'))->name('stores.edit');
    Route::put('/stores/{store}', fn () => redirect('/admin/agriverse/stores'))->name('stores.update');
    Route::delete('/stores/{store}', fn () => redirect('/admin/agriverse/stores'))->name('stores.destroy');

    // Products
    Route::get('/products/{product}/edit', fn () => redirect('/admin/agriverse/products'))->name('products.edit');
    Route::put('/products/{product}', fn () => redirect('/admin/agriverse/products'))->name('products.update');
    Route::delete('/products/{product}', fn () => redirect('/admin/agriverse/products'))->name('products.destroy');

    // Orders
    Route::get('/orders/{order}', fn () => redirect('/admin/agriverse/orders'))->name('orders.show');
    Route::post('/orders/{order}/status', fn () => redirect('/admin/agriverse/orders'))->name('orders.status');

    // Coupons
    Route::get('/coupons/create', fn () => redirect('/admin/agriverse/coupons'))->name('coupons.create');
    Route::get('/coupons/{coupon}/edit', fn () => redirect('/admin/agriverse/coupons'))->name('coupons.edit');
    Route::put('/coupons/{coupon}', fn () => redirect('/admin/agriverse/coupons'))->name('coupons.update');
    Route::post('/coupons', fn () => redirect('/admin/agriverse/coupons'))->name('coupons.store');
    Route::delete('/coupons/{coupon}', fn () => redirect('/admin/agriverse/coupons'))->name('coupons.destroy');
});

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::get('/home', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.agriverse.dashboard');
        }

        return redirect()->route('agriverse.shop.home');
    }

    return redirect()->route('agriverse.shop.home');
})->name('homepage');
