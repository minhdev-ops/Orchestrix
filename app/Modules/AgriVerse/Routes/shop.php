<?php

use App\Modules\AgriVerse\Http\Controllers\Shop\AddressController;
use App\Modules\AgriVerse\Http\Controllers\Shop\AffiliateController;
use App\Modules\AgriVerse\Http\Controllers\Shop\AIExpertController;
use App\Modules\AgriVerse\Http\Controllers\Shop\AirQualityController;
use App\Modules\AgriVerse\Http\Controllers\Shop\CartController;
use App\Modules\AgriVerse\Http\Controllers\Shop\ChatController;
use App\Modules\AgriVerse\Http\Controllers\Shop\CheckoutController;
use App\Modules\AgriVerse\Http\Controllers\Shop\ContractController;
use App\Modules\AgriVerse\Http\Controllers\Shop\DiagnosticController;
use App\Modules\AgriVerse\Http\Controllers\Shop\ForumController;
use App\Modules\AgriVerse\Http\Controllers\Shop\ForumUploadController;
use App\Modules\AgriVerse\Http\Controllers\Shop\GHTKAddressController;
use App\Modules\AgriVerse\Http\Controllers\Shop\HomeController;
use App\Modules\AgriVerse\Http\Controllers\Shop\OrderController;
use App\Modules\AgriVerse\Http\Controllers\Shop\GardenController;
use App\Modules\AgriVerse\Http\Controllers\Shop\PageController;
use App\Modules\AgriVerse\Http\Controllers\Shop\PaymentController;
use App\Modules\AgriVerse\Http\Controllers\Shop\ProductController;
use App\Modules\AgriVerse\Http\Controllers\Shop\QuizController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerDashboardController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerOrderController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerProductController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerReviewController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerShippingController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerStoreController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerVerificationController;
use App\Modules\AgriVerse\Http\Controllers\Shop\StoreController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SupportController;
use App\Modules\AgriVerse\Http\Controllers\Shop\TwoFactorController;
use App\Modules\AgriVerse\Http\Controllers\Shop\WishlistController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/agriverse/demo-seed', function () {
    if (app()->environment('production')) {
        abort(403, 'Cannot run in production');
    }

    try {
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Database has been migrated and seeded successfully!',
            'output' => Artisan::output(),
        ]);
    } catch (Exception $e) {
        Log::error('Demo seed error: '.$e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Đã xảy ra lỗi khi seed dữ liệu.',
        ], 500);
    }
});

