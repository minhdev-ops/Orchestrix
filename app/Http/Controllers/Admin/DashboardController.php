<?php

/**
 * @deprecated Use App\Modules\AgriVerse\Http\Controllers\Admin\DashboardController instead.
 * This controller uses Blade views; the module version uses Inertia SPA.
 * Kept for backward compatibility. Will be removed in next major version.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ModuleManagerService;

class DashboardController extends Controller
{
    protected $moduleManager;

    public function __construct(ModuleManagerService $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    public function index()
    {
        $modules = $this->moduleManager->getActiveModules();

        return view('admin.dashboard', compact('modules'));
    }
}
