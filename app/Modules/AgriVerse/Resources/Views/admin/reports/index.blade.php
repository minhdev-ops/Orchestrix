@extends('layouts.admin')

@section('page-title', 'AgriVerse — Báo cáo')

@section('content')
<div class="space-y-8 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Báo cáo</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Phân tích hiệu suất nền tảng AgriVerse.</p>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6">
            <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">Doanh thu</p>
            <p class="text-2xl font-black text-on-surface tracking-tight font-display">{{ number_format($totalRevenue, 0) }}đ</p>
        </div>
        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6">
            <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest mb-1">Hoa hồng</p>
            <p class="text-2xl font-black text-on-surface tracking-tight font-display">{{ number_format($totalCommission, 0) }}đ</p>
        </div>
        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6">
            <p class="text-[9px] font-black text-primary uppercase tracking-widest mb-1">Đơn hàng chờ</p>
            <p class="text-2xl font-black text-on-surface tracking-tight font-display">{{ $pendingOrders }}</p>
        </div>
        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6">
            <p class="text-[9px] font-black text-on-surface-variant uppercase tracking-widest mb-1">Người dùng</p>
            <p class="text-2xl font-black text-on-surface tracking-tight font-display">{{ $usersCount }}</p>
        </div>
    </div>

    {{-- Revenue Chart --}}
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-7 glass-panel rounded-3xl p-8 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-48 h-48 bg-emerald-500/5 blur-3xl pointer-events-none"></div>
            <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] mb-6">Doanh thu theo tháng</h3>
            @if($revenueByMonth->count() > 0)
                <div class="space-y-3">
                    @foreach($revenueByMonth as $item)
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold text-on-surface-variant w-16 font-mono">{{ $item->month }}</span>
                        <div class="flex-1 h-5 rounded-full bg-surface-container-low overflow-hidden">
                            @php
                                $maxRev = $revenueByMonth->max('total');
                                $pct = $maxRev > 0 ? ($item->total / $maxRev) * 100 : 0;
                            @endphp
                            <div class="h-full rounded-full bg-emerald-500/70" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs font-black font-mono text-on-surface">{{ number_format($item->total, 0) }}đ</span>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-on-surface-variant opacity-60 text-center py-8">Chưa có dữ liệu doanh thu.</p>
            @endif
        </div>

        <div class="col-span-12 md:col-span-5 glass-panel rounded-3xl p-8 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-48 h-48 bg-primary/5 blur-3xl pointer-events-none"></div>
            <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] mb-6">Sản phẩm bán chạy</h3>
            <div class="space-y-4">
                @forelse($topProducts as $product)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-black">
                            {{ $loop->iteration }}
                        </div>
                        <span class="text-sm font-bold text-on-surface">{{ Str::limit($product->name, 25) }}</span>
                    </div>
                    <span class="text-sm font-black font-mono text-on-surface-variant">{{ $product->total_sold ?? 0 }}</span>
                </div>
                @empty
                <p class="text-sm text-on-surface-variant opacity-60 text-center py-8">Chưa có dữ liệu.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Top Stores --}}
    <div class="glass-panel rounded-3xl p-8 relative overflow-hidden">
        <div class="absolute right-0 bottom-0 w-48 h-48 bg-amber-500/5 blur-3xl pointer-events-none"></div>
        <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] mb-6">Cửa hàng hàng đầu</h3>
        <div class="grid grid-cols-12 gap-4">
            @forelse($topStores as $store)
            <div class="col-span-12 md:col-span-3 flex items-center gap-4 p-4 rounded-2xl bg-surface-container-low/50">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-xs">
                    {{ strtoupper(substr($store->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-black text-on-surface">{{ $store->name }}</p>
                    <p class="text-xs text-on-surface-variant font-mono">{{ $store->products_count }} sản phẩm</p>
                </div>
            </div>
            @empty
            <div class="col-span-12 text-center py-8 text-sm text-on-surface-variant opacity-60">Chưa có dữ liệu.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
