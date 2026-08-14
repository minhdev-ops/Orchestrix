<?php

use App\Modules\AgriVerse\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('auth')->name('auth.')->group(function () {
    Route::get('/verify-email', [EmailVerificationController::class, 'show'])->name('verify-email.show');
    Route::post('/verify-email/send-code', [EmailVerificationController::class, 'sendCode'])->name('verify-email.send-code');
    Route::post('/verify-email/verify', [EmailVerificationController::class, 'verify'])->name('verify-email.verify');
});
