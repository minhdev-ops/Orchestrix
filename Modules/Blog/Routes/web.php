<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Controllers\Public\BlogController;

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{category:slug}', [BlogController::class, 'byCategory'])->name('category');
    Route::get('/{category:slug}/{post:slug}', [BlogController::class, 'show'])->name('show');
    Route::post('/{blog_post}/like', [BlogController::class, 'like'])->name('like');
    Route::post('/{blog_post}/comment', [BlogController::class, 'comment'])->name('comment');
});

Route::get('api/blog/latest', [BlogController::class, 'latest'])->name('blog.api.latest');
