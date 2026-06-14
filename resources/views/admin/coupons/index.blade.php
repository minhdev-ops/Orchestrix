@extends('layouts.admin')
@section('page-title', 'Mã giảm giá')
@section('content')
<div class="space-y-6 pb-20">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Mã giảm giá</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Quản lý mã giảm giá trên hệ thống.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">
            + Thêm mã
        </a>
    </div>

    <div class="glass-panel rounded-3xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-outline-variant">
                    <th class="text-left p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Mã</th>
                    <th class="text-left p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Tên</th>
                    <th class="text-left p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Loại</th>
                    <th class="text-left p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Giá trị</th>
                    <th class="text-left p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Đã dùng</th>
                    <th class="text-left p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Hạn dùng</th>
                    <th class="text-right p-4 text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="p-4 font-bold text-on-surface">{{ $coupon->code }}</td>
                    <td class="p-4 text-on-surface">{{ $coupon->name }}</td>
                    <td class="p-4">
                        <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full {{ $coupon->type === 'percent' ? 'bg-secondary/10 text-secondary' : 'bg-primary/10 text-primary' }}">
                            {{ $coupon->type === 'percent' ? '%' : 'VNĐ' }}
                        </span>
                    </td>
                    <td class="p-4 font-bold text-on-surface">{{ $coupon->type === 'percent' ? $coupon->value . '%' : number_format($coupon->value, 0) . 'đ' }}</td>
                    <td class="p-4">
                        <span class="{{ $coupon->usage_limit > 0 && $coupon->used_count >= $coupon->usage_limit ? 'text-error' : 'text-on-surface' }}">
                            {{ $coupon->used_count }}/{{ $coupon->usage_limit > 0 ? $coupon->usage_limit : '∞' }}
                        </span>
                    </td>
                    <td class="p-4 text-on-surface-variant">
                        {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y') : 'Không' }}
                    </td>
                    <td class="p-4 text-right">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-primary hover:underline text-xs font-bold">Sửa</a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Xóa mã này?')">
                            @csrf @method('DELETE')
                            <button class="text-error hover:underline text-xs font-bold ml-3">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-8 text-center text-on-surface-variant opacity-60">Chưa có mã giảm giá nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $coupons->links() }}</div>
</div>
@endsection
