<?php

use Illuminate\Support\Facades\Route;
use Modules\Portfolio\Controllers\Admin\PortfolioModuleController;
use Modules\Portfolio\Controllers\Admin\AboutStatController;
use Modules\Portfolio\Controllers\Admin\AboutExperienceController;

Route::middleware(['web', 'auth'])->prefix('admin/portfolio')->name('admin.portfolio.')->group(function () {
    Route::get('/', [PortfolioModuleController::class, 'index'])->name('index');

    Route::prefix('projects')->group(function () {
        Route::get('/', [PortfolioModuleController::class, 'projects'])->name('projects');
        Route::get('/create', [PortfolioModuleController::class, 'createProject'])->name('projects.create');
        Route::post('/', [PortfolioModuleController::class, 'storeProject'])->name('projects.store');
        Route::get('/{project}', [PortfolioModuleController::class, 'showProject'])->name('projects.show');
        Route::get('/{project}/edit', [PortfolioModuleController::class, 'editProject'])->name('projects.edit');
        Route::put('/{project}', [PortfolioModuleController::class, 'updateProject'])->name('projects.update');
        Route::delete('/{project}', [PortfolioModuleController::class, 'destroyProject'])->name('projects.destroy');
    });

    Route::prefix('skills')->group(function () {
        Route::get('/', [PortfolioModuleController::class, 'skills'])->name('skills');
        Route::get('/create', [PortfolioModuleController::class, 'createSkill'])->name('skills.create');
        Route::post('/', [PortfolioModuleController::class, 'storeSkill'])->name('skills.store');
        Route::get('/{skill}/edit', [PortfolioModuleController::class, 'editSkill'])->name('skills.edit');
        Route::put('/{skill}', [PortfolioModuleController::class, 'updateSkill'])->name('skills.update');
        Route::delete('/{skill}', [PortfolioModuleController::class, 'destroySkill'])->name('skills.destroy');
    });

    Route::prefix('contacts')->group(function () {
        Route::get('/', [PortfolioModuleController::class, 'contacts'])->name('contacts');
        Route::get('/{contact}', [PortfolioModuleController::class, 'showContact'])->name('contacts.show');
        Route::delete('/{contact}', [PortfolioModuleController::class, 'destroyContact'])->name('contacts.destroy');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/about', [PortfolioModuleController::class, 'editAbout'])->name('settings.about');
        Route::post('/about', [PortfolioModuleController::class, 'updateAbout'])->name('settings.about.update');

        Route::resource('about-stats', AboutStatController::class)
            ->only(['store', 'update', 'destroy'])
            ->names(['store' => 'settings.about-stats.store', 'update' => 'settings.about-stats.update', 'destroy' => 'settings.about-stats.destroy']);

        Route::resource('about-experiences', AboutExperienceController::class)
            ->only(['store', 'update', 'destroy'])
            ->names(['store' => 'settings.about-experiences.store', 'update' => 'settings.about-experiences.update', 'destroy' => 'settings.about-experiences.destroy']);

        Route::get('/home', [PortfolioModuleController::class, 'editHome'])->name('settings.home');
        Route::post('/home', [PortfolioModuleController::class, 'updateHome'])->name('settings.home.update');
    });
});
