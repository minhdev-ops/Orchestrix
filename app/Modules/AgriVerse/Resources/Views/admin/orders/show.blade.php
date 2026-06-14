@extends('layouts.admin')

@section('page-title', 'AgriVerse — Chi tiết Đơn hàng')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-20">
    <div>
        <a href="{{ route('admin.agriverse.orders.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-on-surface-variant hover:text-primary transition-colors mb-4">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại danh sách
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Đơn hàng <span class="text-primary font-mono">#{{ substr($order->uuid, 0, 8) }}</span></h2>
                <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Chi tiết đơn hàng và lịch sử trạng thái.</p>
            </div>
            <span class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-full border
                @if(in_array($order->status, ['completed', 'delivered'])) bg-emerald-50 text-emerald-600 border-emerald-200/60
                @elseif($order->status === 'cancelled') bg-red-50 text-red-600 border-red-200/60
                @elseif($order->status === 'confirmed') bg-blue-50 text-blue-600 border-blue-200/60
                @elseif($order->status === 'shipping') bg-indigo-50 text-indigo-600 border-indigo-200/60
                @else bg-amber-50 text-amber-600 border-amber-200/60
                @endif
            ">
                {{ $order->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6">
        {{-- Order Info --}}
        <div class="col-span-12 md:col-span-8 space-y-6">
            <div class="card-premium space-y-5">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Thông tin đơn hàng</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Mã đơn hàng</p>
                        <p class="text-sm font-black text-on-surface font-mono">{{ $order->uuid }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Ngày tạo</p>
                        <p class="text-sm font-black text-on-surface">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Số lượng</p>
                        <p class="text-sm font-black text-on-surface font-mono">{{ $order->quantity }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Đơn giá</p>
                        <p class="text-sm font-black text-on-surface font-mono">{{ number_format($order->unit_price, 0) }}đ</p>
                    </div>
                </div>
                <div class="h-px bg-outline-variant/30"></div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-on-surface-variant">Tạm tính</span>
                        <span class="text-sm font-bold font-mono">{{ number_format($order->total_price, 0) }}đ</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-sm text-on-surface-variant">Giảm giá</span>
                        <span class="text-sm font-bold text-emerald-600 font-mono">-{{ number_format($order->discount_amount, 0) }}đ</span>
                    </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t border-outline-variant/30">
                        <span class="text-base font-black text-on-surface">Tổng cộng</span>
                        <span class="text-xl font-black text-on-surface font-mono">{{ number_format($order->total_amount, 0) }}đ</span>
                    </div>
                </div>
            </div>

            {{-- Buyer Info --}}
            <div class="card-premium space-y-4">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Thông tin người mua</h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black">
                        {{ strtoupper(substr($order->buyer?->name ?? 'N', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-base font-black text-on-surface">{{ $order->buyer?->name ?? 'N/A' }}</p>
                        <p class="text-sm text-on-surface-variant">{{ $order->buyer?->email ?? '' }}</p>
                    </div>
                </div>
                @if($order->shipping_address)
                <div class="h-px bg-outline-variant/30"></div>
                <div>
                    <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest mb-1">Địa chỉ giao hàng</p>
                    <p class="text-sm text-on-surface">{{ $order->shipping_address }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-span-12 md:col-span-4 space-y-6">
            {{-- Update Status --}}
            <div class="card-premium space-y-4">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Cập nhật trạng thái</h3>
                <form action="{{ route('admin.agriverse.orders.update-status', $order->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <select name="status" class="w-full px-4 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>Shipping</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <textarea name="note" rows="2" placeholder="Ghi chú (không bắt buộc)..." class="w-full px-4 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30"></textarea>
                    <button type="submit" class="w-full px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Cập nhật
                    </button>
                </form>
            </div>

            {{-- Status History --}}
            <div class="card-premium space-y-4">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Lịch sử trạng thái</h3>
                <div class="space-y-3">
                    @forelse($order->statuses as $status)
                    <div class="flex gap-3 items-start">
                        <div class="w-2 h-2 rounded-full mt-2
                            @if(in_array($status->status, ['completed', 'delivered'])) bg-emerald-500
                            @elseif($status->status === 'cancelled') bg-red-500
                            @elseif($status->status === 'confirmed') bg-blue-500
                            @elseif($status->status === 'shipping') bg-indigo-500
                            @else bg-amber-500
                            @endif
                        "></div>
                        <div class="flex-1">
                            <p class="text-xs font-black uppercase tracking-widest text-on-surface">{{ $status->status }}</p>
                            @if($status->note)<p class="text-xs text-on-surface-variant mt-0.5">{{ $status->note }}</p>@endif
                            <p class="text-[9px] text-on-surface-variant opacity-50 mt-0.5">{{ $status->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-on-surface-variant opacity-60">Chưa có cập nhật.</p>
                    @endforelse
                </div>
            </div>

            {{-- Product Info --}}
            @if($order->product)
            <div class="card-premium space-y-4">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Sản phẩm</h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-black">
                        {{ strtoupper(substr($order->product->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-black text-on-surface">{{ $order->product->name }}</p>
                        <p class="text-xs text-on-surface-variant">{{ number_format($order->product->price, 0) }}đ</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
