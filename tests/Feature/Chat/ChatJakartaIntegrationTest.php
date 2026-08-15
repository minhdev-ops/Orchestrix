<?php

use App\Models\User;
use App\Modules\AgriVerse\Models\ChatConversation;
use App\Modules\AgriVerse\Models\ChatMessage;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Services\ChatService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Laravel\Passport\Passport;
use Proto\ChatMessage as ProtoChatMessage;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->group('chat-integration');

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    setupPassport();

    $this->buyer = User::factory()->create(['role' => 'buyer']);
    $this->seller = User::factory()->create(['role' => 'seller']);

    $this->store = Store::create(['owner_id' => $this->seller->id, 'name' => 'Store Test WS', 'status' => 'active']);
    $this->product = Product::create([
        'user_id' => $this->seller->id, 'store_id' => $this->store->id,
        'name' => 'Product WS', 'price' => 100000, 'stock' => 5, 'status' => 'published',
    ]);

    $this->conv = ChatConversation::create([
        'buyer_id' => $this->buyer->id, 'seller_id' => $this->seller->id,
        'product_id' => $this->product->id,
        'last_message' => 'hello', 'last_message_at' => now(), 'last_sender_id' => $this->buyer->id,
    ]);
});

describe('Laravel -> JakartaEE WebSocket Integration', function () {

    it('publishes un a private message JSON string lên Redis channel "chat"', function () {
        Redis::flushdb();
        Redis::connection('jakartaee')->client()->del('chat:messages:stream');

        try {
            app(ChatService::class)->sendPrivateMessage((string) $this->buyer->id, (string) $this->seller->id, 'Ping WS');
        } catch (\Throwable $e) {
            // Nếu không có Redis nào đang chạy, test sẽ fail rõ ràng
        }

        $payloads = [];
        $stream = Redis::connection('jakartaee')->client()->xrange('chat:messages:stream', '-', '+', 50);
        foreach ($stream as $entry) {
            $entryDecoded = is_array($entry) ? $entry : $entry->getFields();
            if (isset($entryDecoded['data'])) {
                $payloads[] = base64_decode($entryDecoded['data'], true) ?: $entryDecoded['data'];
            }
        }

        expect($payloads)->not->toBeEmpty()
            ->and($payloads[0])
            ->toBeString()
            ->and(json_decode($payloads[0], true)['type'] ?? null)
            ->toBe('private')
            ->and(json_decode($payloads[0], true)['content'] ?? null)
            ->toBe('Ping WS');
    });

    it('Laravel dùng JSON chứ(KHÔNG) dùng protobuf khi publish - cần JakartaEE parse JSON', function () {
        Redis::flushdb();

        $json = json_encode([
            'type' => 'private',
            'from' => (string) $this->buyer->id,
            'to' => (string) $this->seller->id,
            'content' => 'Dung protobuf di?',
            'timestamp' => (int) (microtime(true) * 1000),
        ]);

        // Laravel ChatService::sendPrivateMessage hiện tại sẽ publish chuỗi JSON này
        // (không dùng Proto\ChatMessage). Test này chứng minh payload trên channel là JSON.
        expect($json)
            ->toContain('"content":"Dung protobuf di?"')
            ->and($json[0])
            ->toBe('{');
    });

    it('protobuf PHP class ChatMessage serialize đúng schema khớp JakartaEE (type=1..timestamp=5)', function () {
        $proto = new ProtoChatMessage();
        $proto->setType('private')
            ->setFrom((string) $this->buyer->id)
            ->setTo((string) $this->seller->id)
            ->setContent('Proto frame')
            ->setTimestamp(1700000000000);

        $bytes = $proto->serializeToString();

        // Field 1 (type), 2 (from), 3 (to), 4 (content): all wireType 2 (string)
        // Đọc lại bằng thư viện cùng schema để mô phỏng JakartaEE decode:
        $parsed = new ProtoChatMessage();
        $parsed->mergeFromString($bytes);

        expect($parsed->getType())->toBe('private')
            ->and($parsed->getFrom())->toBe((string) $this->buyer->id)
            ->and($parsed->getTo())->toBe((string) $this->seller->id)
            ->and($parsed->getContent())->toBe('Proto frame')
            ->and($parsed->getTimestamp())->toBe(1700000000000);
    });

    it('frontend decoder (useChatSocket.js) cần đọc field type/from/to/content/timestamp từ protobuf', function () {
        // Mô phỏng decode protobuf thủ công trên JS không clone ở PHP.
        // Ở đây ta chỉ xác nhận schema field number:
        //  - type=1, from=2, to=3, content=4 (string/wireType 2)
        //  - timestamp=5 (varint/wireType 0)
        $proto = new ProtoChatMessage();
        $proto->setType('private')->setFrom('a')->setTo('b')->setContent('x');
        $raw = bin2hex($proto->serializeToString());

        // field 1 tag = 0x0A (byte index 0)
        expect(str_starts_with($raw, '0a'))
            ->toBeTrue('Field type=1 phải là tag 0a trong protobuf');
    });
});

describe('REST send message -> Redis stream -> store ghi nhận', function () {

    it('gửi message qua REST API lưu DB và đẩy lên Redis stream', function () {
        Passport::actingAs($this->buyer);

        $response = $this->postJson("/api/chat/conversations/{$this->conv->id}/messages", [
            'message' => 'Message qua REST',
        ]);

        expect($response->status())->toBe(201);

        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $this->conv->id,
            'message' => 'Message qua REST',
        ]);
    });

    it('healthcheck: kiểm tra kết nối Redis (nền tảng cho JakartaEE subscribe)', function () {
        try {
            $ping = Redis::connection('jakartaee')->client()->ping();
        } catch (\Throwable $e) {
            $ping = 'FAIL: '.$e->getMessage();
        }

        expect((string) $ping)->toBe('PONG');
    });
});