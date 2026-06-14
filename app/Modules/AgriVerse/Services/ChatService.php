<?php

namespace App\Modules\AgriVerse\Services;

use Proto\ChatMessage;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class ChatService {
    private function redis(): \Predis\Client {
        return Redis::connection( 'jakartaee' )->client();
    }

    private function store( ChatMessage $message ): void {
        try {
            $raw = $message->serializeToString();
            $this->redis()->rpush( 'chat:messages', [ $raw ] );
            $this->redis()->ltrim( 'chat:messages', -50, -1 );
            Redis::connection( 'jakartaee' )->publish( 'chat', $raw );
        } catch ( \Throwable $e ) {
            Log::warning( 'Redis private message/publish failed: ' . $e->getMessage() );
        }
    }

    public function broadcastMessage( string $content, string $type = 'system' ): void {
        try {
            $message = new ChatMessage();
            $message->setType( $type );
            $message->setFrom( 'System' );
            $message->setContent( $content );
            $message->setTimestamp( (int) ( microtime( true ) * 1000 ) );
            $this->store( $message );
        } catch ( \Throwable $e ) {
            Log::warning( 'Broadcast message failed: ' . $e->getMessage() );
        }
    }

    public function sendPrivateMessage( string $fromUserId, string $toUserId, string $content ): void {
        try {
            $message = new ChatMessage();
            $message->setType( 'private' );
            $message->setFrom( $fromUserId );
            $message->setTo( $toUserId );
            $message->setContent( $content );
            $message->setTimestamp( (int) ( microtime( true ) * 1000 ) );
            $this->store( $message );
        } catch ( \Throwable $e ) {
            Log::warning( 'Private message send failed: ' . $e->getMessage() );
        }
    }

    public function sendGroupMessage( string $fromUserId, string $groupId, string $content ): void {
        try {
            $message = new ChatMessage();
            $message->setType( 'group' );
            $message->setFrom( $fromUserId );
            $message->setTo( $groupId );
            $message->setContent( $content );
            $message->setTimestamp( (int) ( microtime( true ) * 1000 ) );
            $raw = $message->serializeToString();
            $this->redis()->rpush( 'chat:messages', [ $raw ] );
            $this->redis()->ltrim( 'chat:messages', -50, -1 );
            Redis::connection( 'jakartaee' )->publish( "chat:group:{$groupId}", $raw );
        } catch ( \Throwable $e ) {
            Log::warning( 'Group message send failed (Redis/Protobuf): ' . $e->getMessage() );
        }
    }
}
