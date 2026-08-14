<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = [
            'orders' => [
                ['buyer_id'], ['seller_id'], ['store_id'], ['product_id'],
                ['status'], ['uuid'], ['created_at'],
            ],
            'products' => [
                ['user_id'], ['store_id'], ['category'], ['status'], ['created_at'],
            ],
            'stores' => [
                ['owner_id'], ['status'],
            ],
            'order_statuses' => [
                ['order_id'], ['user_id'],
            ],
            'transactions' => [
                ['order_id'], ['user_id'], ['status'], ['payment_method'],
            ],
            'reviews' => [
                ['product_id'], ['user_id'], ['order_id'],
            ],
            'carts' => [
                ['user_id'], ['product_id'],
            ],
            'wishlists' => [
                ['user_id'], ['product_id'],
            ],
            'forum_posts' => [
                ['user_id'], ['category_id'], ['created_at'],
            ],
            'forum_comments' => [
                ['post_id'], ['user_id'],
            ],
            'contracts' => [
                ['order_id'], ['seller_id'],
            ],
            'subscriptions' => [
                ['user_id'], ['store_id'], ['plan_id'], ['status'],
            ],
            'refunds' => [
                ['order_id'], ['user_id'],
            ],
            'notifications' => [
                ['notifiable_id'], ['type'], ['read_at'],
            ],
            'messages' => [
                ['conversation_id'], ['sender_id'], ['created_at'],
            ],
            'chat_conversations' => [
                ['buyer_id'], ['seller_id'], ['product_id'],
            ],
        ];

        foreach ($indexes as $tableName => $columns) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $blueprint) use ($columns, $tableName) {
                foreach ($columns as $col) {
                    // Skip if any column in the index doesn't exist
                    $allExist = true;
                    foreach ($col as $c) {
                        if (! Schema::hasColumn($tableName, $c)) {
                            $allExist = false;
                            break;
                        }
                    }
                    if (! $allExist) {
                        continue;
                    }
                    $idxName = implode('_', $col).'_index';
                    try {
                        $blueprint->index($col, $idxName);
                    } catch (Exception $e) {
                        // Index may already exist
                    }
                }
            });
        }
    }

    public function down(): void
    {
        // Index removal is not needed for rollback
    }
};
