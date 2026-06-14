@extends('layouts.admin')
@section('page-title', 'Báo cáo hoa hồng')
@section('content')
<div class="space-y-8 pb-20">
    <div>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Báo cáo hoa hồng người bán</h2>
        <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Tổng hoa hồng hệ thống: <strong class="text-warning">{{ number_format($totalCommission, 0) }}đ</strong></p>
    </div>

    <div class="glass-panel rounded-3xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-outline-variant">
                    <th class="text-left p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Người bán</th>
                    <th class="text-right p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Email</th>
                    <th class="text-right p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Đơn hoàn thành</th>
                    <th class="text-right p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Doanh thu</th>
                    <th class="text-right p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Hoa hồng</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissionBySeller as $seller)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="p-4 font-bold text-on-surface">{{ $seller->name ?: 'N/A' }}</td>
                    <td class="p-4 text-right text-on-surface-variant">{{ $seller->email }}</td>
                    <td class="p-4 text-right text-on-surface">{{ $seller->total_orders ?? 0 }}</td>
                    <td class="p-4 text-right font-bold text-on-surface">{{ number_format($seller->total_revenue ?? 0, 0) }}đ</td>
                    <td class="p-4 text-right font-bold text-warning">{{ number_format($seller->total_commission ?? 0, 0) }}đ</td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center text-on-surface-variant opacity-60">Chưa có dữ liệu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
