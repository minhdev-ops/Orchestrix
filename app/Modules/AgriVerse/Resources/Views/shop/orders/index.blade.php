@extends('agriverse::shop.layout')

@section('title', 'Đơn hàng')

@section('content')
<section class="max-w-[1200px] mx-auto px-3 py-4">
    <div class="text-xs text-stone-400 mb-3 flex items-center gap-1">
        <a href="{{ route('agriverse.shop.home') }}" class="hover:text-emerald-700">Trang chủ</a>
        <span class="text-stone-300">/</span>
        <span class="text-stone-600">Đơn hàng</span>
    </div>

    <h1 class="text-base font-bold text-stone-800 mb-4">Đơn hàng của tôi</h1>

    @if(isset($orders) && $orders->count() > 0)
    <div class="space-y-3">
        @foreach($orders as $order)
        <a href="{{ route('agriverse.shop.orders.show', $order->id) }}" class="block bg-white rounded-xl shadow-sm border border-stone-200/80 p-4 hover:border-emerald-200 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-stone-50 to-stone-100 flex items-center justify-center shrink-0 border border-stone-100">
                        <span class="material-symbols-outlined text-base text-stone-400">receipt_long</span>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-stone-800 group-hover:text-emerald-700 transition-colors">Đơn hàng #{{ substr($order->uuid, 0, 8) }}</div>
                        <div class="text-xs text-stone-400 mt-0.5">{{ $order->product?->name ?? 'N/A' }} · {{ $order->quantity }} sản phẩm</div>
                        <div class="text-[10px] text-stone-400">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-sm font-bold text-red-500">{{ number_format($order->total_amount, 0) }}<span class="text-xs underline">₫</span></div>
                    <div class="mt-1.5">
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md
                            @if(in_array($order->status, ['completed', 'delivered'])) bg-emerald-50 text-emerald-700
                            @elseif($order->status === 'cancelled') bg-red-50 text-red-600
                            @elseif($order->status === 'shipping') bg-blue-50 text-blue-700
                            @else bg-amber-50 text-amber-700
                            @endif
                        ">{{ $order->status }}</span>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl text-stone-300">receipt_long</span>
        </div>
        <div class="text-sm font-semibold text-stone-600 mt-4">Chưa có đơn hàng</div>
        <div class="text-xs text-stone-400 mt-1">Bắt đầu mua sắm để có đơn hàng đầu tiên.</div>
        <a href="{{ route('agriverse.shop.products.index') }}" class="inline-block mt-5 h-9 px-5 rounded-lg bg-emerald-600 text-white text-xs font-bold leading-9 hover:bg-emerald-700 transition-all shadow-sm">Mua sắm ngay</a>
    </div>
    @endif
</section>
@endsection