@extends('layouts.admin')

@section('page-title', 'AgriVerse — Mã Giảm giá')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Mã giảm giá</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Quản lý mã khuyến mãi cho sản phẩm cây cảnh.</p>
        </div>
        <a href="{{ route('admin.agriverse.coupons.create') }}" class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">
            <span class="material-symbols-outlined text-base align-text-bottom mr-1">add</span>
            Thêm mã
        </a>
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
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Mã</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Loại</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Giá trị</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Đã dùng</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Hạn dùng</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($coupons as $coupon)
                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-on-surface font-mono tracking-wider">{{ $coupon->code }}</span>
                            <span class="text-xs text-on-surface-variant opacity-60">{{ $coupon->name }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 text-[9px] font-black uppercase tracking-widest rounded-full border
                            {{ $coupon->type === 'percent' ? 'bg-blue-50 text-blue-600 border-blue-200/60' : 'bg-amber-50 text-amber-600 border-amber-200/60' }}">
                            {{ $coupon->type === 'percent' ? 'Percent' : 'Fixed' }}
                        </span>
                    </td>
                    <td class="px-8 py-6 font-bold font-mono text-on-surface">
                        {{ $coupon->type === 'percent' ? $coupon->value . '%' : number_format($coupon->value, 0) . 'đ' }}
                    </td>
                    <td class="px-8 py-6 font-mono text-sm text-on-surface-variant">
                        {{ $coupon->used_count ?? 0 }}{{ $coupon->usage_limit ? '/' . $coupon->usage_limit : '' }}
                    </td>
                    <td class="px-8 py-6">
                        @if($coupon->expires_at)
                            <span class="text-xs font-bold {{ $coupon->expires_at->isPast() ? 'text-red-500' : 'text-on-surface-variant' }}">
                                {{ $coupon->expires_at->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-xs text-on-surface-variant opacity-60">Không giới hạn</span>
                        @endif
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end items-center gap-2">
                            <a href="{{ route('admin.agriverse.coupons.edit', $coupon->id) }}" class="p-2.5 rounded-xl text-on-surface-variant hover:text-primary hover:bg-primary/5 transition-all">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                            <form action="{{ route('admin.agriverse.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Xóa mã giảm giá này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 rounded-xl text-on-surface-variant hover:text-red-500 hover:bg-red-50 transition-all">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $coupons->links() }}</div>
</div>
@endsection
