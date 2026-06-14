@extends('layouts.admin')
@section('page-title', 'Báo cáo doanh thu')
@section('content')
<div class="space-y-8 pb-20">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Báo cáo doanh thu</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Tổng quan doanh thu và hoa hồng hệ thống.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.revenue', ['period' => 'day']) }}" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $period === 'day' ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-on-surface' }} transition-all">Ngày</a>
            <a href="{{ route('admin.reports.revenue', ['period' => 'week']) }}" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $period === 'week' ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-on-surface' }} transition-all">Tuần</a>
            <a href="{{ route('admin.reports.revenue', ['period' => 'month']) }}" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $period === 'month' ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-on-surface' }} transition-all">Tháng</a>
            <a href="{{ route('admin.reports.revenue', ['period' => 'year']) }}" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $period === 'year' ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-on-surface' }} transition-all">Năm</a>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6">
            <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Tổng doanh thu</h3>
            <p class="text-2xl font-black text-on-surface tracking-tight mt-2">{{ number_format($totalRevenue, 0) }}đ</p>
        </div>
        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6">
            <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Tổng hoa hồng</h3>
            <p class="text-2xl font-black text-warning tracking-tight mt-2">{{ number_format($totalCommission, 0) }}đ</p>
        </div>
        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6">
            <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Tổng đơn hàng</h3>
            <p class="text-2xl font-black text-on-surface tracking-tight mt-2">{{ number_format($totalOrders) }}</p>
        </div>
        <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6">
            <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Giá trị TB/đơn</h3>
            <p class="text-2xl font-black text-on-surface tracking-tight mt-2">{{ number_format($avgOrderValue, 0) }}đ</p>
        </div>
    </div>

    <div class="glass-panel rounded-3xl p-8">
        <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.25em] mb-6">Dữ liệu doanh thu</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-outline-variant">
                    <th class="text-left p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Kỳ</th>
                    <th class="text-right p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Số giao dịch</th>
                    <th class="text-right p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Doanh thu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($revenueData as $row)
                <tr class="border-b border-outline-variant/50">
                    <td class="p-4 font-bold text-on-surface">{{ $row->period }}</td>
                    <td class="p-4 text-right text-on-surface">{{ $row->count }}</td>
                    <td class="p-4 text-right font-bold text-success">{{ number_format($row->total, 0) }}đ</td>
                </tr>
                @empty
                <tr><td colspan="3" class="p-8 text-center text-on-surface-variant opacity-60">Chưa có dữ liệu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
