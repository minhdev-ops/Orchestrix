@extends('layouts.admin')
@section('page-title', 'Báo cáo người bán')
@section('content')
<div class="space-y-8 pb-20">
    <div>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Báo cáo người bán</h2>
        <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Top 5 người bán có doanh thu cao nhất.</p>
    </div>

    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-5 glass-panel rounded-3xl p-8">
            <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.25em] mb-6">Top người bán</h3>
            <div class="space-y-4">
                @forelse($topSellers as $i => $seller)
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-xl {{ $i === 0 ? 'bg-warning/20 text-warning' : 'bg-surface-container-high text-on-surface-variant' }} flex items-center justify-center text-sm font-black">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-on-surface">{{ $seller->name ?: $seller->email }}</p>
                        <p class="text-xs text-on-surface-variant opacity-60">
                            {{ $seller->stores_count ?? 0 }} cửa hàng · {{ $seller->products_count ?? 0 }} sản phẩm
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-success">{{ number_format($seller->total_revenue ?? 0, 0) }}đ</p>
                        <p class="text-[9px] text-on-surface-variant opacity-60">{{ $seller->completed_orders ?? 0 }} đơn</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-on-surface-variant opacity-60">Chưa có dữ liệu.</p>
                @endforelse
            </div>
        </div>

        <div class="col-span-12 md:col-span-7 glass-panel rounded-3xl overflow-hidden">
            <div class="p-6">
                <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.25em] mb-6">Tất cả người bán</h3>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Người bán</th>
                        <th class="text-right p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Cửa hàng</th>
                        <th class="text-right p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">SP</th>
                        <th class="text-right p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Đơn</th>
                        <th class="text-right p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sellers as $seller)
                    <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                        <td class="p-4 font-bold text-on-surface">{{ $seller->name ?: $seller->email }}</td>
                        <td class="p-4 text-right text-on-surface">{{ $seller->stores_count ?? 0 }}</td>
                        <td class="p-4 text-right text-on-surface">{{ $seller->products_count ?? 0 }}</td>
                        <td class="p-4 text-right text-on-surface">{{ $seller->completed_orders ?? 0 }}</td>
                        <td class="p-4 text-right font-bold text-success">{{ number_format($seller->total_revenue ?? 0, 0) }}đ</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-on-surface-variant opacity-60">Chưa có dữ liệu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
