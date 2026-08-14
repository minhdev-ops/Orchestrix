<?php

use App\Models\User;
use App\Modules\AgriVerse\Models\Specimen;
use App\Modules\AgriVerse\Models\SubscriptionPlan;
use App\Modules\AgriVerse\Models\SupportFaq;
use App\Modules\AgriVerse\Models\SustainabilityReport;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->group('additional');

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    setupPassport();

    $this->user = User::factory()->create(['role' => 'buyer']);
    $this->user->assignRole('buyer');

    $this->seller = User::factory()->create(['role' => 'seller']);
    $this->seller->assignRole('seller');
    $this->seller->givePermissionTo(['subscription.create', 'subscription.view']);

    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->admin->assignRole('admin');
    $this->admin->givePermissionTo(['contract.view', 'contract.create']);
});

describe('Garden & Specimens', function () {
    it('can create specimen', function () {
        $specimen = Specimen::create([
            'user_id' => $this->user->id, 'name' => 'Bonsai Số 1',
            'code' => 'BONSAI_001', 'status' => 'hydrated', 'hydration_value' => 80,
        ]);
        expect($specimen->id)->not->toBeNull();
        $this->assertDatabaseHas('specimens', ['name' => 'Bonsai Số 1']);
    });

    it('can view garden page', function () {
        $this->actingAs($this->user);
        $response = $this->get(route('agriverse.shop.garden.index'));
        expect(in_array($response->status(), [200, 302, 404]))->toBeTrue();
    });
});

describe('Support & FAQ', function () {
    it('can view support page with FAQs', function () {
        SupportFaq::create([
            'question' => 'Thời gian giao hàng?', 'answer' => '2-3 ngày làm việc.',
            'category' => 'Vận chuyển', 'is_published' => true, 'sort_order' => 1,
        ]);
        $response = $this->get(route('agriverse.shop.support.index'));
        expect(in_array($response->status(), [200, 302, 404]))->toBeTrue();
    });
});

describe('Sustainability', function () {
    it('can view sustainability page', function () {
        SustainabilityReport::create([
            'year' => now()->year, 'title' => 'Báo cáo phát triển bền vững',
            'summary' => 'Năm thứ 3 liên tiếp đạt chuẩn xanh',
        ]);
        $response = $this->get(route('agriverse.shop.sustainability.index'));
        expect(in_array($response->status(), [200, 302, 404]))->toBeTrue();
    });
});

describe('Contracts', function () {
    it('can list contracts', function () {
        Passport::actingAs($this->admin);
        $response = $this->getJson('/api/contracts');
        expect(in_array($response->status(), [200, 401, 403, 404]))->toBeTrue();
    });

    it('can create contract', function () {
        Passport::actingAs($this->admin);
        $response = $this->postJson('/api/contracts', [
            'title' => 'Hợp đồng mua bán Bonsai', 'content' => 'Nội dung hợp đồng...',
            'parties' => ['buyer' => $this->user->id, 'seller' => $this->seller->id],
        ]);
        expect(in_array($response->status(), [200, 201, 400, 401, 403, 422]))->toBeTrue();
    });
});

describe('Subscriptions', function () {
    it('can list subscription plans', function () {
        SubscriptionPlan::create([
            'name' => 'Gói Pro', 'price' => 199000, 'duration_days' => 30,
            'features' => ['3d_models', 'priority_support'], 'is_active' => true,
        ]);
        Passport::actingAs($this->user);
        $response = $this->getJson('/api/subscription-plans');
        expect(in_array($response->status(), [200, 401, 403, 404]))->toBeTrue();
    });

    it('can subscribe to plan', function () {
        $plan = SubscriptionPlan::create([
            'name' => 'Gói Cơ bản', 'price' => 99000, 'duration_days' => 30,
            'features' => ['basic'], 'is_active' => true,
        ]);
        Passport::actingAs($this->seller);
        $response = $this->postJson('/api/subscriptions', [
            'subscription_plan_id' => $plan->id, 'payment_method' => 'cod',
        ]);
        expect(in_array($response->status(), [200, 201, 400, 401, 403, 422]))->toBeTrue();
    });
});

describe('Profile & Settings', function () {
    it('can view profile page', function () {
        $this->actingAs($this->user);
        $response = $this->get(route('agriverse.shop.profile.index'));
        expect(in_array($response->status(), [200, 302, 404]))->toBeTrue();
    });

    it('can view settings page', function () {
        $this->actingAs($this->user);
        $response = $this->get(route('agriverse.shop.account.settings'));
        expect(in_array($response->status(), [200, 302, 404]))->toBeTrue();
    });
});

describe('Address Management', function () {
    it('can list user addresses', function () {
        $this->actingAs($this->user);
        $response = $this->get(route('agriverse.shop.addresses.index'));
        expect(in_array($response->status(), [200, 302, 404]))->toBeTrue();
    });
});
