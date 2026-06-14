@extends('layouts.admin')

@section('page-title', 'AgriVerse — Quản lý Đơn hàng')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Đơn hàng</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Theo dõi và quản lý đơn hàng cây cảnh.</p>
        </div>
        <form method="GET" class="flex items-center gap-3">
            <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Shipping</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                <input type="text" name="search" placeholder="Mã đơn hoặc người mua..." value="{{ request('search') }}"
                    class="pl-11 pr-4 py-2.5 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="p-5 bg-emerald-50 border border-emerald-200/60 text-emerald-700 rounded-2xl font-bold flex items-center gap-3 text-sm">
            <span class="material-symbols-outlined text-emerald-500">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="card-premium !p-0 overflow-hidden bg-white shadow-xl shadow-black/[0.02] rounded-3xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Mã đơn</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Sản phẩm</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Người mua</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Tổng tiền</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Trạng thái</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-8 py-6">
                        <span class="font-mono text-sm font-bold text-on-surface">#{{ substr($order->uuid, 0, 8) }}</span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-sm font-bold text-on-surface">{{ $order->product?->name ?? 'N/A' }}</span>
                    </td>
                    <td class="px-8 py-6 text-on-surface font-semibold text-sm">{{ $order->buyer?->name ?? 'N/A' }}</td>
                    <td class="px-8 py-6">
                        <span class="font-bold font-mono text-on-surface">{{ number_format($order->total_amount, 0) }}đ</span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full border
                            @if(in_array($order->status, ['completed', 'delivered'])) bg-emerald-50 text-emerald-600 border-emerald-200/60
                            @elseif($order->status === 'cancelled') bg-red-50 text-red-600 border-red-200/60
                            @elseif($order->status === 'confirmed') bg-blue-50 text-blue-600 border-blue-200/60
                            @elseif($order->status === 'shipping') bg-indigo-50 text-indigo-600 border-indigo-200/60
                            @else bg-amber-50 text-amber-600 border-amber-200/60
                            @endif
                        ">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <a href="{{ route('admin.agriverse.orders.show', $order->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-surface-container-low text-on-surface text-xs font-black hover:bg-surface-container-high transition-all">
                            <span class="material-symbols-outlined text-base">visibility</span>
                            Chi tiết
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</div>
@endsection