Route::prefix('agriverse')->name('agriverse.shop.')->group(function () {
    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Products
    Route::get('san-pham', [ProductController::class, 'index'])->name('products.index');
    Route::get('san-pham/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('so-sanh', [ProductController::class, 'compare'])->name('compare.index');

    // Stores
    Route::get('cua-hang', [StoreController::class, 'index'])->name('stores.index');
    Route::get('cua-hang/{store}', [StoreController::class, 'show'])->name('stores.show');

    // Categories
    Route::get('danh-muc', [ProductController::class, 'categories'])->name('categories.index');

    // New feature pages (static / design preview)
    Route::get('tai-khoan/cai-dat', [PageController::class, 'accountSettings'])->name('account.settings')->middleware('auth');
    Route::get('dien-dan/tao-bai-viet', [ForumController::class, 'create'])->name('forum.create')->middleware('auth');
    Route::get('dien-dan/{post}', [ForumController::class, 'show'])->name('forum.show');
    Route::get('dien-dan', [ForumController::class, 'index'])->name('forum.index');
    Route::get('ho-so/{user?}', [PageController::class, 'profile'])->name('profile.index')->middleware('auth');
    // Garden
    Route::prefix('khu-vuon')->name('garden.')->group(function () {
        Route::get('/', [GardenController::class, 'index'])->name('index')->middleware('auth');
        Route::post('plants/{plant}/water', [GardenController::class, 'water'])->name('plants.water')->middleware('auth');
        Route::post('plants/{plant}/fertilize', [GardenController::class, 'fertilize'])->name('plants.fertilize')->middleware('auth');
        Route::put('plants/{plant}/move', [GardenController::class, 'move'])->name('plants.move')->middleware('auth');
        Route::put('plants/{plant}/stage', [GardenController::class, 'updateStage'])->name('plants.stage')->middleware('auth');
        Route::delete('plants/{plant}', [GardenController::class, 'destroy'])->name('plants.destroy')->middleware('auth');
    });
    Route::get('cay-tim-nguoi', [PageController::class, 'quiz'])->name('quiz.index');
    Route::get('chan-doan', [PageController::class, 'diagnostic'])->name('diagnostic.index');
    Route::get('theo-doi-van-chuyen', [OrderController::class, 'tracking'])->name('tracking.index')->middleware('auth');
    Route::get('ho-tro', [PageController::class, 'support'])->name('support.index');
    Route::get('phat-trien-ben-vung', [PageController::class, 'sustainability'])->name('sustainability.index');
    Route::get('bai-viet', [PageController::class, 'journalIndex'])->name('journal.index');
    Route::get('bai-viet/{article?}', [PageController::class, 'journal'])->name('journal.show');
    Route::get('404', [PageController::class, 'notFound'])->name('not-found');

    // Air quality (public, with lat/lng)
    Route::get('khong-khi', [AirQualityController::class, 'index'])->name('air-quality');

    // AI Expert chat (public)
    Route::post('ai-chat', [AIExpertController::class, 'chat'])->name('ai.chat');

    // Consultation booking (public)
    Route::post('ho-tro/dat-lich', [SupportController::class, 'storeBooking'])->name('support.booking');

    // Diagnostic analyze (public)
    Route::post('api/diagnostic/analyze', [DiagnosticController::class, 'analyze'])->name('diagnostic.analyze');

    // Quiz recommendation (public)
    Route::post('api/quiz/recommend', [QuizController::class, 'recommend'])->name('quiz.recommend');

    // AR / 3D Viewer
    Route::get('xem-3d/{product}', [PageController::class, 'arViewer'])->name('ar-viewer');

    // Payment IPN/callbacks (must be outside auth — payment gateways send server-to-server)
    Route::get('thanh-toan/{order}/vnpay-callback', [PaymentController::class, 'vnpayCallback'])->name('payment.vnpay-callback');
    Route::post('thanh-toan/{order}/vnpay-ipn', [PaymentController::class, 'vnpayIpn'])->name('payment.vnpay-ipn');
    Route::get('thanh-toan/{order}/momo-callback', [PaymentController::class, 'momoCallback'])->name('payment.momo-callback');
    Route::post('thanh-toan/{order}/momo-ipn', [PaymentController::class, 'momoIpn'])->name('payment.momo-ipn');

    // Cart & notifications (auth required)
    Route::middleware('auth')->group(function () {
        Route::get('thong-bao', [PageController::class, 'notifications'])->name('notifications.index');
        Route::get('gio-hang', [CartController::class, 'index'])->name('cart.index');
        Route::get('thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::get('thanh-toan/thanh-cong/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
        Route::get('don-hang', [OrderController::class, 'index'])->name('orders.index');
        Route::get('don-hang/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('yeu-thich', [WishlistController::class, 'index'])->name('wishlist.index');
        // Forum
        Route::post('dien-dan', [ForumController::class, 'store'])->name('forum.store');
        Route::post('dien-dan/upload', [ForumUploadController::class, 'uploadImage'])->name('forum.upload');
        Route::get('dien-dan/images', [ForumUploadController::class, 'listImages'])->name('forum.images');
        Route::get('dien-dan/{post}/sua', [ForumController::class, 'edit'])->name('forum.edit');
        Route::match(['put', 'patch'], 'dien-dan/{post}', [ForumController::class, 'update'])->name('forum.update');
        Route::delete('dien-dan/{post}', [ForumController::class, 'destroy'])->name('forum.destroy');
        Route::post('forum/{post}/comments', [ForumController::class, 'storeComment'])->name('forum.comment');
        Route::post('forum/{post}/like', [ForumController::class, 'toggleLike'])->name('forum.like');

        // Addresses
        Route::get('dia-chi', [AddressController::class, 'index'])->name('addresses.index');
        Route::get('dia-chi/them-moi', [AddressController::class, 'create'])->name('addresses.create');
        Route::post('dia-chi', [AddressController::class, 'store'])->name('addresses.store');
        Route::get('dia-chi/{address}/sua', [AddressController::class, 'edit'])->name('addresses.edit');
        Route::match(['put', 'patch'], 'dia-chi/{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('dia-chi/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::post('dia-chi/{address}/mac-dinh', [AddressController::class, 'setDefault'])->name('addresses.set-default');

        // Seller registration & email verification
        Route::get('seller/register', [SellerVerificationController::class, 'showForm'])->name('seller.register');
        Route::post('seller/register/send-code', [SellerVerificationController::class, 'sendVerificationCode'])->name('seller.register.send-code');
        Route::post('seller/register/verify-code', [SellerVerificationController::class, 'verifyCode'])->name('seller.register.verify-code');
        Route::post('seller/register', [SellerVerificationController::class, 'submit'])->name('seller.register.submit');
        Route::get('seller/status', [SellerVerificationController::class, 'status'])->name('seller.status');

        // Seller products
        Route::get('seller/products', [SellerProductController::class, 'index'])->name('seller.products.index');
        Route::get('seller/products/create', [SellerProductController::class, 'create'])->name('seller.products.create');
        Route::post('seller/products', [SellerProductController::class, 'store'])->name('seller.products.store');
        Route::get('seller/products/{product}/edit', [SellerProductController::class, 'edit'])->name('seller.products.edit');
        Route::match(['put', 'patch'], 'seller/products/{product}', [SellerProductController::class, 'update'])->name('seller.products.update');
        Route::delete('seller/products/{product}', [SellerProductController::class, 'destroy'])->name('seller.products.destroy');

        // Seller dashboard
        Route::get('seller/dashboard', [SellerDashboardController::class, 'index'])->name('seller.dashboard');

        // Seller store management
        Route::get('seller/store', [SellerStoreController::class, 'edit'])->name('seller.store.edit');
        Route::match(['put', 'patch'], 'seller/store', [SellerStoreController::class, 'update'])->name('seller.store.update');

        // Seller reviews
        Route::get('seller/reviews', [SellerReviewController::class, 'index'])->name('seller.reviews.index');

        // Payment
        Route::get('thanh-toan/{order}', [PaymentController::class, 'index'])->name('payment.index');
        Route::post('thanh-toan/{order}/process', [PaymentController::class, 'process'])->name('payment.process');
        Route::get('thanh-toan/{order}/chuyen-khoan', [PaymentController::class, 'banking'])->name('payment.banking');
        Route::post('thanh-toan/{order}/upload-proof', [PaymentController::class, 'uploadProof'])->name('payment.upload-proof');

        // Affiliate

        // Affiliate
        Route::get('affiliate', [AffiliateController::class, 'index'])->name('affiliate.index');
        Route::post('affiliate/register', [AffiliateController::class, 'register'])->name('affiliate.register');
        Route::get('affiliate/link', [AffiliateController::class, 'getLink'])->name('affiliate.link');
        Route::get('affiliate/stats', [AffiliateController::class, 'stats'])->name('affiliate.stats');

        // Two-Factor Authentication
        Route::get('cai-dat/2fa', [TwoFactorController::class, 'index'])->name('2fa.index');
        Route::post('cai-dat/2fa/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
        Route::post('cai-dat/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('cai-dat/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
        Route::post('cai-dat/2fa/regenerate-recovery', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('2fa.regenerate-recovery');
        Route::post('cai-dat/2fa/verify-login', [TwoFactorController::class, 'verifyLogin'])->name('2fa.verify-login');

        // Seller orders
        Route::get('seller/orders', [SellerOrderController::class, 'index'])->name('seller.orders.index');
        Route::get('seller/orders/{order}', [SellerOrderController::class, 'show'])->name('seller.orders.show');
        Route::post('seller/orders/{order}/confirm', [SellerOrderController::class, 'confirm'])->name('seller.orders.confirm');
        Route::post('seller/orders/{order}/ship', [SellerOrderController::class, 'ship'])->name('seller.orders.ship');
        Route::post('seller/orders/{order}/deliver', [SellerOrderController::class, 'deliver'])->name('seller.orders.deliver');
        Route::post('seller/orders/{order}/cancel', [SellerOrderController::class, 'cancel'])->name('seller.orders.cancel');
        Route::get('seller/orders/{order}/shipping', [SellerShippingController::class, 'services'])->name('seller.orders.shipping');
        Route::post('seller/orders/{order}/create-shipment', [SellerShippingController::class, 'createShipment'])->name('seller.orders.create-shipment');
    });
});

// API endpoints for cart/wishlist/checkout/order actions
// Cart add & wishlist toggle must be public for guest support; other ops require auth
Route::post('agriverse/api/cart/add', [CartController::class, 'add'])->name('agriverse.api.cart.add');
Route::post('agriverse/api/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('agriverse.api.wishlist.toggle');

Route::middleware('auth')->prefix('agriverse/api')->name('agriverse.api.')->group(function () {
    // Cart (auth-only operations)
    Route::put('cart/{cart}/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/{cart}/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('cart/{product}/buy-now', [CartController::class, 'buyNow'])->name('cart.buy-now');

    // Checkout
    Route::post('checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    // Wishlist
    Route::delete('wishlist/{wishlist}/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');

    // Order actions
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{order}/refund', [OrderController::class, 'requestRefund'])->name('orders.refund');
    Route::post('orders/{order}/confirm-received', [OrderController::class, 'confirmReceived'])->name('orders.confirm-received');
    Route::post('tracking/lookup', [OrderController::class, 'lookupTracking'])->name('tracking.lookup');

    // Chat (order-scoped)
    Route::get('orders/{order}/chat', [ChatController::class, 'index'])->name('orders.chat');
    Route::post('orders/{order}/chat', [ChatController::class, 'store'])->name('orders.chat.send');

    // Chat (product-scoped, conversation-based)
    Route::get('chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
    Route::get('chat/{conversation}/messages', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('chat/{conversation}/send', [ChatController::class, 'send'])->name('chat.send');
    Route::post('chat/start', [ChatController::class, 'start'])->name('chat.start');
    Route::get('chat/pickable-products', [ChatController::class, 'pickableProducts'])->name('chat.pickable');

    // Group Chat
    Route::get('chat/groups', [ChatController::class, 'groups'])->name('chat.groups');
    Route::post('chat/groups', [ChatController::class, 'createGroup'])->name('chat.groups.create');
    Route::post('chat/groups/{group}/join', [ChatController::class, 'joinGroup'])->name('chat.groups.join');
    Route::get('chat/groups/{group}/messages', [ChatController::class, 'groupMessages'])->name('chat.groups.messages');
    Route::post('chat/groups/{group}/send', [ChatController::class, 'sendGroup'])->name('chat.groups.send');
    Route::post('chat/groups/{group}/add-member', [ChatController::class, 'addMember'])->name('chat.groups.add-member');
    Route::get('chat/groups/{group}/members', [ChatController::class, 'groupMembers'])->name('chat.groups.members');
    Route::get('chat/groups/{group}/pending-members', [ChatController::class, 'pendingMembers'])->name('chat.groups.pending-members');
    Route::post('chat/groups/{group}/approve-member/{user}', [ChatController::class, 'approveMember'])->name('chat.groups.approve-member');
    Route::post('chat/groups/{group}/reject-member/{user}', [ChatController::class, 'rejectMember'])->name('chat.groups.reject-member');
    Route::get('chat/search-users', [ChatController::class, 'searchUsers'])->name('chat.search-users');

    // GHTK address lookup & shipping fee (thay thế GHN)
    Route::get('ghtk/provinces', [GHTKAddressController::class, 'provinces'])->name('ghtk.provinces');
    Route::post('ghtk/districts', [GHTKAddressController::class, 'districts'])->name('ghtk.districts');
    Route::post('ghtk/wards', [GHTKAddressController::class, 'wards'])->name('ghtk.wards');
    Route::post('ghtk/shipping-fee', [GHTKAddressController::class, 'shippingFee'])->name('ghtk.shipping-fee');

    // Address API (JSON for async operations)
    Route::post('addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('addresses.api.set-default');

    // Settings
    Route::post('settings/notifications', [PageController::class, 'saveNotificationPreferences'])->name('settings.notifications');
    Route::post('settings/profile', [PageController::class, 'updateProfile'])->name('settings.profile');
    Route::post('settings/password', [PageController::class, 'changePassword'])->name('settings.password');
});

// Contracts (shop view)
Route::middleware('auth')->prefix('agriverse')->name('agriverse.shop.')->group(function () {
    Route::get('hop-dong/{contract}', [ContractController::class, 'show'])->name('contracts.show');
});

// Catch-all for unmatched agriverse routes (must be last)
Route::match(['get', 'post'], 'agriverse/{any}', [PageController::class, 'notFound'])->where('any', '.*');
