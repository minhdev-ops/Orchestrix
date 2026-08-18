<?php

/**
 * @deprecated Module management is being migrated to the AgriVerse module.
 * This controller uses Blade views; the module version uses Inertia SPA.
 * Kept for backward compatibility. Will be removed in next major version.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Review;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\Transaction;
use App\Services\ModuleManagerService;
use Illuminate\Support\Facades\File;

class ModuleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/",
     *     tags={"System"},
     *     summary="Admin dashboard summary",
     *
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function dashboard()
    {
        $moduleManager = app(ModuleManagerService::class);
        $modules = $moduleManager->getActiveModules();

        $storesCount = Store::count();
        $productsCount = Product::count();
        $usersCount = User::count();
        $ordersCount = Order::count();
        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
        $commissionEarned = Order::where('status', 'completed')->sum('commission_fee');
        $pendingOrders = Order::where('status', 'pending')->count();
        $reviewsCount = Review::count();

        return view('admin.dashboard', compact(
            'modules', 'storesCount', 'productsCount', 'usersCount',
            'ordersCount', 'totalRevenue', 'commissionEarned',
            'pendingOrders', 'reviewsCount'
        ));
    }

    /**
     * @OA\Get(
     *     path="/admin/modules",
     *     tags={"System"},
     *     summary="List all modules",
     *
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        $moduleManager = app(ModuleManagerService::class);
        $allModules = $moduleManager->getAllModules();
        $activeModules = $moduleManager->getActiveModules();

        return view('admin.modules.index', compact('allModules', 'activeModules'));
    }

    /**
     * @OA\Post(
     *     path="/admin/modules/{module}/toggle",
     *     tags={"System"},
     *     summary="Toggle module active status",
     *
     *     @OA\Parameter(name="module", in="path", required=true, @OA\Schema(type="string")),
     *
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function toggle($name)
    {
        $modulesConfig = json_decode(File::get(base_path('modules.json')), true);
        if (isset($modulesConfig[$name])) {
            $modulesConfig[$name] = ! $modulesConfig[$name];
            File::put(base_path('modules.json'), json_encode($modulesConfig, JSON_PRETTY_PRINT));
        }

        return back()->with('success', "Module $name updated successfully.");
    }
}
