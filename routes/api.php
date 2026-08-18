<?php

use App\Http\Controllers\Api\AdminPermissionController;
use App\Http\Controllers\api\auth\SocialAuthController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ExpenseController;
use App\Http\Controllers\Api\AdminPermissionController;

use App\Modules\AgriVerse\Http\Controllers\Api\AuthBridgeController;

Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Auth Bridge for JakartaEE token validation
Route::post('auth/validate-token', [AuthBridgeController::class, 'validateToken']);
Route::get('auth/public-key', [AuthBridgeController::class, 'publicKey']);
Route::get('active/{email}/{key}', [AuthController::class, 'activeMail'])->name('active.mail');
Route::get('re-active', [AuthController::class, 'reActive'])->name('reactive.mail');
Route::post('forget-pass', [AuthController::class, 'forgetPass'])->middleware('throttle:3,60');
Route::get('reset-pass/{email}/{key}', [AuthController::class, 'resetPass'])->name('reset.pass');
Route::put('login/google', [SocialAuthController::class, 'checkGoogle'])->name('api.login.google');
Route::put('login/facebook', [SocialAuthController::class, 'checkFacebook'])->name('api.login.facebook');

Route::middleware('auth:api')->group(function () {
    Route::get('user/detail', [AuthController::class, 'show']);
    Route::post('user/update', [AuthController::class, 'updateProfile']);
    Route::post('/change-pass', [AuthController::class, 'changePass']);
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Personal Expenses
    Route::post('expenses/create', [ExpenseController::class, 'store']);
    Route::get('expense-categories', [ExpenseController::class, 'categories']);
    Route::post('expense-categories/create', [ExpenseController::class, 'storeCategory']);

    // Admin Permission Management
    Route::middleware('permission:admin.access')->prefix('admin')->group(function () {
        Route::get('roles', [AdminPermissionController::class, 'roles']);
        Route::get('permissions', [AdminPermissionController::class, 'permissions']);
        Route::post('roles/assign', [AdminPermissionController::class, 'assignRoleToUser']);
        Route::post('roles/remove', [AdminPermissionController::class, 'removeRoleFromUser']);
        Route::post('permissions/sync', [AdminPermissionController::class, 'assignPermissionToRole']);
        Route::get('users/{user}/permissions', [AdminPermissionController::class, 'userPermissions']);
    });
});
