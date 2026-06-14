<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Modules\AgriVerse\Models\ChatGroup;
use App\Modules\AgriVerse\Models\ChatGroupMember;
use App\Modules\AgriVerse\Models\ChatMessage;
use App\Models\User;
use Inertia\Inertia;

class ChatGroupController
{
    public function index()
    {
        $groups = ChatGroup::with('creator:id,name')
            ->withCount(['approvedMembers as member_count'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $groups->getCollection()->transform(fn ($g) => [
            'id' => $g->id,
            'name' => $g->name,
            'avatar' => $g->avatar,
            'member_count' => $g->member_count,
            'created_by' => $g->creator->name ?? 'Người dùng',
            'created_at' => $g->created_at?->toIso8601String(),
        ]);

        return Inertia::render('Admin/ChatGroups/Index', [
            'groups' => $groups,
        ]);
    }

    public function show(ChatGroup $group)
    {
        $group->load('creator:id,name');

        $members = $group->approvedMembers()->get()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
        ]);

        $pendingMembers = $group->pendingMembers()->get()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
        ]);

        $messages = ChatMessage::with('sender:id,name')
            ->where('group_id', $group->id)
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($m) => [
                'id' => $m->id,
                'sender_id' => $m->sender_id,
                'message' => $m->message,
                'sender_name' => $m->sender->name ?? 'Người dùng',
                'created_at' => $m->created_at->toIso8601String(),
            ]);

        return Inertia::render('Admin/ChatGroups/Show', [
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'avatar' => $group->avatar,
                'created_by' => $group->creator->name ?? 'Người dùng',
                'member_count' => $members->count(),
            ],
            'members' => $members,
            'pendingMembers' => $pendingMembers,
            'messages' => $messages,
        ]);
    }

    public function approveMember(ChatGroup $group, User $user)
    {
        $member = ChatGroupMember::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $member->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Đã duyệt người dùng vào nhóm');
    }

    public function rejectMember(ChatGroup $group, User $user)
    {
        ChatGroupMember::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->delete();

        return redirect()->back()->with('success', 'Đã từ chối người dùng');
    }

    public function store(Request $request)
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

        return redirect()->route('admin.agriverse.chat-groups.show', $group)
            ->with('success', 'Đã tạo nhóm chat');
    }

    public function destroy(ChatGroup $group)
    {
        $group->delete();
        return redirect()->route('admin.agriverse.chat-groups.index')
            ->with('success', 'Đã xóa nhóm chat');
    }
}
