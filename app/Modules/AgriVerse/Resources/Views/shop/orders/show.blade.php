@extends('agriverse::shop.layout')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<section class="max-w-[1200px] mx-auto px-3 py-4">
    <div class="text-xs text-stone-400 mb-3 flex items-center gap-1">
        <a href="{{ route('agriverse.shop.home') }}" class="hover:text-emerald-700">Trang chủ</a>
        <span class="text-stone-300">/</span>
        <a href="{{ route('agriverse.shop.orders.index') }}" class="hover:text-emerald-700">Đơn hàng</a>
        <span class="text-stone-300">/</span>
        <span class="text-stone-600">#{{ substr($order->uuid, 0, 8) }}</span>
    </div>

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-base font-bold text-stone-800">Đơn hàng #{{ substr($order->uuid, 0, 8) }}</h1>
            <div class="text-xs text-stone-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <span class="text-[11px] font-semibold px-3 py-1 rounded-lg
            @if(in_array($order->status, ['completed', 'delivered'])) bg-emerald-50 text-emerald-700
            @elseif($order->status === 'cancelled') bg-red-50 text-red-600
            @elseif($order->status === 'shipping') bg-blue-50 text-blue-700
            @else bg-amber-50 text-amber-700
            @endif
        ">{{ $order->status }}</span>
    </div>

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-8 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5">
                <div class="flex items-center gap-2 pb-3 border-b border-stone-100 mb-4">
                    <span class="material-symbols-outlined text-stone-500 text-lg">receipt_long</span>
                    <span class="text-sm font-bold text-stone-800">Chi tiết đơn hàng</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-stone-50 to-stone-100 flex items-center justify-center border border-stone-100 shadow-sm">
                        <span class="text-xl text-stone-300 font-bold">{{ strtoupper(mb_substr($order->product?->name ?? 'N', 0, 1)) }}</span>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-stone-800">{{ $order->product?->name ?? 'N/A' }}</div>
                        <div class="text-xs text-stone-400">{{ $order->store?->name ?? '' }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4 text-xs">
                    <div class="p-3 bg-stone-50 rounded-lg"><span class="text-stone-400">Số lượng:</span> <span class="font-semibold text-stone-700">{{ $order->quantity }}</span></div>
                    <div class="p-3 bg-stone-50 rounded-lg"><span class="text-stone-400">Đơn giá:</span> <span class="font-semibold text-stone-700">{{ number_format($order->unit_price, 0) }}₫</span></div>
                </div>
                @if($order->shipping_address)
                <div class="h-px bg-stone-100 my-4"></div>
                <div class="text-xs">
                    <div class="text-stone-500 font-semibold mb-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">location_on</span>
                        Địa chỉ giao hàng
                    </div>
                    <div class="text-stone-700">{{ $order->shipping_address }}</div>
                </div>
                @endif
                @if($order->notes)
                <div class="h-px bg-stone-100 my-4"></div>
                <div class="text-xs">
                    <div class="text-stone-500 font-semibold mb-1">Ghi chú</div>
                    <div class="text-stone-700">{{ $order->notes }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="col-span-12 md:col-span-4 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5 sticky top-20">
                <div class="flex items-center gap-2 pb-3 border-b border-stone-100 mb-3">
                    <span class="material-symbols-outlined text-stone-500 text-lg">payments</span>
                    <span class="text-sm font-bold text-stone-800">Tổng tiền</span>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Tạm tính</span>
                        <span class="font-semibold text-stone-700">{{ number_format($order->total_price, 0) }}₫</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-stone-500">Giảm giá</span>
                        <span class="font-semibold text-emerald-700">-{{ number_format($order->discount_amount, 0) }}₫</span>
                    </div>
                    @endif
                </div>
                <div class="h-px bg-stone-200 my-3"></div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-stone-800">Tổng cộng</span>
                    <span class="text-lg font-bold text-red-500">{{ number_format($order->total_amount, 0) }}<span class="text-xs underline">₫</span></span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5">
                <div class="flex items-center gap-2 pb-3 border-b border-stone-100 mb-3">
                    <span class="material-symbols-outlined text-stone-500 text-lg">timeline</span>
                    <span class="text-sm font-bold text-stone-800">Lịch sử đơn hàng</span>
                </div>
                <div class="space-y-3">
                    @forelse($order->statuses as $status)
                    <div class="flex gap-3 items-start">
                        <div class="flex flex-col items-center">
                            <div class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0 ring-2 ring-white
                                @if(in_array($status->status, ['completed', 'delivered'])) bg-emerald-500
                                @elseif($status->status === 'cancelled') bg-red-500
                                @elseif($status->status === 'shipping') bg-blue-500
                                @else bg-amber-500
                                @endif
                            "></div>
                            @if(!$loop->last)<div class="w-px h-6 bg-stone-200 mt-0.5"></div>@endif
                        </div>
                        <div class="min-w-0 pb-3 {{ !$loop->last ? '' : '' }}">
                            <div class="text-xs font-semibold text-stone-700">{{ $status->status }}</div>
                            @if($status->note)<div class="text-[10px] text-stone-400 mt-0.5">{{ $status->note }}</div>@endif
                            <div class="text-[10px] text-stone-400 mt-0.5">{{ $status->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-xs text-stone-400 text-center py-2">Chưa có cập nhật.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection