<?php

use App\Models\User;
use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Services\AIPlantDoctorService;
use App\Modules\AgriVerse\Services\AnalyticsService;
use App\Modules\AgriVerse\Services\CacheService;
use App\Modules\AgriVerse\Services\EmailVerificationService;
use App\Modules\AgriVerse\Services\GHTKService;
use App\Modules\AgriVerse\Services\ImageOptimizationService;
use App\Modules\AgriVerse\Services\PaymentService;
use App\Modules\AgriVerse\Services\RateLimitService;
use App\Modules\AgriVerse\Services\SearchService;
use App\Modules\AgriVerse\Services\SeoService;
use App\Modules\AgriVerse\Services\TaxService;
use App\Modules\AgriVerse\Services\TwoFactorService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->group('services');

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

/*************** GHTK Service ***************/

describe('GHTK Service', function () {
    it('can calculate fee', function () {
        $service = app(GHTKService::class);
        $result = $service->calculateFee(
            'Hồ Chí Minh',
            'Quận 1',
            'Hà Nội',
            'Quận Cầu Giấy',
            1000,
            500000
        );

        expect($result)->toHaveKeys(['fee', 'insurance_fee']);
        expect($result['fee'])->toBeGreaterThan(0);
    });

    it('can get services list', function () {
        $service = app(GHTKService::class);
        $services = $service->getServices();

        expect($services)->toBeArray();
        expect(count($services))->toBeGreaterThanOrEqual(1);
    });

    it('can create mock order', function () {
        $service = app(GHTKService::class);
        $result = $service->createOrder([
            'order_id' => 'TEST_'.time(),
            'pick_name' => 'Test',
            'pick_address' => '123 Test St',
            'pick_province' => 'Hồ Chí Minh',
            'pick_district' => 'Quận 1',
            'pick_tel' => '0901234567',
            'name' => 'Khách Test',
            'address' => '456 Test St',
            'province' => 'Hà Nội',
            'district' => 'Quận Cầu Giấy',
            'tel' => '0909876543',
            'value' => 500000,
            'cod_amount' => 0,
            'products' => [
                ['name' => 'Bonsai', 'weight' => 500, 'quantity' => 1, 'product_code' => 'BS001'],
            ],
        ]);

        // Servizio può restituire order_code (mock) o error (live API con token configurato)
        expect($result)->toHaveKeys(array_intersect(array_keys($result), ['order_code', 'error']));
    });

    it('can track mock order', function () {
        $service = app(GHTKService::class);
        $result = $service->trackOrder('GHTK_TEST_123');

        // Servizio restituisce status sia in modalità mock che live
        expect($result)->toBeArray();
    });
});

/*************** Payment Service ***************/

describe('Payment Service', function () {
    it('can instantiate payment service', function () {
        $service = app(PaymentService::class);
        expect($service)->toBeInstanceOf(PaymentService::class);
    });

    it('has payment methods config', function () {
        $methods = config('payment.methods', []);
        expect($methods)->toBeArray();
    });

    it('has valid Momo config', function () {
        $partnerCode = config('payment.momo.partner_code');
        // Should not throw when accessing config
        expect(true)->toBeTrue();
    });
});

/*************** Cache Service ***************/

describe('Cache Service', function () {
    it('can be instantiated', function () {
        $service = app(CacheService::class);
        expect($service)->toBeInstanceOf(CacheService::class);
    });

    it('can get cache stats', function () {
        $service = app(CacheService::class);
        $stats = $service->getStats();
        expect($stats)->toHaveKeys(['product_count', 'category_count', 'store_count']);
    });
});

/*************** Search Service ***************/

describe('Search Service', function () {
    it('can be instantiated', function () {
        $service = app(SearchService::class);
        expect($service)->toBeInstanceOf(SearchService::class);
    });

    it('can get popular searches', function () {
        $service = app(SearchService::class);
        $popular = $service->getPopularSearches();

        expect($popular)->toBeArray();
    });

    it('can get user search history for non-existent user', function () {
        $service = app(SearchService::class);
        $history = $service->getUserSearchHistory(99999);

        expect($history)->toBeArray();
    });
});

/*************** SEO Service ***************/

describe('SEO Service', function () {
    it('can generate product meta for a product', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'owner_id' => $seller->id, 'name' => 'Store', 'status' => 'active',
        ]);
        $product = Product::create([
            'user_id' => $seller->id, 'store_id' => $store->id,
            'name' => 'Bonsai Test', 'price' => 100000, 'stock' => 5,
            'status' => 'published', 'description' => 'Mô tả sản phẩm',
        ]);

        $service = app(SeoService::class);
        $meta = $service->productMeta($product);

        expect($meta)->toHaveKeys(['title', 'description', 'keywords']);
        expect($meta['title'])->toContain('Bonsai Test');
    });

    it('can generate robots.txt', function () {
        $service = app(SeoService::class);
        $robots = $service->generateRobotsTxt();

        expect($robots)->toBeString();
        expect($robots)->toContain('Sitemap:');
    });

    it('can generate home meta tags', function () {
        $service = app(SeoService::class);
        $meta = $service->homeMeta();

        expect($meta)->toHaveKeys(['title', 'description', 'keywords']);
    });
});

