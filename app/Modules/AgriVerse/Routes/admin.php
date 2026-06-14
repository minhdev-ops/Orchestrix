<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AgriVerse\Http\Controllers\Admin\DashboardController;
use App\Modules\AgriVerse\Http\Controllers\Admin\ProductController;
use App\Modules\AgriVerse\Http\Controllers\Admin\StoreController;
use App\Modules\AgriVerse\Http\Controllers\Admin\OrderController;
use App\Modules\AgriVerse\Http\Controllers\Admin\CategoryController;
use App\Modules\AgriVerse\Http\Controllers\Admin\SubscriptionPlanController;
use App\Modules\AgriVerse\Http\Controllers\Admin\ContractController;
use App\Modules\AgriVerse\Http\Controllers\Admin\CouponController;
use App\Modules\AgriVerse\Http\Controllers\Admin\AiScanningController;
use App\Modules\AgriVerse\Http\Controllers\Admin\TransactionController;
use App\Modules\AgriVerse\Http\Controllers\Admin\ReportController;
use App\Modules\AgriVerse\Http\Controllers\Admin\RefundController;
use App\Modules\AgriVerse\Http\Controllers\Admin\UserController;
use App\Modules\AgriVerse\Http\Controllers\Admin\BannerController;
use App\Modules\AgriVerse\Http\Controllers\Admin\ForumController;
use App\Modules\AgriVerse\Http\Controllers\Admin\ForumCategoryController;
use App\Modules\AgriVerse\Http\Controllers\Admin\FileController;
use App\Modules\AgriVerse\Http\Controllers\Admin\SellerController;
use App\Modules\AgriVerse\Http\Controllers\Admin\ChatGroupController;

Route::middleware(['auth', 'checkAdmin'])->prefix('admin/agriverse')->name('admin.agriverse.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::match(['put', 'patch'], 'products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('products/{product}/3d-model', [ProductController::class, 'upload3dModel'])->name('products.upload-3d-model');
    Route::delete('products/{product}/3d-model', [ProductController::class, 'delete3dModel'])->name('products.delete-3d-model');
    Route::post('products/{product}/approve', [ProductController::class, 'approve'])->name('products.approve');
    Route::post('products/{product}/reject', [ProductController::class, 'reject'])->name('products.reject');

    // Stores
    Route::get('stores', [StoreController::class, 'index'])->name('stores.index');
    Route::get('stores/create', [StoreController::class, 'create'])->name('stores.create');
    Route::post('stores', [StoreController::class, 'store'])->name('stores.store');
    Route::get('stores/{store}/edit', [StoreController::class, 'edit'])->name('stores.edit');
    Route::match(['put', 'patch'], 'stores/{store}', [StoreController::class, 'update'])->name('stores.update');
    Route::delete('stores/{store}', [StoreController::class, 'destroy'])->name('stores.destroy');

    // Manual order creation (must be before orders/{order})
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'storeOrder'])->name('orders.store');

    // Orders
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Categories
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::match(['put', 'patch'], 'categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Subscription Plans
    Route::get('plans', [SubscriptionPlanController::class, 'index'])->name('plans.index');
    Route::get('plans/create', [SubscriptionPlanController::class, 'create'])->name('plans.create');
    Route::post('plans', [SubscriptionPlanController::class, 'store'])->name('plans.store');
    Route::get('plans/{plan}/edit', [SubscriptionPlanController::class, 'edit'])->name('plans.edit');
    Route::match(['put', 'patch'], 'plans/{plan}', [SubscriptionPlanController::class, 'update'])->name('plans.update');
    Route::delete('plans/{plan}', [SubscriptionPlanController::class, 'destroy'])->name('plans.destroy');

    // Contracts
    Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
    Route::delete('contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');

    // Coupons
    Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::get('coupons/create', [CouponController::class, 'create'])->name('coupons.create');
    Route::post('coupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::get('coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
    Route::match(['put', 'patch'], 'coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
    Route::delete('coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');

    // AI Scanning Jobs
    Route::get('scans', [AiScanningController::class, 'index'])->name('scans.index');
    Route::get('scans/{job}', [AiScanningController::class, 'show'])->name('scans.show');
    Route::delete('scans/{job}', [AiScanningController::class, 'destroy'])->name('scans.destroy');

    // Transactions
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // Refunds
    Route::get('refunds', [RefundController::class, 'index'])->name('refunds.index');
    Route::get('refunds/{refund}', [RefundController::class, 'show'])->name('refunds.show');
    Route::post('refunds/{refund}/approve', [RefundController::class, 'approve'])->name('refunds.approve');
    Route::post('refunds/{refund}/reject', [RefundController::class, 'reject'])->name('refunds.reject');

    // Users
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Banners
    Route::get('banners', [BannerController::class, 'index'])->name('banners.index');
    Route::post('banners', [BannerController::class, 'store'])->name('banners.store');
    Route::match(['put', 'patch'], 'banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
    Route::post('banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');

    // Forum posts
    Route::get('forum', [ForumController::class, 'index'])->name('forum.index');
    Route::get('forum/{post}', [ForumController::class, 'show'])->name('forum.show');
    Route::post('forum/{post}/approve', [ForumController::class, 'approve'])->name('forum.approve');
    Route::post('forum/{post}/reject', [ForumController::class, 'reject'])->name('forum.reject');
    Route::post('forum/{post}/pin', [ForumController::class, 'pin'])->name('forum.pin');
    Route::delete('forum/{post}', [ForumController::class, 'destroy'])->name('forum.destroy');

    // Forum categories
    Route::get('forum-categories', [ForumCategoryController::class, 'index'])->name('forum-categories.index');
    Route::post('forum-categories', [ForumCategoryController::class, 'store'])->name('forum-categories.store');
    Route::match(['put', 'patch'], 'forum-categories/{category}', [ForumCategoryController::class, 'update'])->name('forum-categories.update');
    Route::delete('forum-categories/{category}', [ForumCategoryController::class, 'destroy'])->name('forum-categories.destroy');

    // Files (CKFinder handles management; this is the browse page)
    Route::get('files', [FileController::class, 'index'])->name('files.index');

    // Sellers (verification management)
    Route::get('sellers', [SellerController::class, 'index'])->name('sellers.index');
    Route::get('sellers/{verification}', [SellerController::class, 'show'])->name('sellers.show');
    Route::post('sellers/{verification}/approve', [SellerController::class, 'approve'])->name('sellers.approve');
    Route::post('sellers/{verification}/reject', [SellerController::class, 'reject'])->name('sellers.reject');

    // Chat Groups (admin management)
    Route::get('chat-groups', [ChatGroupController::class, 'index'])->name('chat-groups.index');
    Route::post('chat-groups', [ChatGroupController::class, 'store'])->name('chat-groups.store');
    Route::get('chat-groups/{group}', [ChatGroupController::class, 'show'])->name('chat-groups.show');
    Route::post('chat-groups/{group}/approve-member/{user}', [ChatGroupController::class, 'approveMember'])->name('chat-groups.approve-member');
    Route::post('chat-groups/{group}/reject-member/{user}', [ChatGroupController::class, 'rejectMember'])->name('chat-groups.reject-member');
    Route::delete('chat-groups/{group}', [ChatGroupController::class, 'destroy'])->name('chat-groups.destroy');
});
