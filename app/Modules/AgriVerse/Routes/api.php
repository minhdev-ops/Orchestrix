<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AgriVerse\Http\Controllers\Api\ProductController;
use App\Modules\AgriVerse\Http\Controllers\Api\ThreeDAssetController;
use App\Modules\AgriVerse\Http\Controllers\Api\SubscriptionController;
use App\Modules\AgriVerse\Http\Controllers\Api\StoreController;
use App\Modules\AgriVerse\Http\Controllers\Api\SubscriptionPlanController;
use App\Modules\AgriVerse\Http\Controllers\Api\DigitalPassportController;
use App\Modules\AgriVerse\Http\Controllers\Api\DigitalPassportLogController;
use App\Modules\AgriVerse\Http\Controllers\Api\OrderController;
use App\Modules\AgriVerse\Http\Controllers\Api\ContractController;
use App\Modules\AgriVerse\Http\Controllers\Api\CartController;
use App\Modules\AgriVerse\Http\Controllers\Api\WishlistController;
use App\Modules\AgriVerse\Http\Controllers\Api\CategoryController;
use App\Modules\AgriVerse\Http\Controllers\Api\ReviewController;
use App\Modules\AgriVerse\Http\Controllers\Api\NotificationController;
use App\Modules\AgriVerse\Http\Controllers\Api\CouponController;
use App\Modules\AgriVerse\Http\Controllers\Api\ReportController;
use App\Modules\AgriVerse\Http\Controllers\Api\AiScanningController;
use App\Modules\AgriVerse\Http\Controllers\Api\PlantDoctorController;
use App\Modules\AgriVerse\Http\Controllers\Api\SellerDashboardController;
use App\Modules\AgriVerse\Http\Controllers\Api\ChatController;
use App\Modules\AgriVerse\Http\Controllers\Api\ForumController;
use App\Modules\AgriVerse\Http\Controllers\Api\AnalyticsController;

