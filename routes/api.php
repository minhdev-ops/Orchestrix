<?php

use App\Http\Controllers\api\auth\SocialAuthController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ThreeDAssetController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\SubscriptionPlanController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('active/{email}/{key}', [AuthController::class, 'activeMail'])->name('active.mail');
Route::get('re-active', [AuthController::class, 'reActive'])->name('reactive.mail');
Route::post('forget-pass', [AuthController::class, 'forgetPass']);
Route::get('reset-pass/{email}/{key}', [AuthController::class, 'resetPass'])->name('reset.pass');
Route::put('login/google', [SocialAuthController::class, 'checkGoogle'])->name('api.login.google');
Route::put('login/facebook', [SocialAuthController::class, 'checkFacebook'])->name('api.login.facebook');

Route::middleware('auth:api')->group(function () {
    Route::get('user/detail', [AuthController::class, 'show']);
    Route::post('/change-pass', [AuthController::class, 'changePass']);
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('permission:product.view')->group(function () {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{product}', [ProductController::class, 'show']);
    });
    Route::post('products', [ProductController::class, 'store'])->middleware('permission:product.create');
    Route::match(['put', 'patch'], 'products/{product}', [ProductController::class, 'update'])->middleware('permission:product.edit');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->middleware('permission:product.delete');
    Route::get('products/{product}/assets', [ThreeDAssetController::class, 'index']);

    Route::middleware('permission:asset.view')->group(function () {
        Route::get('assets', [ThreeDAssetController::class, 'index']);
        Route::get('assets/{asset}', [ThreeDAssetController::class, 'show']);
    });
    Route::post('assets', [ThreeDAssetController::class, 'store'])->middleware('permission:asset.upload');
    Route::match(['put', 'patch'], 'assets/{asset}', [ThreeDAssetController::class, 'update'])->middleware('permission:asset.edit');
    Route::delete('assets/{asset}', [ThreeDAssetController::class, 'destroy'])->middleware('permission:asset.delete');
    Route::post('assets/{asset}/compress', [ThreeDAssetController::class, 'compress'])->middleware('permission:asset.compress');

    Route::get('subscriptions', [SubscriptionController::class, 'index'])->middleware('permission:subscription.view');
    Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show'])->middleware('permission:subscription.view');
    Route::post('subscriptions', [SubscriptionController::class, 'store'])->middleware('permission:subscription.create');
    Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->middleware('permission:subscription.edit');

    Route::middleware('permission:store.view')->group(function () {
        Route::get('stores', [StoreController::class, 'index']);
        Route::get('stores/{store}', [StoreController::class, 'show']);
    });

    Route::middleware('permission:plan.view')->group(function () {
        Route::get('subscription-plans', [SubscriptionPlanController::class, 'index']);
        Route::get('subscription-plans/{plan}', [SubscriptionPlanController::class, 'show']);
    });
});
