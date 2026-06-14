@extends('layouts.admin')

@section('page-title', 'AgriVerse — Chi tiết Giao dịch')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 pb-20">
    <div>
        <a href="{{ route('admin.agriverse.transactions.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-on-surface-variant hover:text-primary transition-colors mb-4">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại danh sách
        </a>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Giao dịch <span class="text-primary font-mono">#{{ $transaction->transaction_id }}</span></h2>
    </div>

    <div class="card-premium space-y-6">
        <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Chi tiết thanh toán</h3>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Người thanh toán</p>
                <p class="text-sm font-black text-on-surface">{{ $transaction->user?->name ?? 'N/A' }}</p>
                <p class="text-xs text-on-surface-variant">{{ $transaction->user?->email ?? '' }}</p>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Phương thức</p>
                <p class="text-sm font-black text-on-surface">{{ $transaction->payment_method ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Số tiền</p>
                <p class="text-2xl font-black text-on-surface font-mono">{{ number_format($transaction->amount, 0) }}đ</p>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Trạng thái</p>
                <span class="px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full border inline-block mt-1
                    @if($transaction->payment_status === 'paid') bg-emerald-50 text-emerald-600 border-emerald-200/60
                    @elseif($transaction->payment_status === 'failed') bg-red-50 text-red-600 border-red-200/60
                    @else bg-amber-50 text-amber-600 border-amber-200/60
                    @endif
                ">{{ $transaction->payment_status ?? 'pending' }}</span>
            </div>
        </div>
        <div class="h-px bg-outline-variant/30"></div>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Phí hoa hồng</p>
                <p class="text-sm font-black text-on-surface font-mono">{{ number_format($transaction->commission_fee, 0) }}đ</p>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Người bán nhận</p>
                <p class="text-sm font-black text-on-surface font-mono">{{ number_format($transaction->seller_amount, 0) }}đ</p>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Ngày thanh toán</p>
                <p class="text-sm font-black text-on-surface">{{ $transaction->paid_at ? $transaction->paid_at->format('d/m/Y H:i') : '—' }}</p>
            </div>
        </div>
    </div>

    @if($transaction->order)
    <div class="card-premium space-y-4">
        <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Đơn hàng liên quan</h3>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
                <div>
                    <p class="text-sm font-black text-on-surface font-mono">#{{ substr($transaction->order->uuid, 0, 8) }}</p>
                    <p class="text-xs text-on-surface-variant">{{ $transaction->order->product?->name ?? 'N/A' }}</p>
                </div>
            </div>
            <span class="text-sm font-black text-on-surface font-mono">{{ number_format($transaction->order->total_amount, 0) }}đ</span>
        </div>
    </div>
    @endif
</div>
@endsection
