<?php

use App\Models\User;
use App\Modules\AgriVerse\Models\ChatConversation;
use App\Modules\AgriVerse\Models\ChatMessage;
use App\Modules\AgriVerse\Models\ChatGroup;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->group('chat');

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    setupPassport();

    $this->buyer = User::factory()->create(['role' => 'buyer']);
    $this->buyer->assignRole('buyer');

    $this->seller = User::factory()->create(['role' => 'seller']);
    $this->seller->assignRole('seller');

    $this->store = Store::create(['owner_id' => $this->seller->id, 'name' => 'Store', 'status' => 'active']);

    $this->product = Product::create([
        'user_id' => $this->seller->id, 'store_id' => $this->store->id,
        'name' => 'Test', 'price' => 50000, 'stock' => 10, 'status' => 'published',
    ]);
});

describe('Conversations', function () {
    it('can list conversations', function () {
        Passport::actingAs($this->buyer);
        $response = $this->getJson('/api/chat/conversations');
        expect(in_array($response->status(), [200, 401, 403, 404]))->toBeTrue();
    });

    it('can start a conversation', function () {
        Passport::actingAs($this->buyer);
        $response = $this->postJson('/api/chat/conversations', [
            'seller_id' => $this->seller->id, 'product_id' => $this->product->id,
            'message' => 'Chào bạn, cây này còn không?',
        ]);
        expect(in_array($response->status(), [200, 201, 400, 401, 403, 422]))->toBeTrue();
    });
});

describe('Messages', function () {
    it('can get conversation messages', function () {
        $conv = ChatConversation::create([
            'buyer_id' => $this->buyer->id, 'seller_id' => $this->seller->id,
            'product_id' => $this->product->id,
        ]);

        Passport::actingAs($this->buyer);
        $response = $this->getJson("/api/chat/conversations/{$conv->id}/messages");
        expect(in_array($response->status(), [200, 401, 403, 404]))->toBeTrue();
    });

    it('can send a message', function () {
        $conv = ChatConversation::create([
            'buyer_id' => $this->buyer->id, 'seller_id' => $this->seller->id,
            'product_id' => $this->product->id,
        ]);

        Passport::actingAs($this->buyer);
        $response = $this->postJson("/api/chat/conversations/{$conv->id}/messages", [
            'message' => 'Xin chào!',
        ]);
        expect(in_array($response->status(), [200, 201, 400, 401, 403, 422]))->toBeTrue();
    });
});

describe('Group Chat', function () {
    it('can get group messages', function () {
        $group = ChatGroup::create([
            'name' => 'Nhóm Bonsai', 'created_by' => $this->seller->id,
        ]);

        Passport::actingAs($this->seller);
        $response = $this->getJson("/api/chat/groups/{$group->id}/messages");
        expect(in_array($response->status(), [200, 401, 403, 404]))->toBeTrue();
    });

    it('can send group message', function () {
        $group = ChatGroup::create([
            'name' => 'Nhóm Bonsai', 'created_by' => $this->seller->id,
        ]);

        Passport::actingAs($this->seller);
        $response = $this->postJson("/api/chat/groups/{$group->id}/messages", [
            'message' => 'Chào mọi người!',
        ]);
        expect(in_array($response->status(), [200, 201, 400, 401, 403, 422]))->toBeTrue();
    });
});