Route::middleware(['api', 'auth:api'])->prefix('api')->group(function () {
    // Products
    Route::middleware('permission:product.view')->group(function () {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{product}', [ProductController::class, 'show']);
    });
    Route::post('products', [ProductController::class, 'store'])->middleware('permission:product.create');
    Route::match(['put', 'patch'], 'products/{product}', [ProductController::class, 'update'])->middleware('permission:product.edit');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->middleware('permission:product.delete');
    Route::get('products/{product}/assets', [ThreeDAssetController::class, 'index']);

    // Digital Passport Logs (separate from product-scoped)
    Route::middleware('permission:product.view')->group(function () {
        Route::get('digital-passport-logs', [DigitalPassportLogController::class, 'index']);
        Route::get('digital-passport-logs/{log}', [DigitalPassportLogController::class, 'show']);
    });
    Route::post('digital-passport-logs', [DigitalPassportLogController::class, 'store'])->middleware('permission:product.edit');

    // 3D Assets
    Route::middleware('permission:asset.view')->group(function () {
        Route::get('assets', [ThreeDAssetController::class, 'index']);
        Route::get('assets/{asset}', [ThreeDAssetController::class, 'show']);
    });
    Route::post('assets', [ThreeDAssetController::class, 'store'])->middleware('permission:asset.upload');
    Route::match(['put', 'patch'], 'assets/{asset}', [ThreeDAssetController::class, 'update'])->middleware('permission:asset.edit');
    Route::delete('assets/{asset}', [ThreeDAssetController::class, 'destroy'])->middleware('permission:asset.delete');
    Route::post('assets/{asset}/compress', [ThreeDAssetController::class, 'compress'])->middleware('permission:asset.compress');

    // Orders
    Route::middleware('permission:order.view')->group(function () {
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
    });
    Route::post('orders', [OrderController::class, 'store'])->middleware('permission:order.create');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->middleware('permission:order.edit');
    Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])->middleware('permission:order.edit');
    Route::post('orders/{order}/deliver', [OrderController::class, 'deliver'])->middleware('permission:order.edit');
    Route::post('orders/{order}/complete', [OrderController::class, 'complete'])->middleware('permission:order.edit');

    // Contracts
    Route::middleware('permission:contract.view')->group(function () {
        Route::get('contracts', [ContractController::class, 'index']);
        Route::get('contracts/{contract}', [ContractController::class, 'show']);
    });
    Route::post('contracts', [ContractController::class, 'store'])->middleware('permission:contract.create');
    Route::post('contracts/{contract}/sign', [ContractController::class, 'sign'])->middleware('permission:contract.edit');
    Route::get('contracts/{contract}/pdf', [ContractController::class, 'downloadPdf'])->middleware('permission:contract.view');

    // Subscriptions
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->middleware('permission:subscription.view');
    Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show'])->middleware('permission:subscription.view');
    Route::post('subscriptions', [SubscriptionController::class, 'store'])->middleware('permission:subscription.create');
    Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->middleware('permission:subscription.edit');

    // Stores
    Route::middleware('permission:store.view')->group(function () {
        Route::get('stores', [StoreController::class, 'index']);
        Route::get('stores/{store}', [StoreController::class, 'show']);
    });

    // Subscription Plans
    Route::middleware('permission:plan.view')->group(function () {
        Route::get('subscription-plans', [SubscriptionPlanController::class, 'index']);
        Route::get('subscription-plans/{plan}', [SubscriptionPlanController::class, 'show']);
    });

    // Categories (public read)
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    Route::get('categories/{category}/products', [CategoryController::class, 'products']);

    // Cart
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart', [CartController::class, 'store']);
    Route::put('cart/{cart}', [CartController::class, 'update']);
    Route::delete('cart/{cart}', [CartController::class, 'destroy']);
    Route::delete('cart', [CartController::class, 'clear']);

    // Wishlist
    Route::get('wishlist', [WishlistController::class, 'index']);
    Route::post('wishlist', [WishlistController::class, 'store']);
    Route::delete('wishlist/{wishlist}', [WishlistController::class, 'destroy']);

    // Reviews (public read)
    Route::get('products/{product}/reviews', [ReviewController::class, 'index']);
    Route::post('products/{product}/reviews', [ReviewController::class, 'store'])->middleware('permission:order.view');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);

    // Coupons
    Route::get('coupons', [CouponController::class, 'index']);
    Route::get('coupons/{coupon}', [CouponController::class, 'show']);
    Route::post('coupons/validate', [CouponController::class, 'validate']);

    // Reports
    Route::get('reports/seller/revenue', [ReportController::class, 'sellerRevenue']);
    Route::get('reports/buyer/stats', [ReportController::class, 'buyerStats']);

    // === PHASE 11: 3D Assets & AI Services ===

    // 3D Model endpoints (public read)
    Route::get('products/{product}/3d-model', [ThreeDAssetController::class, 'model']);
    Route::get('products/{product}/ar-config', [ThreeDAssetController::class, 'arConfig']);

    // AI Scanning Service
    Route::post('services/3d-scan', [AiScanningController::class, 'requestScan']);
    Route::get('services/scan-status/{job}', [AiScanningController::class, 'scanStatus']);
    Route::get('services/my-scans', [AiScanningController::class, 'myJobs']);

    // Digital Passport (per-product)
    Route::get('products/{product}/digital-passport', [DigitalPassportController::class, 'index']);
    Route::post('products/{product}/digital-passport', [DigitalPassportController::class, 'store'])->middleware('permission:product.edit');
    Route::put('passport/{passport}', [DigitalPassportController::class, 'update'])->middleware('permission:product.edit');
    Route::delete('passport/{passport}', [DigitalPassportController::class, 'destroy'])->middleware('permission:product.edit');
    Route::get('products/{product}/passport-summary', [DigitalPassportController::class, 'summary']);

    // Seller Dashboard
    Route::get('seller/dashboard/stats', [SellerDashboardController::class, 'stats']);

    // Admin endpoints
    Route::middleware('permission:admin.access')->prefix('admin')->group(function () {
        Route::get('commissions', [\App\Http\Controllers\Admin\ReportController::class, 'commission']);
        Route::get('revenue', [\App\Http\Controllers\Admin\ReportController::class, 'revenue']);
        Route::get('sellers', [\App\Http\Controllers\Admin\ReportController::class, 'sellers']);
    });

    // Plant Doctor (AI Diagnosis)
    Route::prefix('plant-doctor')->group(function () {
        Route::post('diagnose', [PlantDoctorController::class, 'diagnose']);
        Route::get('history', [PlantDoctorController::class, 'history']);
        Route::get('history/{diagnosis}', [PlantDoctorController::class, 'show']);
        Route::delete('history/{diagnosis}', [PlantDoctorController::class, 'destroy']);
    });

    // Chat
    Route::prefix('chat')->group(function () {
        Route::get('conversations', [ChatController::class, 'conversations']);
        Route::post('conversations', [ChatController::class, 'startConversation']);
        Route::get('conversations/{conversation}/messages', [ChatController::class, 'messages']);
        Route::post('conversations/{conversation}/messages', [ChatController::class, 'sendMessage']);
        Route::get('groups/{group}/messages', [ChatController::class, 'groupMessages']);
        Route::post('groups/{group}/messages', [ChatController::class, 'sendGroupMessage']);
    });

    // Analytics
    Route::post('analytics/track', [AnalyticsController::class, 'track']);
    Route::post('analytics/page-view', [AnalyticsController::class, 'pageView']);
    Route::post('analytics/product-view/{product}', [AnalyticsController::class, 'productView']);
    Route::post('analytics/add-to-cart', [AnalyticsController::class, 'addToCart']);
    Route::get('analytics/recently-viewed', [AnalyticsController::class, 'recentlyViewed']);

    // Forum
    Route::prefix('forum')->group(function () {
        Route::get('categories', [ForumController::class, 'categories']);
        Route::get('posts', [ForumController::class, 'posts']);
        Route::post('posts', [ForumController::class, 'store']);
        Route::get('posts/{post}', [ForumController::class, 'show']);
        Route::match(['put', 'patch'], 'posts/{post}', [ForumController::class, 'update']);
        Route::delete('posts/{post}', [ForumController::class, 'destroy']);
        Route::get('posts/{post}/comments', [ForumController::class, 'comments']);
        Route::post('posts/{post}/comments', [ForumController::class, 'addComment']);
        Route::post('posts/{post}/like', [ForumController::class, 'toggleLike']);
        Route::get('my-posts', [ForumController::class, 'myPosts']);
    });
});
