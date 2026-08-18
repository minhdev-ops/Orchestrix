<?php

namespace Tests\Feature\AgriVerse;

use App\Models\User;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Refund;
use App\Modules\AgriVerse\Models\Store;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    private $buyer;

    private $seller;

    private $product;

    private $order;

    private bool $hasRefundsTable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $this->hasRefundsTable = Schema::hasTable('refunds');

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');

        $this->buyer = User::factory()->create(['role' => 'buyer']);
        $this->buyer->assignRole('buyer');

        $this->seller = User::factory()->create(['role' => 'seller']);
        $this->seller->assignRole('seller');

        $store = Store::create([
            'owner_id' => $this->seller->id,
            'name' => 'Test Store',
            'status' => 'active',
        ]);

        $this->product = Product::factory()->create([
            'user_id' => $this->seller->id,
            'store_id' => $store->id,
            'stock' => 10,
        ]);

        $this->order = Order::create([
            'product_id' => $this->product->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'store_id' => $this->product->store_id,
            'quantity' => 1,
            'unit_price' => $this->product->price,
            'total_price' => $this->product->price,
            'total_amount' => $this->product->price,
            'commission_fee' => $this->product->price * 0.05,
            'status' => 'pending',
            'shipping_address' => '123 Test St',
        ]);

        OrderStatus::create([
            'order_id' => $this->order->id,
            'status' => 'pending',
            'note' => 'Test order created',
            'user_id' => $this->buyer->id,
        ]);
    }

    public function test_buyer_can_cancel_pending_order()
    {
        $response = $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$this->order->id}/cancel", [
                'reason' => 'Test cancellation',
            ]);

        $response->assertSessionHas('success');
        $this->order->refresh();
        $this->assertEquals('cancelled', $this->order->status);
        $this->assertEquals('Test cancellation', $this->order->cancel_reason);
    }

    public function test_cancel_restores_stock()
    {
        $originalStock = $this->product->fresh()->stock;

        $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$this->order->id}/cancel", [
                'reason' => 'Stock restore test',
            ]);

        $this->assertEquals($originalStock + $this->order->quantity, $this->product->fresh()->stock);
    }

    public function test_cancel_requires_reason()
    {
        $response = $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$this->order->id}/cancel", []);

        $response->assertSessionHasErrors('reason');
    }

    public function test_buyer_cannot_cancel_others_order()
    {
        $otherBuyer = User::factory()->create(['role' => 'buyer']);
        $otherBuyer->assignRole('buyer');

        $response = $this->actingAs($otherBuyer)
            ->post("/agriverse/api/orders/{$this->order->id}/cancel", [
                'reason' => 'Should fail',
            ]);

        $response->assertStatus(403);
    }

    public function test_buyer_can_request_refund_for_delivered_order()
    {
        $deliveredOrder = Order::create([
            'product_id' => $this->product->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'store_id' => $this->product->store_id,
            'quantity' => 1,
            'unit_price' => 100000,
            'total_price' => 100000,
            'total_amount' => 100000,
            'commission_fee' => 5000,
            'status' => 'delivered',
            'shipping_address' => '123 Test St',
            'delivered_at' => now(),
        ]);

        $response = $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$deliveredOrder->id}/refund", [
                'reason' => 'San pham khong dung mo ta',
                'description' => 'Mau sac khong giong hinh',
            ]);

        $response->assertSessionHas('success');

        $refund = Refund::where('order_id', $deliveredOrder->id)->first();
        $this->assertNotNull($refund);
        $this->assertEquals('pending', $refund->status);
        $this->assertEquals('San pham khong dung mo ta', $refund->reason);
    }

    public function test_duplicate_refund_request_is_blocked()
    {
        $completedOrder = Order::create([
            'product_id' => $this->product->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'store_id' => $this->product->store_id,
            'quantity' => 1,
            'unit_price' => 100000,
            'total_price' => 100000,
            'total_amount' => 100000,
            'commission_fee' => 5000,
            'status' => 'completed',
            'shipping_address' => '123 Test St',
        ]);

        // First request
        $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$completedOrder->id}/refund", [
                'reason' => 'Lan 1',
            ]);

        // Duplicate
        $response = $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$completedOrder->id}/refund", [
                'reason' => 'Lan 2',
            ]);

        $response->assertSessionHas('error');
    }

    public function test_admin_can_approve_refund()
    {
        if (! $this->hasRefundsTable) {
            $this->markTestSkipped('Refunds table does not exist');
        }

        $refund = Refund::create([
            'order_id' => $this->order->id,
            'user_id' => $this->buyer->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'amount' => 50000,
            'reason' => 'Test refund',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post("/admin/agriverse/refunds/{$refund->id}/approve");

        $response->assertSessionHas('success');
        $refund->refresh();
        $this->assertEquals('approved', $refund->status);
    }

    public function test_admin_can_reject_refund_with_note()
    {
        if (! $this->hasRefundsTable) {
            $this->markTestSkipped('Refunds table does not exist');
        }

        $refund = Refund::create([
            'order_id' => $this->order->id,
            'user_id' => $this->buyer->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'amount' => 50000,
            'reason' => 'Test refund',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post("/admin/agriverse/refunds/{$refund->id}/reject", [
                'note' => 'Khong du dieu kien hoan tien',
            ]);

        $response->assertSessionHas('success');
        $refund->refresh();
        $this->assertEquals('rejected', $refund->status);
        $this->assertEquals('Khong du dieu kien hoan tien', $refund->admin_note);
    }

    public function test_refund_reject_requires_note()
    {
        if (! $this->hasRefundsTable) {
            $this->markTestSkipped('Refunds table does not exist');
        }

        $refund = Refund::create([
            'order_id' => $this->order->id,
            'user_id' => $this->buyer->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'amount' => 50000,
            'reason' => 'Test refund',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post("/admin/agriverse/refunds/{$refund->id}/reject", []);

        $response->assertSessionHasErrors('note');
    }

    public function test_admin_refund_index_page()
    {
        if (! $this->hasRefundsTable) {
            $this->markTestSkipped('Refunds table does not exist');
        }

        $response = $this->actingAs($this->admin)->get('/admin/agriverse/refunds');
        $response->assertStatus(200);
    }

    public function test_admin_refund_show_page()
    {
        if (! $this->hasRefundsTable) {
            $this->markTestSkipped('Refunds table does not exist');
        }

        $refund = Refund::create([
            'order_id' => $this->order->id,
            'user_id' => $this->buyer->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'amount' => 50000,
            'reason' => 'Test refund',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/admin/agriverse/refunds/{$refund->id}");
        $response->assertStatus(200);
    }

    public function test_admin_order_create_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/agriverse/orders/create');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_order_manually()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/agriverse/orders', [
                'product_id' => $this->product->id,
                'buyer_id' => $this->buyer->id,
                'quantity' => 1,
                'shipping_address' => '456 Admin Ave',
                'notes' => 'Manual order test',
            ]);

        $response->assertSessionHas('success');
    }

    public function test_order_status_history_is_created()
    {
        $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$this->order->id}/cancel", [
                'reason' => 'Check status history',
            ]);

        $statusLog = OrderStatus::where('order_id', $this->order->id)
            ->where('status', 'cancelled')
            ->first();

        $this->assertNotNull($statusLog);
        $this->assertStringContainsString('Check status history', $statusLog->note);
    }
}
