<?php

use Illuminate\Support\Facades\Route;
use Modules\Portfolio\Controllers\Public\PortfolioController;

Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::get('/', [PortfolioController::class, 'index'])->name('index');
    Route::get('/home-ref', [PortfolioController::class, 'index'])->name('home');

    Route::get('/about', [PortfolioController::class, 'about'])->name('about');
    Route::get('/contact', [PortfolioController::class, 'contact'])->name('contact');
    Route::post('/contact', [PortfolioController::class, 'storeContact'])->name('contact.store');

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [PortfolioController::class, 'projects'])->name('index');
        Route::get('/{project}', [PortfolioController::class, 'showProject'])->name('show');
    });

    Route::prefix('skills')->name('skills.')->group(function () {
        Route::get('/', [PortfolioController::class, 'skills'])->name('index');
        Route::get('/{skill}', [PortfolioController::class, 'showSkill'])->name('show');
    });
});

Route::prefix('api/portfolio')->name('portfolio.api.')->group(function () {
    Route::get('/projects', [PortfolioController::class, 'apiProjects'])->name('projects');
    Route::get('/skills', [PortfolioController::class, 'apiSkills'])->name('skills');
});
