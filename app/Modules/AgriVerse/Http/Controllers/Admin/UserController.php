<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Models\User;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Review;
use App\Modules\AgriVerse\Models\UserAddress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = $request->role) {
            $query->where('role', $role);
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function show(User $user)
    {
        $user->load(['stores']);

        $orders = Order::where('buyer_id', $user->id)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $reviews = Review::where('user_id', $user->id)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $addresses = UserAddress::where('user_id', $user->id)->get();

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'orders' => $orders,
            'reviews' => $reviews,
            'addresses' => $addresses,
        ]);
    }

    public function toggleActive(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể tự thay đổi trạng thái của chính mình.');
        }

        if ($user->isAdmin()) {
            return back()->with('error', 'Không thể thay đổi trạng thái tài khoản admin.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'Người dùng đã được kích hoạt.' : 'Người dùng đã bị vô hiệu hóa.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể tự xóa tài khoản của chính mình.');
        }

        $user->delete();

        return redirect()->route('admin.agriverse.users.index')
            ->with('success', 'Người dùng đã được xóa.');
    }
}
