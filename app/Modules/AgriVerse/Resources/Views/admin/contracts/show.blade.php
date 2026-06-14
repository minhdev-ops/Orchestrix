@extends('layouts.admin')

@section('page-title', 'AgriVerse — Chi tiết Hợp đồng')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-20">
    <div>
        <a href="{{ route('admin.agriverse.contracts.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-on-surface-variant hover:text-primary transition-colors mb-4">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại danh sách
        </a>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Hợp đồng <span class="text-primary font-mono">{{ $contract->contract_number ?? '#' . substr($contract->uuid, 0, 8) }}</span></h2>
    </div>

    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-8 space-y-6">
            <div class="card-premium space-y-5">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Nội dung hợp đồng</h3>
                <div class="prose prose-sm max-w-none text-on-surface">
                    {!! nl2br(e($contract->content ?? 'Không có nội dung.')) !!}
                </div>
            </div>

            <div class="card-premium space-y-4">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Thông tin đơn hàng</h3>
                @if($contract->order)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Sản phẩm</p>
                        <p class="text-sm font-black text-on-surface">{{ $contract->order->product?->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Tổng tiền</p>
                        <p class="text-sm font-black text-on-surface font-mono">{{ number_format($contract->order->total_amount, 0) }}đ</p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Người bán</p>
                        <p class="text-sm font-black text-on-surface">{{ $contract->order->seller?->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Người mua</p>
                        <p class="text-sm font-black text-on-surface">{{ $contract->order->buyer?->name ?? 'N/A' }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="col-span-12 md:col-span-4 space-y-6">
            <div class="card-premium space-y-4">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Trạng thái ký kết</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-on-surface-variant">Người bán</span>
                        <span class="px-3 py-1 text-[9px] font-black uppercase tracking-widest rounded-full border
                            {{ $contract->signed_by_seller ? 'bg-emerald-50 text-emerald-600 border-emerald-200/60' : 'bg-amber-50 text-amber-600 border-amber-200/60' }}">
                            {{ $contract->signed_by_seller ? 'Đã ký' : 'Chờ ký' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-on-surface-variant">Người mua</span>
                        <span class="px-3 py-1 text-[9px] font-black uppercase tracking-widest rounded-full border
                            {{ $contract->signed_by_buyer ? 'bg-emerald-50 text-emerald-600 border-emerald-200/60' : 'bg-amber-50 text-amber-600 border-amber-200/60' }}">
                            {{ $contract->signed_by_buyer ? 'Đã ký' : 'Chờ ký' }}
                        </span>
                    </div>
                    @if($contract->signed_at)
                    <div class="h-px bg-outline-variant/30"></div>
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Ngày ký</p>
                        <p class="text-sm font-black text-on-surface">{{ $contract->signed_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                    <div class="h-px bg-outline-variant/30"></div>
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Trạng thái</p>
                        <span class="px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full border inline-block mt-1
                            @if($contract->status === 'signed') bg-emerald-50 text-emerald-600 border-emerald-200/60
                            @elseif($contract->status === 'cancelled') bg-red-50 text-red-600 border-red-200/60
                            @else bg-amber-50 text-amber-600 border-amber-200/60
                            @endif
                        ">{{ $contract->status ?? 'pending' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
