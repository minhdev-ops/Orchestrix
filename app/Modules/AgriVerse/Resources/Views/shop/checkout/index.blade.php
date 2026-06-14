@extends('agriverse::shop.layout')

@section('title', 'Thanh toán')

@section('content')
<section class="max-w-[1200px] mx-auto px-3 py-4">
    <div class="text-xs text-stone-400 mb-3 flex items-center gap-1">
        <a href="{{ route('agriverse.shop.home') }}" class="hover:text-emerald-700">Trang chủ</a>
        <span class="text-stone-300">/</span>
        <span class="text-stone-600">Thanh toán</span>
    </div>

    <h1 class="text-base font-bold text-stone-800 mb-4">Thanh toán</h1>

    @if(isset($cartItems) && $cartItems->count() > 0)
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-7">
            <form action="{{ route('agriverse.api.checkout.process') }}" method="POST">
                @csrf
                <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-stone-100">
                        <span class="material-symbols-outlined text-stone-500 text-lg">location_on</span>
                        <span class="text-sm font-bold text-stone-800">Thông tin giao hàng</span>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-stone-500 mb-1.5 block">Địa chỉ nhận hàng</label>
                        <textarea name="shipping_address" rows="3" required
                            class="w-full px-3 py-2.5 rounded-lg border border-stone-300 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 transition-all bg-white">{{ old('shipping_address', auth()->user()->address ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-stone-500 mb-1.5 block">Ghi chú cho người bán</label>
                        <textarea name="notes" rows="2" placeholder="Ví dụ: Gọi điện trước khi giao..." class="w-full px-3 py-2.5 rounded-lg border border-stone-300 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 transition-all bg-white">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5 mt-4 space-y-3">
                    <div class="flex items-center gap-2 pb-3 border-b border-stone-100">
                        <span class="material-symbols-outlined text-stone-500 text-lg">confirmation_number</span>
                        <span class="text-sm font-bold text-stone-800">Mã giảm giá</span>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" name="coupon_code" placeholder="Nhập mã giảm giá" class="flex-1 h-9 px-3 rounded-lg border border-stone-300 text-xs outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 transition-all">
                        <button type="button" class="h-9 px-4 rounded-lg bg-stone-100 text-stone-600 text-xs font-semibold hover:bg-stone-200 transition-all">Áp dụng</button>
                    </div>
                </div>

                <button type="submit" class="w-full h-11 rounded-xl bg-emerald-600 text-white text-sm font-bold flex items-center justify-center gap-2 hover:bg-emerald-700 hover:shadow-md transition-all mt-4 shadow-sm">
                    <span class="material-symbols-outlined text-base">lock</span>
                    Xác nhận đặt hàng
                </button>
            </form>
        </div>

        <div class="col-span-12 md:col-span-5">
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5 sticky top-20">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-stone-100">
                    <span class="material-symbols-outlined text-stone-500 text-lg">receipt_long</span>
                    <span class="text-sm font-bold text-stone-800">Đơn hàng</span>
                </div>
                <div class="space-y-3">
                    @foreach($cartItems as $item)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-xs text-stone-800 truncate">{{ $item->product->name }}</span>
                            <span class="text-[10px] text-stone-400">x{{ $item->quantity }}</span>
                        </div>
                        <span class="text-xs font-semibold text-stone-700 shrink-0">{{ number_format($item->product->price * $item->quantity, 0) }}₫</span>
                    </div>
                    @endforeach
                </div>
                @php $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity); @endphp
                <div class="h-px bg-stone-200 my-3"></div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Tạm tính</span>
                        <span class="font-semibold text-stone-700">{{ number_format($subtotal, 0) }}₫</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Phí vận chuyển</span>
                        <span class="text-emerald-700 font-semibold text-[11px] flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">check_circle</span>
                            Miễn phí
                        </span>
                    </div>
                </div>
                <div class="h-px bg-stone-200 my-3"></div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-stone-800">Tổng cộng</span>
                    <span class="text-lg font-bold text-red-500">{{ number_format($subtotal, 0) }}<span class="text-xs underline">₫</span></span>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl text-stone-300">shopping_cart</span>
        </div>
        <div class="text-sm font-semibold text-stone-600 mt-4">Giỏ hàng trống</div>
        <div class="text-xs text-stone-400 mt-1">Vui lòng thêm sản phẩm vào giỏ trước khi thanh toán.</div>
        <a href="{{ route('agriverse.shop.products.index') }}" class="inline-block mt-5 h-9 px-5 rounded-lg bg-emerald-600 text-white text-xs font-bold leading-9 hover:bg-emerald-700 transition-all shadow-sm">Mua sắm ngay</a>
    </div>
    @endif
</section>
@endsection