<?php

use App\Http\Controllers\api\auth\SocialAuthController;
use App\Http\Controllers\api\AuthController;

// Route::prefix('admin')->name('admin.')->middleware('web')->group(function () {
//     require base_path('app/Admin/Routes/web.php');
// });
Route::post( 'register', [ AuthController::class, 'register' ] );
Route::post( 'login', [ AuthController::class, 'login' ] );
Route::get( 'active/{email}/{key}', [ AuthController::class, 'activeMail' ] )->name( 'active.mail' );
Route::get( 're-active', [ AuthController::class, 'reActive' ] )->name( 'reactive.mail' );
Route::post( 'forget-pass', [ AuthController::class, 'forgetPass' ] );
Route::get( 'reset-pass/{email}/{key}', [ AuthController::class, 'resetPass' ] )->name( 'reset.pass' );
Route::put( 'login/google', [ SocialAuthController::class, 'checkGoogle' ] )->name( 'api.login.google' );
Route::put('login/facebook', [SocialAuthController::class, 'checkFacebook'])->name('api.login.facebook');

Route::middleware( 'auth:api' )->group( function () {
    Route::get( 'user/detail', [ AuthController::class, 'show' ] );
    Route::post( '/change-pass', [ AuthController::class, 'changePass' ] );
    Route::get( '/logout', [ AuthController::class, 'logout' ] )->name( 'logout' );
} );
