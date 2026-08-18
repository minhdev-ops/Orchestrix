<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Modules\AgriVerse\Models\OrderStatus;
use App\Modules\AgriVerse\Models\Refund;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RefundController
{
    public function index(Request $request)
    {
        $query = Refund::with(['order.product', 'user', 'processor']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return Inertia::render('Admin/Refunds/Index', [
            'refunds' => $query->latest()->paginate(15),
            'filter' => $request->only('status'),
        ]);
    }

    public function show(Refund $refund)
    {
        $refund->load(['order.product', 'order.store', 'user', 'processor']);

        return Inertia::render('Admin/Refunds/Show', ['refund' => $refund]);
    }

    public function approve(Request $request, Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý.');
        }

        $validated = $request->validate(['note' => 'nullable|string|max:500']);
        $refund->update([
            'status' => 'approved',
            'admin_note' => $validated['note'] ?? $refund->admin_note,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        $order = $refund->order;
        $order->update(['status' => 'refunded']);

        $order->product?->increment('stock', $order->quantity);

        OrderStatus::create([
            'order_id' => $refund->order_id,
            'status' => 'refunded',
            'note' => 'Hoàn tiền được duyệt: '.($validated['note'] ?? ''),
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Đã duyệt hoàn tiền.');
    }

    public function reject(Request $request, Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý.');
        }

        $data = $request->validate(['note' => 'required|string|max:500']);

        $refund->update([
            'status' => 'rejected',
            'admin_note' => $data['note'],
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Đã từ chối hoàn tiền.');
    }
}
