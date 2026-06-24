<?php

namespace Tests\Feature\AgriVerse;

use Tests\TestCase;
use App\Models\User;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\Refund;
use App\Modules\AgriVerse\Models\OrderStatus;

class OrderFlowTest extends TestCase
{
    private $admin;
    private $buyer;
    private $seller;
    private $product;
    private $order;
    private bool $hasRefundsTable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasRefundsTable = \Illuminate\Support\Facades\Schema::hasTable('refunds');

        $this->admin = User::where('role', 'admin')->first();
        $this->buyer = User::where('role', 'buyer')->first();
        $this->seller = User::where('role', 'seller')->first();

        if ($this->seller) {
            $this->product = Product::where('user_id', $this->seller->id)
                ->where('stock', '>', 0)
                ->first();
        }

        if ($this->buyer && $this->product) {
            $this->order = Order::where('buyer_id', $this->buyer->id)
                ->where('product_id', $this->product->id)
                ->where('status', 'pending')
                ->first();

            if (!$this->order) {
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
        }
    }

    public function test_buyer_can_cancel_pending_order()
    {
        if (!$this->buyer || !$this->order || $this->order->status !== 'pending') {
            $this->markTestSkipped('No pending order available for buyer');
        }

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
        if (!$this->buyer || !$this->order || $this->order->status !== 'pending') {
            $this->markTestSkipped('No pending order available');
        }

        $originalStock = $this->product->fresh()->stock;

        $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$this->order->id}/cancel", [
                'reason' => 'Stock restore test',
            ]);

        $this->assertEquals($originalStock + $this->order->quantity, $this->product->fresh()->stock);
    }

    public function test_cancel_requires_reason()
    {
        if (!$this->buyer || !$this->order) {
            $this->markTestSkipped('No order available');
        }

        $response = $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$this->order->id}/cancel", []);

        $response->assertSessionHasErrors('reason');
    }

    public function test_buyer_cannot_cancel_others_order()
    {
        $otherBuyer = User::where('role', 'buyer')
            ->where('id', '!=', $this->buyer?->id)
            ->first();

        if (!$otherBuyer || !$this->order) {
            $this->markTestSkipped('No other buyer or order available');
        }

        $response = $this->actingAs($otherBuyer)
            ->post("/agriverse/api/orders/{$this->order->id}/cancel", [
                'reason' => 'Should fail',
            ]);

        $response->assertStatus(403);
    }

    public function test_buyer_can_request_refund_for_delivered_order()
    {
        if (!$this->buyer) {
            $this->markTestSkipped('No buyer found');
        }

        $deliveredOrder = Order::where('buyer_id', $this->buyer->id)
            ->where('status', 'delivered')
            ->first();

        if (!$deliveredOrder) {
            $deliveredOrder = Order::create([
                'product_id' => $this->product?->id ?? 1,
                'buyer_id' => $this->buyer->id,
                'seller_id' => $this->seller?->id ?? 1,
                'store_id' => $this->product?->store_id,
                'quantity' => 1,
                'unit_price' => 100000,
                'total_price' => 100000,
                'total_amount' => 100000,
                'commission_fee' => 5000,
                'status' => 'delivered',
                'shipping_address' => '123 Test St',
                'delivered_at' => now(),
            ]);
        }

        $response = $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$deliveredOrder->id}/refund", [
                'reason' => 'Sản phẩm không đúng mô tả',
                'description' => 'Màu sắc không giống hình',
            ]);

        $response->assertSessionHas('success');

        $refund = Refund::where('order_id', $deliveredOrder->id)->first();
        $this->assertNotNull($refund);
        $this->assertEquals('pending', $refund->status);
        $this->assertEquals('Sản phẩm không đúng mô tả', $refund->reason);
    }

    public function test_duplicate_refund_request_is_blocked()
    {
        if (!$this->buyer) {
            $this->markTestSkipped('No buyer found');
        }

        $completedOrder = Order::where('buyer_id', $this->buyer->id)
            ->where('status', 'completed')
            ->first();

        if (!$completedOrder) {
            $this->markTestSkipped('No completed order found');
        }

        // First request
        $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$completedOrder->id}/refund", [
                'reason' => 'Lần 1',
            ]);

        // Duplicate
        $response = $this->actingAs($this->buyer)
            ->post("/agriverse/api/orders/{$completedOrder->id}/refund", [
                'reason' => 'Lần 2',
            ]);

        $response->assertSessionHas('error');
    }

    public function test_admin_can_approve_refund()
    {
        if (!$this->hasRefundsTable) {
            $this->markTestSkipped('Refunds table does not exist (run migrations)');
        }
        if (!$this->admin) {
            $this->markTestSkipped('No admin found');
        }

        $refund = Refund::where('status', 'pending')->first();
        if (!$refund) {
            $this->markTestSkipped('No pending refund found');
        }

        $response = $this->actingAs($this->admin)
            ->post("/admin/agriverse/refunds/{$refund->id}/approve");

        $response->assertSessionHas('success');
        $refund->refresh();
        $this->assertEquals('approved', $refund->status);
    }

    public function test_admin_can_reject_refund_with_note()
    {
        if (!$this->hasRefundsTable) {
            $this->markTestSkipped('Refunds table does not exist (run migrations)');
        }
        if (!$this->admin) {
            $this->markTestSkipped('No admin found');
        }

        $refund = Refund::where('status', 'pending')->first();
        if (!$refund) {
            $this->markTestSkipped('No pending refund found');
        }

        $response = $this->actingAs($this->admin)
            ->post("/admin/agriverse/refunds/{$refund->id}/reject", [
                'note' => 'Không đủ điều kiện hoàn tiền',
            ]);

        $response->assertSessionHas('success');
        $refund->refresh();
        $this->assertEquals('rejected', $refund->status);
        $this->assertEquals('Không đủ điều kiện hoàn tiền', $refund->admin_note);
    }

    public function test_refund_reject_requires_note()
    {
        if (!$this->hasRefundsTable) {
            $this->markTestSkipped('Refunds table does not exist (run migrations)');
        }
        if (!$this->admin) {
            $this->markTestSkipped('No admin found');
        }

        $refund = Refund::where('status', 'pending')->first();
        if (!$refund) {
            $this->markTestSkipped('No pending refund found');
        }

        $response = $this->actingAs($this->admin)
            ->post("/admin/agriverse/refunds/{$refund->id}/reject", []);

        $response->assertSessionHasErrors('note');
    }

    public function test_admin_refund_index_page()
    {
        if (!$this->hasRefundsTable) {
            $this->markTestSkipped('Refunds table does not exist (run migrations)');
        }
        if (!$this->admin) {
            $this->markTestSkipped('No admin found');
        }

        $response = $this->actingAs($this->admin)->get('/admin/agriverse/refunds');
        $response->assertStatus(200);
    }

    public function test_admin_refund_show_page()
    {
        if (!$this->hasRefundsTable) {
            $this->markTestSkipped('Refunds table does not exist (run migrations)');
        }
        if (!$this->admin) {
            $this->markTestSkipped('No admin found');
        }

        $refund = Refund::first();
        if (!$refund) {
            $this->markTestSkipped('No refunds found');
        }

        $response = $this->actingAs($this->admin)
            ->get("/admin/agriverse/refunds/{$refund->id}");
        $response->assertStatus(200);
    }

    public function test_admin_order_create_page()
    {
        if (!$this->admin) {
            $this->markTestSkipped('No admin found');
        }

        $response = $this->actingAs($this->admin)->get('/admin/agriverse/orders/create');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_order_manually()
    {
        if (!$this->admin || !$this->product || !$this->buyer) {
            $this->markTestSkipped('Missing admin, product, or buyer');
        }

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
        if (!$this->buyer || !$this->order) {
            $this->markTestSkipped('No order available');
        }

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