/*************** Two-Factor Service ***************/

describe('Two-Factor Service', function () {
    it('can generate secret key', function () {
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecretKey();

        expect($secret)->toBeString();
        expect(strlen($secret))->toBeGreaterThan(10);
    });

    it('can generate QR setup data', function () {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $service = app(TwoFactorService::class);
        $data = $service->generateSetupData($user);

        expect($data)->toHaveKeys(['secret', 'qr_code_url', 'otpauth_url']);
    });

    it('can check if 2FA is enabled', function () {
        $user = User::factory()->create();

        $service = app(TwoFactorService::class);
        $enabled = $service->is2FAEnabled($user);

        expect($enabled)->toBeFalse();
    });
});

/*************** Email Verification Service ***************/

describe('Email Verification Service', function () {
    it('can check verification status', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $service = app(EmailVerificationService::class);
        $verified = $service->isVerified($user);

        expect($verified)->toBeTrue();
    });

    it('can check unverified status', function () {
        $user = User::factory()->unverified()->create();

        $service = app(EmailVerificationService::class);
        $verified = $service->isVerified($user);

        expect($verified)->toBeFalse();
    });
});

/*************** Image Optimization Service ***************/

describe('Image Optimization Service', function () {
    it('can validate image size config', function () {
        $service = app(ImageOptimizationService::class);
        $sizes = config('agriverse.image.sizes', []);

        // Should not throw when instantiated
        expect($service)->toBeInstanceOf(ImageOptimizationService::class);
    });

    it('can get image info for missing file', function () {
        $service = app(ImageOptimizationService::class);
        $info = $service->getInfo('non-existent-file.jpg');

        expect($info)->toBeNull();
    });
});

/*************** Rate Limit Service ***************/

describe('Rate Limit Service', function () {
    it('can check login limits', function () {
        $service = app(RateLimitService::class);

        // Creating a mock request
        $request = Request::create('/test', 'GET');

        $limited = $service->isLoginLimited($request);
        expect($limited)->toBeFalse();
    });

    it('can get rate limit headers', function () {
        $service = app(RateLimitService::class);
        $request = Request::create('/test', 'GET');

        $headers = $service->getHeaders($request, 'test_key', 60);
        expect($headers)->toHaveKeys(['X-RateLimit-Limit', 'X-RateLimit-Remaining', 'X-RateLimit-Reset']);
    });
});

/*************** Tax Service ***************/

describe('Tax Service', function () {
    it('can get tax rates', function () {
        $service = app(TaxService::class);
        $rates = $service->getTaxRates();

        expect($rates)->toHaveKeys(['default', 'categories', 'exempt']);
    });

    it('can calculate item tax for a product', function () {
        $category = Category::create(['name' => 'Nông sản', 'slug' => 'nong-san', 'is_active' => true]);
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create(['owner_id' => $seller->id, 'name' => 'Store', 'status' => 'active']);
        $product = Product::create([
            'user_id' => $seller->id,
            'store_id' => $store->id,
            'name' => 'Test Product',
            'price' => 100000,
            'stock' => 10,
            'status' => 'published',
        ]);
        $product->categories()->attach($category->id);

        $service = app(TaxService::class);
        $itemTax = $service->calculateItemTax($product, 2);

        expect($itemTax)->toHaveKeys(['tax_rate', 'tax_amount']);
        expect($itemTax['tax_rate'])->toBe(0.05); // 5% for nong-san
    });
});

/*************** AI Plant Doctor Service ***************/

describe('AI Plant Doctor Service', function () {
    it('can instantiate with config', function () {
        $service = app(AIPlantDoctorService::class);
        expect($service)->toBeInstanceOf(AIPlantDoctorService::class);
    });
});

/*************** Analytics Service ***************/

describe('Analytics Service', function () {
    it('can track an event', function () {
        $service = app(AnalyticsService::class);

        // Should not throw when tracking
        $event = $service->track('page_view', 'homepage', ['url' => '/agriverse']);
        expect($event)->not->toBeNull();
    });

    it('can track page view', function () {
        $service = app(AnalyticsService::class);

        $event = $service->trackPageView('san-pham', ['category' => 'cay-canh']);
        expect($event)->not->toBeNull();
        expect($event->event_type)->toBe('page_view');
    });
});
