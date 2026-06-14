@extends('layouts.admin')

@section('page-title', 'Hệ thống Điều khiển')

@section('content')
    <div class="space-y-8 pb-20">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Dashboard</h2>
                <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Tổng quan hệ thống Orchestrix AgriVerse.</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-6 py-3 rounded-2xl bg-surface-container-low text-on-surface text-xs font-black uppercase tracking-widest border border-outline-variant hover:bg-surface-container-high transition-all">
                    Đồng bộ dữ liệu
                </button>
                <button class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">
                    Báo cáo hệ thống
                </button>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-12 gap-6">
            {{-- Revenue --}}
            <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
                <div class="w-10 h-10 rounded-xl bg-success/10 text-success flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl">payments</span>
                </div>
                <div>
                    <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Doanh thu</h3>
                    <p class="text-2xl font-black text-on-surface tracking-tight">{{ number_format($totalRevenue ?? 0) }}đ</p>
                </div>
            </div>

            {{-- Commission --}}
            <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
                <div class="w-10 h-10 rounded-xl bg-warning/10 text-warning flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl">commission</span>
                </div>
                <div>
                    <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Hoa hồng</h3>
                    <p class="text-2xl font-black text-on-surface tracking-tight">{{ number_format($commissionEarned ?? 0) }}đ</p>
                </div>
            </div>

            {{-- Orders --}}
            <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                </div>
                <div>
                    <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Đơn hàng</h3>
                    <p class="text-2xl font-black text-on-surface tracking-tight">{{ $ordersCount ?? 0 }}</p>
                    <p class="text-[9px] text-warning font-black uppercase mt-1 tracking-widest">{{ $pendingOrders ?? 0 }} đang chờ</p>
                </div>
            </div>

            {{-- Reviews --}}
            <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
                <div class="w-10 h-10 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl">star</span>
                </div>
                <div>
                    <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Đánh giá</h3>
                    <p class="text-2xl font-black text-on-surface tracking-tight">{{ $reviewsCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- Second Row: Charts / Recent Activity --}}
        <div class="grid grid-cols-12 gap-6 mt-6">
            {{-- Recent Orders --}}
            <div class="col-span-12 md:col-span-7 glass-panel rounded-3xl p-8 overflow-hidden relative">
                <div class="absolute right-0 bottom-0 w-32 h-32 bg-primary/5 blur-3xl pointer-events-none"></div>
                <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.25em] mb-6">Đơn hàng gần đây</h3>
                <div class="space-y-4">
                    @php
                        $recentOrders = \Modules\AgriVerse\Models\Order::with(['buyer', 'product'])->latest()->take(5)->get();
                    @endphp
                    @forelse($recentOrders as $order)
                        <div class="flex gap-4 items-start justify-between">
                            <div class="flex gap-4 items-start">
                                <div class="w-2 h-2 rounded-full mt-1.5
                                    @if($order->status === 'completed') bg-success shadow-[0_0_10px_rgba(34,197,94,0.4)]
                                    @elseif($order->status === 'cancelled') bg-error shadow-[0_0_10px_rgba(239,68,68,0.4)]
                                    @elseif($order->status === 'confirmed') bg-primary shadow-[0_0_10px_rgba(0,82,255,0.4)]
                                    @else bg-warning shadow-[0_0_10px_rgba(234,179,8,0.4)]
                                    @endif
                                "></div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface">
                                        {{ $order->product?->name ?? '#' . $order->uuid }}
                                    </p>
                                    <p class="text-xs text-on-surface-variant opacity-60">
                                        {{ $order->buyer?->email }} · {{ number_format($order->total_amount, 0) }}đ
                                    </p>
                                    <p class="text-[9px] font-black text-on-surface-variant uppercase mt-1 tracking-widest opacity-50">
                                        {{ $order->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full
                                @if($order->status === 'completed') bg-success/10 text-success
                                @elseif($order->status === 'cancelled') bg-error/10 text-error
                                @elseif($order->status === 'confirmed') bg-primary/10 text-primary
                                @else bg-warning/10 text-warning
                                @endif
                            ">
                                @lang('order.status.' . $order->status)
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-on-surface-variant opacity-60">Chưa có đơn hàng nào.</p>
                    @endforelse
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="col-span-12 md:col-span-5 glass-panel rounded-3xl p-8 overflow-hidden relative">
                <div class="absolute right-0 bottom-0 w-32 h-32 bg-secondary/5 blur-3xl pointer-events-none"></div>
                <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.25em] mb-6">Thống kê nhanh</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-on-surface">Cửa hàng</span>
                        <span class="text-sm font-black text-on-surface">{{ $storesCount ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-on-surface">Sản phẩm</span>
                        <span class="text-sm font-black text-on-surface">{{ $productsCount ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-on-surface">Người dùng</span>
                        <span class="text-sm font-black text-on-surface">{{ $usersCount ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-on-surface">Doanh thu</span>
                        <span class="text-sm font-black text-success">{{ number_format($totalRevenue ?? 0, 0) }}đ</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-on-surface">Hoa hồng</span>
                        <span class="text-sm font-black text-warning">{{ number_format($commissionEarned ?? 0, 0) }}đ</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-on-surface">Đánh giá</span>
                        <span class="text-sm font-black text-on-surface">{{ $reviewsCount ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity --}}
        <div class="mt-6">
            <div class="col-span-12 glass-panel rounded-3xl p-8 overflow-hidden relative">
                <div class="absolute right-0 bottom-0 w-32 h-32 bg-primary/5 blur-3xl pointer-events-none"></div>
                <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.25em] mb-6">Hoạt động gần đây</h3>
                <div class="space-y-4">
                    <div class="flex gap-4 items-start">
                        <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shadow-[0_0_10px_rgba(0,82,255,0.4)]"></div>
                        <div>
                            <p class="text-sm font-bold text-on-surface">Hệ thống sẵn sàng</p>
                            <p class="text-xs text-on-surface-variant opacity-60">Orchestrix AgriVerse đang hoạt động.</p>
                            <p class="text-[9px] font-black text-primary uppercase mt-2 tracking-widest">Vừa xong</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
