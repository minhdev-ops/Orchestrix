<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Models\User;
use App\Modules\AgriVerse\Models\ChatConversation;
use App\Modules\AgriVerse\Models\ChatGroup;
use App\Modules\AgriVerse\Models\ChatGroupMember;
use App\Modules\AgriVerse\Models\ChatMessage;
use App\Modules\AgriVerse\Models\Message;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Services\ChatService;
use Illuminate\Http\Request;

class ChatController
{
    public function __construct(
        protected ChatService $chatService,
    ) {}

    public function index(Request $request, Order $order)
    {
        if ($order->buyer_id !== auth()->id() && $order->seller_id !== auth()->id()) {
            abort(403);
        }

        $perPage = 20;
        $page = max(1, (int) $request->page);
        $query = Message::with('sender')->where('order_id', $order->id);
        $total = $query->count();
        $messages = $query->latest()
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'messages' => $messages,
            'has_more' => $page * $perPage < $total,
        ]);
    }

    public function store(Request $request, Order $order)
    {
        if ($order->buyer_id !== auth()->id() && $order->seller_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'order_id' => $order->id,
            'sender_id' => auth()->id(),
            'message' => $data['message'],
        ]);

        $message->load('sender');

        $otherUserId = $order->buyer_id === auth()->id() ? $order->seller_id : $order->buyer_id;
        $this->chatService->sendPrivateMessage((string) auth()->id(), (string) $otherUserId, $data['message']);

        return response()->json(['message' => $message], 201);
    }

    public function conversations()
    {
        $userId = auth()->id();

        $conversations = ChatConversation::with(['product:id,name,image', 'buyer:id,name', 'seller:id,name', 'lastSender:id,name'])
            ->where(function ($q) use ($userId) {
                $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
            })
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'buyer_id' => $c->buyer_id,
                'seller_id' => $c->seller_id,
                'product' => $c->product ? ['id' => $c->product->id, 'name' => $c->product->name, 'image' => $c->product->image] : null,
                'other_user' => $c->buyer_id === $userId
                    ? ['id' => $c->seller->id, 'name' => $c->seller->name]
                    : ['id' => $c->buyer->id, 'name' => $c->buyer->name],
                'last_message' => $c->last_message,
                'last_message_at' => $c->last_message_at?->diffForHumans(),
                'unread' => $c->buyer_id === $userId ? $c->buyer_unread : $c->seller_unread,
            ]);

        return response()->json(['conversations' => $conversations]);
    }

    public function messages(Request $request, ChatConversation $conversation)
    {
        $userId = auth()->id();
        if ($conversation->buyer_id !== $userId && $conversation->seller_id !== $userId) {
            abort(403);
        }

        // Reset unread khi user mở conversation
        if ($conversation->buyer_id === $userId) {
            $conversation->updateQuietly(['buyer_unread' => 0]);
        } else {
            $conversation->updateQuietly(['seller_unread' => 0]);
        }

        $perPage = 20;
        $page = max(1, (int) $request->page);

        $query = ChatMessage::with('sender')
            ->where('conversation_id', $conversation->id);

        $total = $query->count();
        $messages = $query->latest()
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($m) => [
                'id' => $m->id,
                'sender_id' => $m->sender_id,
                'message' => $m->message,
                'sender_name' => $m->sender->name ?? 'Người dùng',
                'is_mine' => $m->sender_id === $userId,
                'created_at' => $m->created_at->toIso8601String(),
            ]);

        return response()->json([
            'messages' => $messages,
            'has_more' => $page * $perPage < $total,
        ]);
    }

    public function send(Request $request, ChatConversation $conversation)
    {
        if ($conversation->buyer_id !== auth()->id() && $conversation->seller_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'message' => $data['message'],
        ]);

        $isBuyer = auth()->id() === $conversation->buyer_id;
        $conversation->update([
            'last_message' => $data['message'],
            'last_message_at' => now(),
            'last_sender_id' => auth()->id(),
            'buyer_unread' => $isBuyer ? $conversation->buyer_unread : $conversation->buyer_unread + 1,
            'seller_unread' => $isBuyer ? $conversation->seller_unread + 1 : $conversation->seller_unread,
        ]);

        $message->load('sender');

        $otherUserId = $isBuyer ? $conversation->seller_id : $conversation->buyer_id;
        $this->chatService->sendPrivateMessage((string) auth()->id(), (string) $otherUserId, $data['message']);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'message' => $message->message,
                'sender_name' => $message->sender->name ?? 'Người dùng',
                'is_mine' => true,
                'created_at' => $message->created_at->toIso8601String(),
            ],
        ], 201);
    }

    public function start(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $sellerId = $product->user_id;
        $buyerId = auth()->id();

        if ($buyerId === $sellerId) {
            return response()->json(['error' => 'Bạn không thể tự nhắn tin với chính mình'], 422);
        }

        $conversation = ChatConversation::firstOrCreate(
            ['buyer_id' => $buyerId, 'seller_id' => $sellerId],
            ['buyer_id' => $buyerId, 'seller_id' => $sellerId, 'product_id' => $product->id]
        );

        if ((int) $conversation->product_id !== (int) $product->id) {
            $conversation->updateQuietly(['product_id' => $product->id]);
        }

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'product' => ['id' => $product->id, 'name' => $product->name, 'image' => $product->image],
            ],
        ]);
    }

    public function pickableProducts()
    {
        $userId = auth()->id();

        $contactedSellerIds = ChatConversation::where('buyer_id', $userId)
            ->pluck('seller_id')
            ->merge(ChatConversation::where('seller_id', $userId)->pluck('buyer_id'))
            ->unique()
            ->values();

        $products = Product::published()
            ->where('user_id', '!=', $userId)
            ->whereNotIn('user_id', $contactedSellerIds)
            ->limit(20)
            ->get(['id', 'name', 'image', 'price', 'user_id'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'image' => $p->image,
                'price' => $p->price,
                'seller_name' => $p->user->name ?? 'Người bán',
            ]);

        return response()->json(['products' => $products]);
    }

    // ===== Group Chat =====

    public function groups()
    {
        $userId = auth()->id();
        $memberGroups = ChatGroupMember::where('user_id', $userId)
            ->where('status', 'approved')
            ->pluck('group_id');
        $pendingGroupIds = ChatGroupMember::where('user_id', $userId)
            ->where('status', 'pending')
            ->pluck('group_id');
        $groups = ChatGroup::with(['creator:id,name'])
            ->withCount(['approvedMembers'])
            ->whereIn('id', $memberGroups)
            ->orWhere('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($g) => [
                'id' => $g->id,
                'name' => $g->name,
                'avatar' => $g->avatar,
                'member_count' => $g->approved_members_count,
                'created_by' => $g->creator->name ?? 'Người dùng',
                'is_creator' => $g->created_by === $userId,
                'is_pending' => $pendingGroupIds->contains($g->id),
            ]);

        return response()->json(['groups' => $groups]);
    }

    public function createGroup(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $group = ChatGroup::create([
            'name' => $data['name'],
            'created_by' => auth()->id(),
        ]);

        ChatGroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'status' => 'approved',
            'joined_at' => now(),
        ]);

        return response()->json(['group' => [
            'id' => $group->id,
            'name' => $group->name,
            'member_count' => 1,
        ]], 201);
    }

    public function joinGroup(ChatGroup $group)
    {
        $userId = auth()->id();
        $exists = ChatGroupMember::where('group_id', $group->id)->where('user_id', $userId)->exists();
        if ($exists) {
            return response()->json(['error' => 'Bạn đã gửi yêu cầu hoặc đã là thành viên'], 422);
        }

        ChatGroupMember::create([
            'group_id' => $group->id,
            'user_id' => $userId,
            'status' => 'pending',
            'joined_at' => now(),
        ]);

        return response()->json(['message' => 'Đã gửi yêu cầu tham gia nhóm, chờ admin duyệt']);
    }

    public function addMember(Request $request, ChatGroup $group)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $exists = ChatGroupMember::where('group_id', $group->id)
            ->where('user_id', $data['user_id'])
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Người dùng đã là thành viên'], 422);
        }

        ChatGroupMember::create([
            'group_id' => $group->id,
            'user_id' => $data['user_id'],
            'status' => 'approved',
            'joined_at' => now(),
        ]);

        return response()->json(['message' => 'Đã thêm thành viên']);
    }

    public function groupMembers(ChatGroup $group)
    {
        if (! ChatGroupMember::where('group_id', $group->id)->where('user_id', auth()->id())->where('status', 'approved')->exists()) {
            abort(403);
        }

        $members = $group->approvedMembers()->get()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
        ]);

        return response()->json(['members' => $members]);
    }

    public function pendingMembers(ChatGroup $group)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }

        $members = $group->pendingMembers()->get()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
        ]);

        return response()->json(['pending_members' => $members]);
    }

    public function approveMember(ChatGroup $group, User $user)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }

        $member = ChatGroupMember::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $member->update(['status' => 'approved']);

        return response()->json(['message' => 'Đã duyệt người dùng vào nhóm']);
    }

    public function rejectMember(ChatGroup $group, User $user)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }

        ChatGroupMember::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->delete();

        return response()->json(['message' => 'Đã từ chối người dùng']);
    }

    public function searchUsers(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) {
            return response()->json(['users' => []]);
        }

        $users = User::where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
            ]);

        return response()->json(['users' => $users]);
    }

    public function groupMessages(Request $request, ChatGroup $group)
    {
        if (! ChatGroupMember::where('group_id', $group->id)->where('user_id', auth()->id())->where('status', 'approved')->exists()) {
            abort(403);
        }

        $perPage = 20;
        $page = max(1, (int) $request->page);
        $query = ChatMessage::with('sender:id,name')
            ->where('group_id', $group->id);
        $total = $query->count();
        $messages = $query->latest()
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($m) => [
                'id' => $m->id,
                'sender_id' => $m->sender_id,
                'message' => $m->message,
                'sender_name' => $m->sender->name ?? 'Người dùng',
                'is_mine' => $m->sender_id === auth()->id(),
                'created_at' => $m->created_at->toIso8601String(),
            ]);

        return response()->json([
            'messages' => $messages,
            'has_more' => $page * $perPage < $total,
        ]);
    }

    public function sendGroup(Request $request, ChatGroup $group)
    {
        if (! ChatGroupMember::where('group_id', $group->id)->where('user_id', auth()->id())->where('status', 'approved')->exists()) {
            abort(403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = ChatMessage::create([
            'group_id' => $group->id,
            'sender_id' => auth()->id(),
            'message' => $data['message'],
        ]);

        $message->load('sender:id,name');

        $this->chatService->sendGroupMessage(
            (string) auth()->id(),
            (string) $group->id,
            $data['message']
        );

        return response()->json([
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'message' => $message->message,
                'sender_name' => $message->sender->name ?? 'Người dùng',
                'is_mine' => true,
                'created_at' => $message->created_at->toIso8601String(),
            ],
        ], 201);
    }
}
