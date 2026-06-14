@extends('layouts.admin')

@section('page-title', 'Chi tiết Đơn hàng #' . $order->id)

@section('content')
<div class="space-y-10 pb-20 max-w-4xl">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.orders.index') }}" class="text-on-surface-variant hover:text-on-surface transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Đơn hàng #{{ $order->id }}</h2>
    </div>

    @if(session('success'))
        <div class="p-4 bg-tertiary/10 border border-tertiary/20 text-tertiary rounded-2xl font-bold flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Order Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card-premium p-8 bg-white rounded-2xl shadow-xl shadow-black/[0.02]">
            <h3 class="text-sm font-black text-primary uppercase tracking-widest mb-6">Thông tin đơn hàng</h3>
            <dl class="space-y-4">
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">Mã đơn</dt>
                    <dd class="font-bold text-on-surface">#{{ $order->id }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">UUID</dt>
                    <dd class="font-mono text-xs text-on-surface">{{ $order->uuid }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">Sản phẩm</dt>
                    <dd class="font-bold text-on-surface">{{ $order->product?->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">Số lượng</dt>
                    <dd class="font-bold text-on-surface">{{ $order->quantity }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">Đơn giá</dt>
                    <dd class="font-bold text-on-surface">{{ number_format($order->unit_price, 0) }}đ</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">Thành tiền</dt>
                    <dd class="font-bold text-lg text-primary">{{ number_format($order->total_price, 0) }}đ</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">Phí hoa hồng</dt>
                    <dd class="font-bold text-on-surface">{{ number_format($order->commission_fee, 0) }}đ</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">Trạng thái</dt>
                    <dd>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                            @switch($order->status)
                                @case('pending') bg-yellow-100 text-yellow-800 @break
                                @case('confirmed') bg-blue-100 text-blue-800 @break
                                @case('awaiting_payment') bg-orange-100 text-orange-800 @break
                                @case('delivered') bg-purple-100 text-purple-800 @break
                                @case('completed') bg-green-100 text-green-800 @break
                                @case('cancelled') bg-red-100 text-red-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch">
                            {{ $order->status }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">Địa chỉ giao</dt>
                    <dd class="text-right text-on-surface">{{ $order->shipping_address ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">Ghi chú</dt>
                    <dd class="text-right text-on-surface">{{ $order->notes ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">Ngày tạo</dt>
                    <dd class="font-bold text-on-surface">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <div class="space-y-6">
            {{-- Buyer Info --}}
            <div class="card-premium p-8 bg-white rounded-2xl shadow-xl shadow-black/[0.02]">
                <h3 class="text-sm font-black text-primary uppercase tracking-widest mb-6">Người mua</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-on-surface-variant">Tên</dt>
                        <dd class="font-bold text-on-surface">{{ $order->buyer?->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-on-surface-variant">Email</dt>
                        <dd class="text-on-surface">{{ $order->buyer?->email ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Seller Info --}}
            <div class="card-premium p-8 bg-white rounded-2xl shadow-xl shadow-black/[0.02]">
                <h3 class="text-sm font-black text-primary uppercase tracking-widest mb-6">Người bán</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-on-surface-variant">Tên</dt>
                        <dd class="font-bold text-on-surface">{{ $order->seller?->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-on-surface-variant">Email</dt>
                        <dd class="text-on-surface">{{ $order->seller?->email ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    {{-- Status History --}}
    <div class="card-premium p-8 bg-white rounded-2xl shadow-xl shadow-black/[0.02]">
        <h3 class="text-sm font-black text-primary uppercase tracking-widest mb-6">Lịch sử trạng thái</h3>
        @if($order->statuses->count() > 0)
        <div class="space-y-4">
            @foreach($order->statuses as $status)
            <div class="flex items-start gap-4 pb-4 border-b border-outline-variant/10 last:border-0">
                <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm">circle</span>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-on-surface">{{ $status->status }}</p>
                    <p class="text-sm text-on-surface-variant">{{ $status->note }}</p>
                    <p class="text-xs text-on-surface-variant/60 mt-1">{{ $status->created_at->format('d/m/Y H:i') }} - {{ $status->user?->name ?? 'Hệ thống' }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-on-surface-variant opacity-50">Chưa có cập nhật nào.</p>
        @endif
    </div>

    {{-- Update Status --}}
    <div class="card-premium p-8 bg-white rounded-2xl shadow-xl shadow-black/[0.02]">
        <h3 class="text-sm font-black text-primary uppercase tracking-widest mb-6">Cập nhật trạng thái</h3>
        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex items-end gap-4">
            @csrf
            @method('PUT')
            <div class="flex-1">
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="flex-[2]">
                <input type="text" name="note" placeholder="Ghi chú (không bắt buộc)" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none">
            </div>
            <button type="submit" class="px-6 py-3 bg-primary text-on-primary rounded-xl text-xs font-black uppercase tracking-wider hover:bg-primary/90 transition-colors">
                Cập nhật
            </button>
        </form>
    </div>
</div>
@endsection
