<?php

use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Authentication Routes
Auth::routes();

// Home Route
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
})->name('home');


Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
    ->name('ckfinder_connector');

Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
    ->name('ckfinder_browser');

Route::middleware(['auth', 'checkAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [ModuleController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::resource('users', UserController::class)->except(['show']);

    // General Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
