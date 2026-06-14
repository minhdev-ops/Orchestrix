<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Modules\AgriVerse\Models\SellerVerification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SellerController
{
    public function index(Request $request)
    {
        $query = SellerVerification::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return Inertia::render('Admin/Sellers/Index', [
            'verifications' => $query->latest()->paginate(15),
            'filter' => $request->only('status'),
        ]);
    }

    public function show(SellerVerification $verification)
    {
        $verification->load('user');
        return Inertia::render('Admin/Sellers/Show', [
            'verification' => $verification,
        ]);
    }

    public function approve(SellerVerification $verification)
    {
        if ($verification->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý.');
        }

        $user = $verification->user;

        $verification->update([
            'status' => 'approved',
            'verified_at' => now(),
        ]);

        $user->update([
            'role' => 'seller',
            'seller_verified_at' => now(),
        ]);

        $user->assignRole('seller');

        return back()->with('success', 'Đã duyệt người bán.');
    }

    public function reject(Request $request, SellerVerification $verification)
    {
        if ($verification->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý.');
        }

        $data = $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $verification->update([
            'status' => 'rejected',
            'reject_reason' => $data['reject_reason'],
        ]);

        return back()->with('success', 'Đã từ chối yêu cầu.');
    }
}
