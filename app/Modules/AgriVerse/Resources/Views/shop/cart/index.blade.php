@extends('agriverse::shop.layout')

@section('title', 'Giỏ hàng')

@section('content')
<section class="max-w-[1200px] mx-auto px-3 py-4">
    <div class="text-xs text-stone-400 mb-3 flex items-center gap-1">
        <a href="{{ route('agriverse.shop.home') }}" class="hover:text-emerald-700">Trang chủ</a>
        <span class="text-stone-300">/</span>
        <span class="text-stone-600">Giỏ hàng</span>
    </div>

    <h1 class="text-base font-bold text-stone-800 mb-4">Giỏ hàng</h1>

    @if(isset($cartItems) && $cartItems->count() > 0)
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-8 space-y-3">
            @foreach($cartItems as $item)
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-4 flex items-center gap-4">
                <a href="{{ route('agriverse.shop.products.show', $item->product->id) }}" class="w-16 h-16 rounded-xl bg-gradient-to-br from-stone-50 to-stone-100 flex items-center justify-center shrink-0 border border-stone-100 shadow-sm">
                    <span class="text-2xl text-stone-300 font-bold">{{ strtoupper(mb_substr($item->product->name, 0, 1)) }}</span>
                </a>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('agriverse.shop.products.show', $item->product->id) }}" class="text-sm font-semibold text-stone-800 hover:text-emerald-700 line-clamp-1 transition-colors">{{ $item->product->name }}</a>
                    <div class="flex items-center gap-3 mt-2">
                        <form action="{{ route('agriverse.api.cart.update', $item->id) }}" method="POST" class="flex items-center border border-stone-300 rounded-lg overflow-hidden">
                            @csrf
                            @method('PUT')
                            <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" class="w-8 h-9 flex items-center justify-center text-stone-500 hover:bg-stone-50 text-sm transition-colors">−</button>
                            <span class="w-9 h-9 flex items-center justify-center text-sm font-semibold border-x border-stone-300 bg-white">{{ $item->quantity }}</span>
                            <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="w-8 h-9 flex items-center justify-center text-stone-500 hover:bg-stone-50 text-sm transition-colors">+</button>
                        </form>
                        <span class="text-xs text-stone-500">{{ number_format($item->product->price, 0) }}₫ / cái</span>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-base font-bold text-red-500">{{ number_format($item->product->price * $item->quantity, 0) }}<span class="text-xs underline">₫</span></div>
                    <form action="{{ route('agriverse.api.cart.remove', $item->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="text-[10px] text-stone-400 hover:text-red-500 hover:font-semibold mt-1.5 transition-colors">Xoá</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="col-span-12 md:col-span-4">
            @php $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity); @endphp
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5 sticky top-20">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-stone-100">
                    <span class="material-symbols-outlined text-stone-500 text-lg">receipt_long</span>
                    <span class="text-sm font-bold text-stone-800">Tổng tiền</span>
                </div>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Tạm tính ({{ $cartItems->sum('quantity') }} sản phẩm)</span>
                        <span class="font-semibold text-stone-700">{{ number_format($subtotal, 0) }}₫</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Phí vận chuyển</span>
                        <span class="text-emerald-700 font-semibold text-[11px] flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">check_circle</span>
                            Miễn phí
                        </span>
                    </div>
                    <div class="h-px bg-stone-200"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-stone-800">Tổng cộng</span>
                        <span class="text-lg font-bold text-red-500">{{ number_format($subtotal, 0) }}<span class="text-xs underline">₫</span></span>
                    </div>
                </div>
                <a href="{{ route('agriverse.shop.checkout.index') }}" class="block w-full h-10 rounded-lg bg-emerald-600 text-white text-sm font-bold flex items-center justify-center gap-1.5 hover:bg-emerald-700 hover:shadow-md transition-all mt-5 shadow-sm">
                    Thanh toán
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </a>
                <a href="{{ route('agriverse.shop.products.index') }}" class="block w-full text-center text-xs text-stone-400 mt-3 hover:text-emerald-700 transition-colors">Tiếp tục mua sắm</a>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl text-stone-300">shopping_cart</span>
        </div>
        <div class="text-sm font-semibold text-stone-600 mt-4">Giỏ hàng trống</div>
        <div class="text-xs text-stone-400 mt-1">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm.</div>
        <a href="{{ route('agriverse.shop.products.index') }}" class="inline-block mt-5 h-9 px-5 rounded-lg bg-emerald-600 text-white text-xs font-bold leading-9 hover:bg-emerald-700 transition-all shadow-sm">Mua sắm ngay</a>
    </div>
    @endif
</section>
@endsection