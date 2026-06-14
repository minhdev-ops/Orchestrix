@extends('agriverse::shop.layout')

@section('title', 'Yêu thích')

@section('content')
<section class="max-w-[1200px] mx-auto px-3 py-4">
    <div class="text-xs text-stone-400 mb-3 flex items-center gap-1">
        <a href="{{ route('agriverse.shop.home') }}" class="hover:text-emerald-700">Trang chủ</a>
        <span class="text-stone-300">/</span>
        <span class="text-stone-600">Yêu thích</span>
    </div>

    <h1 class="text-base font-bold text-stone-800 mb-4">Sản phẩm yêu thích</h1>

    @if(isset($wishlistItems) && $wishlistItems->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-4">
        <div class="grid grid-cols-6 md:grid-cols-12 gap-3">
            @foreach($wishlistItems as $item)
            <div class="col-span-6 md:col-span-3">
                <div class="border border-stone-200 rounded-xl hover:border-emerald-200 hover:shadow-md transition-all bg-white group">
                    <a href="{{ route('agriverse.shop.products.show', $item->product->id) }}">
                        <div class="aspect-square bg-stone-50 flex items-center justify-center rounded-t-xl overflow-hidden relative">
                            <span class="text-4xl text-stone-200 font-bold group-hover:scale-110 transition-transform duration-300">{{ strtoupper(mb_substr($item->product->name, 0, 1)) }}</span>
                            <span class="absolute top-2 right-2 w-6 h-6 rounded-full bg-red-50 flex items-center justify-center">
                                <span class="material-symbols-outlined text-sm text-red-500">favorite</span>
                            </span>
                        </div>
                    </a>
                    <div class="p-2.5">
                        <a href="{{ route('agriverse.shop.products.show', $item->product->id) }}" class="text-xs font-semibold text-stone-800 line-clamp-2 hover:text-emerald-700 leading-snug min-h-[2rem] transition-colors">{{ $item->product->name }}</a>
                        <div class="text-sm font-bold text-red-500 mt-1.5">{{ number_format($item->product->price, 0) }}<span class="text-xs underline">₫</span></div>
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-stone-100">
                            <form action="{{ route('agriverse.api.cart.add') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-full h-7 rounded-lg bg-emerald-600 text-white text-[10px] font-bold hover:bg-emerald-700 transition-all shadow-sm">Thêm vào giỏ</button>
                            </form>
                            <form action="{{ route('agriverse.api.wishlist.remove', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="w-7 h-7 rounded-lg border border-stone-200 flex items-center justify-center text-stone-300 hover:text-red-500 hover:border-red-200 transition-all">
                                    <span class="material-symbols-outlined text-xs">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl text-stone-300">favorite</span>
        </div>
        <div class="text-sm font-semibold text-stone-600 mt-4">Chưa có sản phẩm yêu thích</div>
        <div class="text-xs text-stone-400 mt-1">Hãy thêm sản phẩm vào danh sách yêu thích để theo dõi.</div>
        <a href="{{ route('agriverse.shop.products.index') }}" class="inline-block mt-5 h-9 px-5 rounded-lg bg-emerald-600 text-white text-xs font-bold leading-9 hover:bg-emerald-700 transition-all shadow-sm">Khám phá sản phẩm</a>
    </div>
    @endif
</section>
@endsection