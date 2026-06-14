<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use Inertia\Inertia;

class FileController
{
    public function index()
    {
        return Inertia::render('Admin/Files/Index');
    }
}
