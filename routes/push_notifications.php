<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AgriVerse\Http\Controllers\Api\PushNotificationController;

Route::middleware(['api', 'auth:api'])->prefix('api')->group(function () {
    // Push Notifications
    Route::prefix('notifications/push')->group(function () {
        Route::post('register', [PushNotificationController::class, 'register']);
        Route::post('unregister', [PushNotificationController::class, 'unregister']);
        Route::get('devices', [PushNotificationController::class, 'devices']);
        Route::post('test', [PushNotificationController::class, 'test']);
    });
});
