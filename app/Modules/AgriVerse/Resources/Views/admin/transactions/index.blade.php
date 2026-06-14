@extends('layouts.admin')

@section('page-title', 'AgriVerse — Giao dịch')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Giao dịch</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Lịch sử giao dịch thanh toán trên nền tảng.</p>
        </div>
        <form method="GET" class="flex items-center gap-3">
            <select name="method" onchange="this.form.submit()" class="px-4 py-2.5 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="">Tất cả phương thức</option>
                <option value="transfer" {{ request('method') === 'transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Tiền mặt</option>
            </select>
            <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="">Tất cả trạng thái</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </form>
    </div>

    <div class="card-premium !p-0 overflow-hidden bg-white shadow-xl shadow-black/[0.02] rounded-3xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Mã GD</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Người dùng</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Số tiền</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Phương thức</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Trạng thái</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Ngày thanh toán</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-8 py-6">
                        <span class="font-mono text-xs font-bold text-on-surface">#{{ $tx->transaction_id }}</span>
                    </td>
                    <td class="px-8 py-6 text-sm font-bold text-on-surface">{{ $tx->user?->name ?? 'N/A' }}</td>
                    <td class="px-8 py-6">
                        <span class="font-bold font-mono text-on-surface">{{ number_format($tx->amount, 0) }}đ</span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">{{ $tx->payment_method ?? 'N/A' }}</span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full border
                            @if($tx->payment_status === 'paid') bg-emerald-50 text-emerald-600 border-emerald-200/60
                            @elseif($tx->payment_status === 'failed') bg-red-50 text-red-600 border-red-200/60
                            @else bg-amber-50 text-amber-600 border-amber-200/60
                            @endif
                        ">{{ $tx->payment_status ?? 'pending' }}</span>
                    </td>
                    <td class="px-8 py-6 text-sm text-on-surface-variant">{{ $tx->paid_at ? $tx->paid_at->format('d/m/Y') : '—' }}</td>
                    <td class="px-8 py-6 text-right">
                        <a href="{{ route('admin.agriverse.transactions.show', $tx->id) }}" class="p-2.5 rounded-xl text-on-surface-variant hover:text-primary hover:bg-primary/5 transition-all inline-block">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $transactions->links() }}</div>
</div>
@endsection
