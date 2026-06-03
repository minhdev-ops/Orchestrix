<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Controllers\Admin\BlogModuleController;

Route::resource('blog', BlogModuleController::class);
