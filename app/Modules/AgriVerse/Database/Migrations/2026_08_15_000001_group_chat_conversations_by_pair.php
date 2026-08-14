<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Merge duplicate conversations for the same (buyer_id, seller_id) pair.
        $pairs = DB::table('chat_conversations')
            ->select('buyer_id', 'seller_id')
            ->groupBy('buyer_id', 'seller_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($pairs as $pair) {
            $convs = DB::table('chat_conversations')
                ->where('buyer_id', $pair->buyer_id)
                ->where('seller_id', $pair->seller_id)
                ->orderBy('last_message_at')
                ->get();

            $keeper = $convs->last();
            $toDelete = $convs->reject(fn ($c) => $c->id === $keeper->id);

            foreach ($toDelete as $old) {
                DB::table('chat_messages')
                    ->where('conversation_id', $old->id)
                    ->update(['conversation_id' => $keeper->id]);

                DB::table('chat_conversations')
                    ->where('id', $old->id)
                    ->delete();
            }

            $latest = DB::table('chat_messages')
                ->where('conversation_id', $keeper->id)
                ->orderByDesc('created_at')
                ->first();

            if ($latest) {
                DB::table('chat_conversations')
                    ->where('id', $keeper->id)
                    ->update([
                        'last_message' => $latest->message,
                        'last_message_at' => $latest->created_at,
                        'last_sender_id' => $latest->sender_id,
                    ]);
            }
        }

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropUnique('chat_conversations_buyer_id_seller_id_product_id_unique');
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->unique(['buyer_id', 'seller_id']);
        });
    }

    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropUnique('chat_conversations_buyer_id_seller_id_unique');
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->unique(['buyer_id', 'seller_id', 'product_id']);
        });
    }
};
