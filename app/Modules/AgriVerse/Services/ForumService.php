<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Predis\Client;

class ForumService
{
    private function redis(): Client
    {
        return Redis::connection('jakartaee')->client();
    }

    public function publishComment(int $postId, int $userId, string $userName, string $content): void
    {
        try {
            $payload = json_encode([
                'event' => 'comment',
                'post_id' => $postId,
                'user_id' => $userId,
                'user_name' => $userName,
                'content' => $content,
                'timestamp' => now()->toIso8601String(),
            ]);
            Redis::connection('jakartaee')->publish("forum:post:{$postId}", $payload);
        } catch (\Throwable $e) {
            Log::warning('Forum publish comment failed: '.$e->getMessage());
        }
    }

    public function publishLike(int $postId, int $userId, string $userName, int $likeCount): void
    {
        try {
            $payload = json_encode([
                'event' => 'like',
                'post_id' => $postId,
                'user_id' => $userId,
                'user_name' => $userName,
                'like_count' => $likeCount,
                'timestamp' => now()->toIso8601String(),
            ]);
            Redis::connection('jakartaee')->publish("forum:post:{$postId}", $payload);
        } catch (\Throwable $e) {
            Log::warning('Forum publish like failed: '.$e->getMessage());
        }
    }
}
