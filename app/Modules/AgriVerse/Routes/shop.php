<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AgriVerse\Http\Controllers\Shop\HomeController;
use App\Modules\AgriVerse\Http\Controllers\Shop\ProductController;
use App\Modules\AgriVerse\Http\Controllers\Shop\StoreController;
use App\Modules\AgriVerse\Http\Controllers\Shop\CartController;
use App\Modules\AgriVerse\Http\Controllers\Shop\OrderController;
use App\Modules\AgriVerse\Http\Controllers\Shop\WishlistController;
use App\Modules\AgriVerse\Http\Controllers\Shop\CheckoutController;
use App\Modules\AgriVerse\Http\Controllers\Shop\AddressController;
use App\Modules\AgriVerse\Http\Controllers\Shop\GHNAddressController;
use App\Modules\AgriVerse\Http\Controllers\Shop\PageController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerVerificationController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerDashboardController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerOrderController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerShippingController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerProductController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerStoreController;
use App\Modules\AgriVerse\Http\Controllers\Shop\SellerReviewController;
use App\Modules\AgriVerse\Http\Controllers\Shop\ChatController;
use App\Modules\AgriVerse\Http\Controllers\Shop\ForumController;
use App\Modules\AgriVerse\Http\Controllers\Shop\AirQualityController;

Route::prefix('agriverse')->name('agriverse.shop.')->group(function () {
    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Products
    Route::get('san-pham', [ProductController::class, 'index'])->name('products.index');
    Route::get('san-pham/{product}', [ProductController::class, 'show'])->name('products.show');

    // Stores
    Route::get('cua-hang', [StoreController::class, 'index'])->name('stores.index');
    Route::get('cua-hang/{store}', [StoreController::class, 'show'])->name('stores.show');

    // Categories
    Route::get('danh-muc', [ProductController::class, 'categories'])->name('categories.index');

    // New feature pages (static / design preview)
    Route::get('tai-khoan/cai-dat', [PageController::class, 'accountSettings'])->name('account.settings');
    Route::get('dien-dan/tao-bai-viet', [ForumController::class, 'create'])->name('forum.create')->middleware('auth');
    Route::get('dien-dan/{post}', [ForumController::class, 'show'])->name('forum.show');
    Route::get('dien-dan', [ForumController::class, 'index'])->name('forum.index');
    Route::get('ho-so/{user?}', [PageController::class, 'profile'])->name('profile.index');
    Route::get('khu-vuon', [PageController::class, 'garden'])->name('garden.index');
    Route::get('cay-tim-nguoi', [PageController::class, 'quiz'])->name('quiz.index');
    Route::get('chan-doan', [PageController::class, 'diagnostic'])->name('diagnostic.index');
    Route::get('theo-doi-van-chuyen', [OrderController::class, 'tracking'])->name('tracking.index');
    Route::get('ho-tro', [PageController::class, 'support'])->name('support.index');
    Route::get('phat-trien-ben-vung', [PageController::class, 'sustainability'])->name('sustainability.index');
    Route::get('bai-viet/{article?}', [PageController::class, 'journal'])->name('journal.show');
    Route::get('404', [PageController::class, 'notFound'])->name('not-found');

    // Air quality (public, with lat/lng)
    Route::get('khong-khi', [AirQualityController::class, 'index'])->name('air-quality');

    // AR / 3D Viewer
    Route::get('xem-3d/{product}', [PageController::class, 'arViewer'])->name('ar-viewer');

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

    // GHN address lookup & shipping fee
    Route::get('ghn/provinces', [GHNAddressController::class, 'provinces'])->name('ghn.provinces');
    Route::post('ghn/districts', [GHNAddressController::class, 'districts'])->name('ghn.districts');
    Route::post('ghn/wards', [GHNAddressController::class, 'wards'])->name('ghn.wards');
    Route::post('ghn/shipping-fee', [GHNAddressController::class, 'shippingFee'])->name('ghn.shipping-fee');

    // Address API (JSON for async operations)
    Route::post('addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('addresses.api.set-default');

    // Settings
    Route::post('settings/notifications', [\App\Modules\AgriVerse\Http\Controllers\Shop\PageController::class, 'saveNotificationPreferences'])->name('settings.notifications');
    Route::post('settings/profile', [\App\Modules\AgriVerse\Http\Controllers\Shop\PageController::class, 'updateProfile'])->name('settings.profile');
    Route::post('settings/password', [\App\Modules\AgriVerse\Http\Controllers\Shop\PageController::class, 'changePassword'])->name('settings.password');
});

// Contracts (shop view)
Route::middleware('auth')->prefix('agriverse')->name('agriverse.shop.')->group(function () {
    Route::get('hop-dong/{contract}', [\App\Modules\AgriVerse\Http\Controllers\Shop\ContractController::class, 'show'])->name('contracts.show');
});
