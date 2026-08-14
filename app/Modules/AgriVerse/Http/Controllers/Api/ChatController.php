<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\ChatConversation;
use App\Modules\AgriVerse\Models\ChatGroup;
use App\Modules\AgriVerse\Models\ChatGroupMember;
use App\Modules\AgriVerse\Models\ChatMessage;
use App\Modules\AgriVerse\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController
{
    public function __construct(
        protected ChatService $chatService,
    ) {}

    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = ChatConversation::where('buyer_id', $userId)
            ->orWhere('seller_id', $userId)
            ->with(['buyer:id,name,avatar', 'seller:id,name,avatar', 'product:id,name,images'])
            ->orderByDesc('last_message_at')
            ->paginate($request->per_page ?? 20);

        return response()->json($conversations);
    }

    public function messages(Request $request, ChatConversation $conversation): JsonResponse
    {
        $userId = $request->user()->id;

        if ($conversation->buyer_id !== $userId && $conversation->seller_id !== $userId) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $messages = ChatMessage::where('conversation_id', $conversation->id)
            ->with('sender:id,name,avatar')
            ->orderBy('created_at')
            ->paginate($request->per_page ?? 50);

        return response()->json($messages);
    }

    public function sendMessage(Request $request, ChatConversation $conversation): JsonResponse
    {
        $userId = $request->user()->id;

        if ($conversation->buyer_id !== $userId && $conversation->seller_id !== $userId) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'message' => $data['message'],
        ]);

        $conversation->update([
            'last_message' => $data['message'],
            'last_message_at' => now(),
            'last_sender_id' => $userId,
        ]);

        $this->chatService->sendPrivateMessage(
            (string) $userId,
            (string) ($conversation->buyer_id === $userId ? $conversation->seller_id : $conversation->buyer_id),
            $data['message']
        );

        return response()->json([
            'message' => $message->load('sender:id,name,avatar'),
        ], 201);
    }

    public function startConversation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'seller_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'message' => 'required|string|max:5000',
        ]);

        $buyerId = $request->user()->id;

        if ($buyerId === (int) $data['seller_id']) {
            return response()->json(['error' => 'Cannot start conversation with yourself'], 422);
        }

        $conversation = ChatConversation::firstOrCreate(
            [
                'buyer_id' => $buyerId,
                'seller_id' => $data['seller_id'],
                'product_id' => $data['product_id'],
            ],
            [
                'last_message' => $data['message'],
                'last_message_at' => now(),
                'last_sender_id' => $buyerId,
            ]
        );

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $buyerId,
            'message' => $data['message'],
        ]);

        $this->chatService->sendPrivateMessage(
            (string) $buyerId,
            (string) $data['seller_id'],
            $data['message']
        );

        return response()->json([
            'conversation' => $conversation->load(['buyer:id,name,avatar', 'seller:id,name,avatar', 'product:id,name']),
            'message' => $message->load('sender:id,name,avatar'),
        ], 201);
    }

    public function groupMessages(Request $request, ChatGroup $group): JsonResponse
    {
        $userId = $request->user()->id;

        $isMember = ChatGroupMember::where('group_id', $group->id)
            ->where('user_id', $userId)
            ->exists();

        if (! $isMember) {
            return response()->json(['error' => 'Not a member of this group'], 403);
        }

        $messages = ChatMessage::where('group_id', $group->id)
            ->with('sender:id,name,avatar')
            ->orderBy('created_at')
            ->paginate($request->per_page ?? 50);

        return response()->json($messages);
    }

    public function sendGroupMessage(Request $request, ChatGroup $group): JsonResponse
    {
        $userId = $request->user()->id;

        $isMember = ChatGroupMember::where('group_id', $group->id)
            ->where('user_id', $userId)
            ->exists();

        if (! $isMember) {
            return response()->json(['error' => 'Not a member of this group'], 403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = ChatMessage::create([
            'group_id' => $group->id,
            'sender_id' => $userId,
            'message' => $data['message'],
        ]);

        $this->chatService->sendGroupMessage(
            (string) $userId,
            (string) $group->id,
            $data['message']
        );

        return response()->json([
            'message' => $message->load('sender:id,name,avatar'),
        ], 201);
    }
}
