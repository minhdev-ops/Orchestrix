<?php

namespace Tests\Feature\AgriVerse;

use Tests\TestCase;
use App\Models\User;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Category;

class AdminTest extends TestCase
{
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_dashboard_requires_auth()
    {
        $response = $this->get('/admin/agriverse');
        $response->assertRedirect('/login');
    }

    public function test_admin_dashboard_accessible_by_admin()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse');
        $response->assertStatus(200);
    }

    public function test_admin_products_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/products');
        $response->assertStatus(200);
    }

    public function test_admin_orders_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/orders');
        $response->assertStatus(200);
    }

    public function test_admin_stores_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/stores');
        $response->assertStatus(200);
    }

    public function test_admin_categories_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/categories');
        $response->assertStatus(200);
    }

    public function test_admin_plans_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/plans');
        $response->assertStatus(200);
    }

    public function test_admin_contracts_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/contracts');
        $response->assertStatus(200);
    }

    public function test_admin_coupons_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/coupons');
        $response->assertStatus(200);
    }

    public function test_admin_scans_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/scans');
        $response->assertStatus(200);
    }

    public function test_admin_transactions_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/transactions');
        $response->assertStatus(200);
    }

    public function test_admin_reports_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/reports');
        $response->assertStatus(200);
    }

    public function test_admin_product_create_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/products/create');
        $response->assertStatus(200);
    }

    public function test_admin_product_create()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found');
        }
        $response = $this->actingAs($this->admin)->post('/admin/agriverse/products', [
            'name' => 'Test Product ' . uniqid(),
            'price' => 50000,
            'status' => 'draft',
            'stock' => 10,
        ]);
        $response->assertRedirect('/admin/agriverse/products');
    }
}
