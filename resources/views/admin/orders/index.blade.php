@extends('layouts.admin')

@section('page-title', 'Quản lý Đơn hàng')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Đơn hàng</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Quản lý tất cả đơn hàng trên nền tảng.</p>
        </div>
        <div class="flex items-center gap-4">
            <form method="GET" class="flex items-center gap-3">
                <select name="status" onchange="this.form.submit()" class="px-4 py-2 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <input type="text" name="search" placeholder="Tìm kiếm..." value="{{ request('search') }}"
                    class="px-4 py-2 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none">
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-tertiary/10 border border-tertiary/20 text-tertiary rounded-2xl font-bold flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="card-premium !p-0 overflow-hidden bg-white shadow-xl shadow-black/[0.02]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Đơn hàng</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Người mua</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Người bán</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Tổng tiền</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Trạng thái</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black text-xs">
                                #{{ $order->id }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-base font-black text-on-surface">{{ $order->product?->name ?? 'N/A' }}</span>
                                <span class="text-xs text-on-surface-variant opacity-60">SL: {{ $order->quantity }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-on-surface font-semibold">{{ $order->buyer?->name ?? 'N/A' }}</td>
                    <td class="px-8 py-6 text-on-surface font-semibold">{{ $order->seller?->name ?? 'N/A' }}</td>
                    <td class="px-8 py-6 text-on-surface font-bold">{{ number_format($order->total_price, 0) }}đ</td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                            @switch($order->status)
                                @case('pending') bg-yellow-100 text-yellow-800 @break
                                @case('confirmed') bg-blue-100 text-blue-800 @break
                                @case('delivered') bg-purple-100 text-purple-800 @break
                                @case('completed') bg-green-100 text-green-800 @break
                                @case('cancelled') bg-red-100 text-red-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="px-4 py-2 bg-primary text-on-primary rounded-xl text-xs font-black uppercase tracking-wider hover:bg-primary/90 transition-colors">
                            Chi tiết
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-12 text-center text-on-surface-variant opacity-50">
                        Chưa có đơn hàng nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $orders->appends(request()->query())->links() }}
    </div>
</div>
@endsection
