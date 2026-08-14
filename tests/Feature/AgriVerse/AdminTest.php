<?php

namespace Tests\Feature\AgriVerse;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');
    }

    public function test_admin_dashboard_requires_auth()
    {
        $response = $this->get('/admin/agriverse');
        $response->assertRedirect('/login');
    }

    public function test_admin_dashboard_accessible_by_admin()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse');
        $response->assertStatus(200);
    }

    public function test_admin_products_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/products');
        $response->assertStatus(200);
    }

    public function test_admin_orders_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/orders');
        $response->assertStatus(200);
    }

    public function test_admin_stores_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/stores');
        $response->assertStatus(200);
    }

    public function test_admin_categories_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/categories');
        $response->assertStatus(200);
    }

    public function test_admin_plans_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/plans');
        $response->assertStatus(200);
    }

    public function test_admin_contracts_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/contracts');
        $response->assertStatus(200);
    }

    public function test_admin_coupons_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/coupons');
        $response->assertStatus(200);
    }

    public function test_admin_scans_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/scans');
        $response->assertStatus(200);
    }

    public function test_admin_transactions_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/transactions');
        $response->assertStatus(200);
    }

    public function test_admin_reports_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/reports');
        $response->assertStatus(200);
    }

    public function test_admin_product_create_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/products/create');
        $response->assertStatus(200);
    }

    public function test_admin_product_create()
    {
        $response = $this
            ->withoutMiddleware(ValidateCsrfToken::class)
            ->actingAs($this->admin)
            ->post('/admin/agriverse/products', [
                'name' => 'Test Product '.uniqid(),
                'price' => 50000,
                'status' => 'draft',
                'stock' => 10,
            ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/agriverse/products');
    }
}
