<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ChatService
{
    private function redis()
    {
        return Redis::connection('jakartaee')->client();
    }

    private function buildMessage(array $data): string
    {
        return json_encode([
            'type' => $data['type'] ?? 'system',
            'from' => $data['from'] ?? 'System',
            'to' => $data['to'] ?? '',
            'content' => $data['content'] ?? '',
            'timestamp' => (int) (microtime(true) * 1000),
        ]);
    }

    /**
     * Ghi history vao Redis Stream (maxlen ~50) + publish realtime lên channel.
     *
     * Lưu ý: kết nối 'jakartaee' dùng driver PREDIS, KHÔNG có method xadd() theo cú
     * pháp của phpredis. XADD stream bằng rawCommand để:
     *   XADD chat:messages:stream MAXLEN ~50 * data <base64>
     * Trước đây gọi $this->redis()->xadd(..., 4 tham số) -> PREDIS bắn 'XADD'
     * Sai tham số -> "ERR wrong number of arguments" -> history KHÔNG ghi được,
     * khiến JakartaEE (đọc stream lịch sử khi client connect) không lấy được tin.
     */
    private function streamAdd(string $key, string $payload): void
    {
        $client = $this->redis();
        $args = ['xadd', $key, 'MAXLEN', '~', '50', '*', 'data', $payload];
        $client->executeRaw($args);
    }

    private function store(string $jsonMessage): void
    {
        try {
            $encoded = base64_encode($jsonMessage);
            $this->streamAdd('chat:messages:stream', $encoded);
            Redis::connection('jakartaee')->publish('chat', $jsonMessage);
        } catch (\Throwable $e) {
            Log::warning('Redis publish failed: '.$e->getMessage());
        }
    }

    public function broadcastMessage(string $content, string $type = 'system'): void
    {
        try {
            $this->store($this->buildMessage([
                'type' => $type,
                'content' => $content,
            ]));
        } catch (\Throwable $e) {
            Log::warning('Broadcast message failed: '.$e->getMessage());
        }
    }

    public function sendPrivateMessage(string $fromUserId, string $toUserId, string $content): void
    {
        try {
            $this->store($this->buildMessage([
                'type' => 'private',
                'from' => $fromUserId,
                'to' => $toUserId,
                'content' => $content,
            ]));
        } catch (\Throwable $e) {
            Log::warning('Private message send failed: '.$e->getMessage());
        }
    }

    public function sendGroupMessage(string $fromUserId, string $groupId, string $content): void
    {
        try {
            $json = $this->buildMessage([
                'type' => 'group',
                'from' => $fromUserId,
                'to' => $groupId,
                'content' => $content,
            ]);
            $encoded = base64_encode($json);
            $this->streamAdd('chat:messages:stream', $encoded);
            Redis::connection('jakartaee')->publish("chat:group:{$groupId}", $json);
        } catch (\Throwable $e) {
            Log::warning('Group message send failed: '.$e->getMessage());
        }
    }
}
