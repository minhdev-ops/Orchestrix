<?php

use App\Modules\AgriVerse\Http\Controllers\Api\AiScanningController;
use App\Modules\AgriVerse\Http\Controllers\Api\AnalyticsController;
use App\Modules\AgriVerse\Http\Controllers\Api\CartController;
use App\Modules\AgriVerse\Http\Controllers\Api\CategoryController;
use App\Modules\AgriVerse\Http\Controllers\Api\ChatController;
use App\Modules\AgriVerse\Http\Controllers\Api\ContractController;
use App\Modules\AgriVerse\Http\Controllers\Api\CouponController;
use App\Modules\AgriVerse\Http\Controllers\Api\DigitalPassportController;
use App\Modules\AgriVerse\Http\Controllers\Api\DigitalPassportLogController;
use App\Modules\AgriVerse\Http\Controllers\Api\ForumController;
use App\Modules\AgriVerse\Http\Controllers\Api\NotificationController;
use App\Modules\AgriVerse\Http\Controllers\Api\OrderController;
use App\Modules\AgriVerse\Http\Controllers\Api\PlantDoctorController;
use App\Modules\AgriVerse\Http\Controllers\Api\ProductController;
use App\Modules\AgriVerse\Http\Controllers\Api\ReportController;
use App\Modules\AgriVerse\Http\Controllers\Api\ReviewController;
use App\Modules\AgriVerse\Http\Controllers\Api\SellerDashboardController;
use App\Modules\AgriVerse\Http\Controllers\Api\StoreController;
use App\Modules\AgriVerse\Http\Controllers\Api\SubscriptionController;
use App\Modules\AgriVerse\Http\Controllers\Api\SubscriptionPlanController;
use App\Modules\AgriVerse\Http\Controllers\Api\ThreeDAssetController;
use App\Modules\AgriVerse\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:api', 'throttle:api'])->prefix('api')->name('api.')->group(function () {
    // Products
    Route::middleware('permission:product.view')->group(function () {
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    });
    Route::post('products', [ProductController::class, 'store'])->name('products.store')->middleware('permission:product.create');
    Route::match(['put', 'patch'], 'products/{product}', [ProductController::class, 'update'])->name('products.update')->middleware('permission:product.edit');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy')->middleware('permission:product.delete');
    Route::get('products/{product}/assets', [ThreeDAssetController::class, 'index'])->name('products.assets');

    // Digital Passport Logs
    Route::middleware('permission:product.view')->group(function () {
        Route::get('digital-passport-logs', [DigitalPassportLogController::class, 'index'])->name('passport-logs.index');
        Route::get('digital-passport-logs/{log}', [DigitalPassportLogController::class, 'show'])->name('passport-logs.show');
    });
    Route::post('digital-passport-logs', [DigitalPassportLogController::class, 'store'])->name('passport-logs.store')->middleware('permission:product.edit');

    // 3D Assets
    Route::middleware('permission:asset.view')->group(function () {
        Route::get('assets', [ThreeDAssetController::class, 'index'])->name('assets.index');
        Route::get('assets/{asset}', [ThreeDAssetController::class, 'show'])->name('assets.show');
    });
    Route::post('assets', [ThreeDAssetController::class, 'store'])->name('assets.store')->middleware('permission:asset.upload');
    Route::match(['put', 'patch'], 'assets/{asset}', [ThreeDAssetController::class, 'update'])->name('assets.update')->middleware('permission:asset.edit');
    Route::delete('assets/{asset}', [ThreeDAssetController::class, 'destroy'])->name('assets.destroy')->middleware('permission:asset.delete');
    Route::post('assets/{asset}/compress', [ThreeDAssetController::class, 'compress'])->name('assets.compress')->middleware('permission:asset.compress');

    // Orders
    Route::middleware('permission:order.view')->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store')->middleware('permission:order.create');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel')->middleware('permission:order.edit');
    Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm')->middleware('permission:order.edit');
    Route::post('orders/{order}/deliver', [OrderController::class, 'deliver'])->name('orders.deliver')->middleware('permission:order.edit');
    Route::post('orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete')->middleware('permission:order.edit');

    // Contracts
    Route::middleware('permission:contract.view')->group(function () {
        Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::get('contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
    });
    Route::post('contracts', [ContractController::class, 'store'])->name('contracts.store')->middleware('permission:contract.create');
    Route::post('contracts/{contract}/sign', [ContractController::class, 'sign'])->name('contracts.sign')->middleware('permission:contract.edit');
    Route::get('contracts/{contract}/pdf', [ContractController::class, 'downloadPdf'])->name('contracts.pdf')->middleware('permission:contract.view');

    // Subscriptions
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index')->middleware('permission:subscription.view');
    Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show')->middleware('permission:subscription.view');
    Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store')->middleware('permission:subscription.create');
    Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel')->middleware('permission:subscription.edit');

    // Stores
    Route::middleware('permission:store.view')->group(function () {
        Route::get('stores', [StoreController::class, 'index'])->name('stores.index');
        Route::get('stores/{store}', [StoreController::class, 'show'])->name('stores.show');
    });

    // Subscription Plans
    Route::middleware('permission:plan.view')->group(function () {
        Route::get('subscription-plans', [SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
        Route::get('subscription-plans/{plan}', [SubscriptionPlanController::class, 'show'])->name('subscription-plans.show');
    });

    // Categories
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('categories/{category}/products', [CategoryController::class, 'products'])->name('categories.products');

    // Cart
    Route::get('cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('cart', [CartController::class, 'clear'])->name('cart.clear');

    // Wishlist
    Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Reviews
    Route::get('products/{product}/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('permission:order.view');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Coupons
    Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::get('coupons/{coupon}', [CouponController::class, 'show'])->name('coupons.show');
    Route::post('coupons/validate', [CouponController::class, 'validate'])->name('coupons.validate');

    // Reports
    Route::get('reports/seller/revenue', [ReportController::class, 'sellerRevenue'])->name('reports.seller-revenue');
    Route::get('reports/buyer/stats', [ReportController::class, 'buyerStats'])->name('reports.buyer-stats');

    // 3D Model endpoints
    Route::get('products/{product}/3d-model', [ThreeDAssetController::class, 'model'])->name('products.3d-model');
    Route::get('products/{product}/ar-config', [ThreeDAssetController::class, 'arConfig'])->name('products.ar-config');

    // AI Scanning Service
    Route::post('services/3d-scan', [AiScanningController::class, 'requestScan'])->name('services.scan');
    Route::get('services/scan-status/{job}', [AiScanningController::class, 'scanStatus'])->name('services.scan-status');
    Route::get('services/my-scans', [AiScanningController::class, 'myJobs'])->name('services.my-scans');

    // Digital Passport
    Route::get('products/{product}/digital-passport', [DigitalPassportController::class, 'index'])->name('passport.index');
    Route::post('products/{product}/digital-passport', [DigitalPassportController::class, 'store'])->name('passport.store')->middleware('permission:product.edit');
    Route::put('passport/{passport}', [DigitalPassportController::class, 'update'])->name('passport.update')->middleware('permission:product.edit');
    Route::delete('passport/{passport}', [DigitalPassportController::class, 'destroy'])->name('passport.destroy')->middleware('permission:product.edit');
    Route::get('products/{product}/passport-summary', [DigitalPassportController::class, 'summary'])->name('passport.summary');

    // Seller Dashboard
    Route::get('seller/dashboard/stats', [SellerDashboardController::class, 'stats'])->name('seller.stats');

    // Admin endpoints
    Route::middleware('permission:admin.access')->prefix('admin')->name('admin.')->group(function () {
        Route::get('commissions', [App\Http\Controllers\Admin\ReportController::class, 'commission'])->name('commissions');
        Route::get('revenue', [App\Http\Controllers\Admin\ReportController::class, 'revenue'])->name('revenue');
        Route::get('sellers', [App\Http\Controllers\Admin\ReportController::class, 'sellers'])->name('sellers');
    });

    // Plant Doctor (AI Diagnosis)
    Route::prefix('plant-doctor')->name('plant-doctor.')->group(function () {
        Route::post('diagnose', [PlantDoctorController::class, 'diagnose'])->name('diagnose');
        Route::get('history', [PlantDoctorController::class, 'history'])->name('history');
        Route::get('history/{diagnosis}', [PlantDoctorController::class, 'show'])->name('show');
        Route::delete('history/{diagnosis}', [PlantDoctorController::class, 'destroy'])->name('destroy');
    });

    // Chat
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('conversations', [ChatController::class, 'conversations'])->name('conversations');
        Route::post('conversations', [ChatController::class, 'startConversation'])->name('conversations.start');
        Route::get('conversations/{conversation}/messages', [ChatController::class, 'messages'])->name('messages');
        Route::post('conversations/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('messages.send');
        Route::get('groups/{group}/messages', [ChatController::class, 'groupMessages'])->name('group.messages');
        Route::post('groups/{group}/messages', [ChatController::class, 'sendGroupMessage'])->name('group.messages.send');
    });

    // Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::post('track', [AnalyticsController::class, 'track'])->name('track');
        Route::post('page-view', [AnalyticsController::class, 'pageView'])->name('page-view');
        Route::post('product-view/{product}', [AnalyticsController::class, 'productView'])->name('product-view');
        Route::post('add-to-cart', [AnalyticsController::class, 'addToCart'])->name('add-to-cart');
        Route::get('recently-viewed', [AnalyticsController::class, 'recentlyViewed'])->name('recently-viewed');
    });

    // Forum
    Route::prefix('forum')->name('forum.')->group(function () {
        Route::get('categories', [ForumController::class, 'categories'])->name('categories');
        Route::get('posts', [ForumController::class, 'posts'])->name('posts');
        Route::post('posts', [ForumController::class, 'store'])->name('posts.store');
        Route::get('posts/{post}', [ForumController::class, 'show'])->name('posts.show');
        Route::match(['put', 'patch'], 'posts/{post}', [ForumController::class, 'update'])->name('posts.update');
        Route::delete('posts/{post}', [ForumController::class, 'destroy'])->name('posts.destroy');
        Route::get('posts/{post}/comments', [ForumController::class, 'comments'])->name('posts.comments');
        Route::post('posts/{post}/comments', [ForumController::class, 'addComment'])->name('posts.comments.store');
        Route::post('posts/{post}/like', [ForumController::class, 'toggleLike'])->name('posts.like');
        Route::get('my-posts', [ForumController::class, 'myPosts'])->name('my-posts');
    });
});
