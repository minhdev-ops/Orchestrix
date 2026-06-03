<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ModuleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/",
     *     tags={"System"},
     *     summary="Admin dashboard summary",
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function dashboard()
    {
        $moduleManager = app(\App\Services\ModuleManagerService::class);
        $modules = $moduleManager->getActiveModules();

        $stats = [];

        try {
        } catch (\Exception $e) {
            // Log error or notify admin, but don't crash the dashboard
            report($e);
        }

        return view('admin.dashboard', compact('modules', 'stats'));
    }

    /**
     * @OA\Get(
     *     path="/admin/modules",
     *     tags={"System"},
     *     summary="List all modules",
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        $moduleManager = app(\App\Services\ModuleManagerService::class);
        $allModules = $moduleManager->getAllModules();
        $activeModules = $moduleManager->getActiveModules();
        return view('admin.modules.index', compact('allModules', 'activeModules'));
    }

    /**
     * @OA\Post(
     *     path="/admin/modules/{module}/toggle",
     *     tags={"System"},
     *     summary="Toggle module active status",
     *     @OA\Parameter(name="module", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function toggle($name)
    {
        $modulesConfig = json_decode(File::get(base_path('modules.json')), true);
        if (isset($modulesConfig[$name])) {
            $modulesConfig[$name] = !$modulesConfig[$name];
            File::put(base_path('modules.json'), json_encode($modulesConfig, JSON_PRETTY_PRINT));
        }

        return back()->with('success', "Module $name updated successfully.");
    }
}
