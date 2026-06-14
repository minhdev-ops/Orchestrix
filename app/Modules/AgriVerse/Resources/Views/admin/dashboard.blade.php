@extends('layouts.admin')

@section('page-title', 'AgriVerse Hub')

@section('content')
<div class="space-y-8 pb-20">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">AgriVerse</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Nền tảng thương mại cây cảnh bonsai số — tổng quan hệ thống.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-6 py-3 rounded-2xl bg-surface-container-low text-on-surface text-xs font-black uppercase tracking-widest border border-outline-variant hover:bg-surface-container-high transition-all">
                <span class="material-symbols-outlined text-base align-text-bottom mr-1.5">download</span>
                Xuất báo cáo
            </button>
            <a href="{{ route('admin.agriverse.reports.index') }}" class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">
                <span class="material-symbols-outlined text-base align-text-bottom mr-1.5">bar_chart</span>
                Báo cáo chi tiết
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">store</span>
            </div>
            <div>
                <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Cửa hàng</h3>
                <p class="text-2xl font-black text-on-surface tracking-tight font-display">{{ $storesCount }}</p>
                <p class="text-[9px] text-emerald-600 font-black uppercase mt-1 tracking-widest">Đang hoạt động</p>
            </div>
        </div>

        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">inventory_2</span>
            </div>
            <div>
                <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Sản phẩm</h3>
                <p class="text-2xl font-black text-on-surface tracking-tight font-display">{{ $productsCount }}</p>
                <p class="text-[9px] text-emerald-600 font-black uppercase mt-1 tracking-widest">Đã đăng bán</p>
            </div>
        </div>

        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">receipt_long</span>
            </div>
            <div>
                <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Đơn hàng</h3>
                <p class="text-2xl font-black text-on-surface tracking-tight font-display">{{ $ordersCount }}</p>
                <p class="text-[9px] text-amber-600 font-black uppercase mt-1 tracking-widest">{{ $pendingOrders }} đang chờ</p>
            </div>
        </div>

        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">group</span>
            </div>
            <div>
                <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Người dùng</h3>
                <p class="text-2xl font-black text-on-surface tracking-tight font-display">{{ $usersCount }}</p>
                <p class="text-[9px] text-emerald-600 font-black uppercase mt-1 tracking-widest">Trên nền tảng</p>
            </div>
        </div>
    </div>

    {{-- Second Row: Revenue / Recent Activity --}}
    <div class="grid grid-cols-12 gap-6">
        {{-- Revenue & Commission --}}
        <div class="col-span-12 md:col-span-4 glass-panel rounded-3xl p-8 overflow-hidden relative">
            <div class="absolute right-0 bottom-0 w-40 h-40 bg-emerald-500/5 blur-3xl pointer-events-none"></div>
            <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] mb-6">Doanh thu & Hoa hồng</h3>
            <div class="space-y-6">
                <div>
                    <p class="text-sm font-bold text-on-surface-variant opacity-60">Tổng doanh thu</p>
                    <p class="text-3xl font-black text-on-surface tracking-tight font-display">{{ number_format($totalRevenue, 0) }}<span class="text-base font-bold text-on-surface-variant">đ</span></p>
                </div>
                <div class="h-px bg-outline-variant/30"></div>
                <div>
                    <p class="text-sm font-bold text-on-surface-variant opacity-60">Hoa hồng hệ thống</p>
                    <p class="text-2xl font-black text-amber-600 tracking-tight font-display">{{ number_format($totalCommission, 0) }}<span class="text-sm font-bold text-on-surface-variant">đ</span></p>
                </div>
                <div class="h-px bg-outline-variant/30"></div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-on-surface-variant">Cửa hàng hoạt động</span>
                    <span class="text-lg font-black text-on-surface">{{ $storesCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-on-surface-variant">Sản phẩm</span>
                    <span class="text-lg font-black text-on-surface">{{ $productsCount }}</span>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="col-span-12 md:col-span-8 glass-panel rounded-3xl p-8 overflow-hidden relative">
            <div class="absolute right-0 bottom-0 w-48 h-48 bg-emerald-500/5 blur-3xl pointer-events-none"></div>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Đơn hàng gần đây</h3>
                <a href="{{ route('admin.agriverse.orders.index') }}" class="text-[10px] font-black uppercase tracking-widest text-primary hover:text-primary/80 transition-colors">Xem tất cả</a>
            </div>
            <div class="space-y-4">
                @php
                    $recentOrders = \App\Modules\AgriVerse\Models\Order::with(['buyer', 'product'])->latest()->take(6)->get();
                @endphp
                @forelse($recentOrders as $order)
                <div class="flex items-center justify-between py-3 border-b border-outline-variant/10 last:border-0">
                    <div class="flex items-center gap-4">
                        <div class="w-2.5 h-2.5 rounded-full
                            @if($order->status === 'completed' || $order->status === 'delivered') bg-emerald-500 shadow-[0_0_12px_rgba(5,150,105,0.3)]
                            @elseif($order->status === 'cancelled') bg-red-500 shadow-[0_0_12px_rgba(220,38,38,0.3)]
                            @elseif($order->status === 'confirmed') bg-blue-500 shadow-[0_0_12px_rgba(0,82,255,0.3)]
                            @else bg-amber-500 shadow-[0_0_12px_rgba(217,119,6,0.3)]
                            @endif
                        "></div>
                        <div>
                            <p class="text-sm font-bold text-on-surface">{{ $order->product?->name ?? '#' . $order->uuid }}</p>
                            <p class="text-xs text-on-surface-variant opacity-60">{{ $order->buyer?->name ?? 'N/A' }} · {{ number_format($order->total_amount, 0) }}đ</p>
                        </div>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full
                        @if(in_array($order->status, ['completed', 'delivered'])) bg-emerald-50 text-emerald-600
                        @elseif($order->status === 'cancelled') bg-red-50 text-red-600
                        @elseif($order->status === 'confirmed') bg-blue-50 text-blue-600
                        @else bg-amber-50 text-amber-600
                        @endif
                    ">
                        @lang('order.status.' . $order->status)
                    </span>
                </div>
                @empty
                <p class="text-sm text-on-surface-variant opacity-60 text-center py-8">Chưa có đơn hàng nào.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-4 glass-panel rounded-3xl p-8 relative">
            <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] mb-6">Quản lý nhanh</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.agriverse.products.index') }}" class="flex flex-col items-center gap-3 p-5 rounded-2xl bg-surface-container-low/50 hover:bg-surface-container-low transition-all group">
                    <span class="material-symbols-outlined text-3xl text-primary group-hover:scale-110 transition-transform">inventory_2</span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Sản phẩm</span>
                </a>
                <a href="{{ route('admin.agriverse.stores.index') }}" class="flex flex-col items-center gap-3 p-5 rounded-2xl bg-surface-container-low/50 hover:bg-surface-container-low transition-all group">
                    <span class="material-symbols-outlined text-3xl text-emerald-600 group-hover:scale-110 transition-transform">store</span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Cửa hàng</span>
                </a>
                <a href="{{ route('admin.agriverse.orders.index') }}" class="flex flex-col items-center gap-3 p-5 rounded-2xl bg-surface-container-low/50 hover:bg-surface-container-low transition-all group">
                    <span class="material-symbols-outlined text-3xl text-amber-600 group-hover:scale-110 transition-transform">receipt_long</span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Đơn hàng</span>
                </a>
                <a href="{{ route('admin.agriverse.categories.index') }}" class="flex flex-col items-center gap-3 p-5 rounded-2xl bg-surface-container-low/50 hover:bg-surface-container-low transition-all group">
                    <span class="material-symbols-outlined text-3xl text-primary group-hover:scale-110 transition-transform">category</span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Danh mục</span>
                </a>
            </div>
        </div>

        {{-- System Status --}}
        <div class="col-span-12 md:col-span-8 glass-panel rounded-3xl p-8 relative">
            <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] mb-6">Trạng thái hệ thống</h3>
            <div class="space-y-4">
                <div class="flex gap-4 items-start py-3 border-b border-outline-variant/10">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-1.5 shadow-[0_0_12px_rgba(5,150,105,0.3)]"></div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-on-surface">AgriVerse Platform</p>
                        <p class="text-xs text-on-surface-variant opacity-60">Tất cả dịch vụ đang hoạt động bình thường.</p>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full bg-emerald-50 text-emerald-600">Hoạt động</span>
                </div>
                <div class="flex gap-4 items-start py-3 border-b border-outline-variant/10">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-1.5 shadow-[0_0_12px_rgba(5,150,105,0.3)]"></div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-on-surface">3D Model Service</p>
                        <p class="text-xs text-on-surface-variant opacity-60">Máy chủ render và nén 3D đang sẵn sàng.</p>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full bg-emerald-50 text-emerald-600">Hoạt động</span>
                </div>
                <div class="flex gap-4 items-start py-3 border-b border-outline-variant/10">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-1.5 shadow-[0_0_12px_rgba(5,150,105,0.3)]"></div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-on-surface">AI Scanning Queue</p>
                        <p class="text-xs text-on-surface-variant opacity-60">Hàng đợi scan AI đang xử lý.</p>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full bg-amber-50 text-amber-600">{{ $pendingScans ?? 0 }} Đang chờ</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
