<?php

use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Authentication Routes
Auth::routes(['register' => false]);

use App\Http\Controllers\Auth\WebAuthController;

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

Route::post('/login', [WebAuthController::class, 'login']);

Route::get('/register', function () {
    if (auth()->check()) {
        return redirect()->route('agriverse.shop.home');
    }
    return inertia('Auth/Register');
})->name('register');

Route::post('/register', [WebAuthController::class, 'register']);

// Social Login
Route::post('/auth/google', [\App\Http\Controllers\Auth\SocialAuthController::class, 'google'])->name('social.google');
Route::post('/auth/facebook', [\App\Http\Controllers\Auth\SocialAuthController::class, 'facebook'])->name('social.facebook');

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

    // User Management
    Route::resource('users', UserController::class)->except(['show']);

    // Store Management
    Route::get('stores', [StoreController::class, 'index'])->name('stores.index');
    Route::get('stores/{store}/edit', [StoreController::class, 'edit'])->name('stores.edit');
    Route::put('stores/{store}', [StoreController::class, 'update'])->name('stores.update');
    Route::delete('stores/{store}', [StoreController::class, 'destroy'])->name('stores.destroy');

    // Product Management
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Order Management
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Coupon Management
    Route::resource('coupons', AdminCouponController::class);

    // Reports
    Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('reports/commission', [ReportController::class, 'commission'])->name('reports.commission');
    Route::get('reports/sellers', [ReportController::class, 'sellers'])->name('reports.sellers');

    // General Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
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
