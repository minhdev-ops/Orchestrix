@extends('agriverse::shop.layout')

@section('title', $product->name)

@section('content')
<section class="max-w-[1200px] mx-auto px-3 py-4">
    <div class="text-xs text-stone-400 mb-3 flex items-center gap-1 flex-wrap">
        <a href="{{ route('agriverse.shop.home') }}" class="hover:text-emerald-700">Trang chủ</a>
        <span class="text-stone-300">/</span>
        <a href="{{ route('agriverse.shop.products.index') }}" class="hover:text-emerald-700">Sản phẩm</a>
        @if($product->category)
        <span class="text-stone-300">/</span>
        <a href="{{ route('agriverse.shop.products.index', ['category' => $product->category]) }}" class="hover:text-emerald-700">{{ $product->category }}</a>
        @endif
        <span class="text-stone-300">/</span>
        <span class="text-stone-600">{{ mb_strlen($product->name) > 40 ? mb_substr($product->name, 0, 40) . '...' : $product->name }}</span>
    </div>

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-5">
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 overflow-hidden">
                <div class="aspect-square bg-gradient-to-br from-stone-50 to-stone-100 flex items-center justify-center relative">
                    <span class="text-[100px] text-stone-200 font-bold select-none">{{ strtoupper(mb_substr($product->name, 0, 1)) }}</span>
                    @if($product->compare_price && $product->compare_price > $product->price)
                        @php $discount = round((1 - $product->price / $product->compare_price) * 100); @endphp
                        <span class="absolute top-3 left-3 bg-gradient-to-r from-red-500 to-rose-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-sm">Giảm {{ $discount }}%</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-span-12 md:col-span-7 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h1 class="text-lg md:text-xl font-bold text-stone-800 leading-snug">{{ $product->name }}</h1>
                        @if($product->category)
                        <a href="{{ route('agriverse.shop.products.index', ['category' => $product->category]) }}" class="inline-block mt-1.5 text-[11px] font-medium text-emerald-700 bg-emerald-50 rounded-md px-2 py-0.5 hover:bg-emerald-100 transition-colors">{{ $product->category }}</a>
                        @endif
                    </div>
                    <form action="{{ route('agriverse.api.wishlist.toggle', (int)$product->id) }}" method="POST">
                        @csrf
                        <button class="w-9 h-9 rounded-lg border border-stone-200 flex items-center justify-center hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300 shrink-0 transition-all">
                            <span class="material-symbols-outlined text-base">favorite</span>
                        </button>
                    </form>
                </div>

                <div class="mt-4 p-4 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl">
                    <div class="flex items-baseline gap-2.5">
                        <span class="text-2xl md:text-3xl font-bold text-red-500">{{ number_format($product->price, 0) }}<span class="text-sm underline">₫</span></span>
                        @if($product->compare_price)
                            <span class="text-sm text-stone-400 line-through">{{ number_format($product->compare_price, 0) }}₫</span>
                            <span class="text-[11px] font-bold text-red-500 bg-red-100/80 rounded-md px-2 py-0.5">-{{ round((1 - $product->price / $product->compare_price) * 100) }}%</span>
                        @endif
                    </div>
                </div>

                <div class="mt-3 flex items-center gap-3 text-xs text-stone-500">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm text-stone-400">inventory_2</span>
                        @if($product->stock > 0)
                            <span class="text-emerald-700 font-semibold">Còn hàng</span>
                        @else
                            <span class="text-red-500 font-semibold">Hết hàng</span>
                        @endif
                    </div>
                    <div class="w-px h-3 bg-stone-200"></div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm text-stone-400">shopping_bag</span>
                        <span>Đã bán {{ $product->sold_count ?? 0 }}</span>
                    </div>
                </div>

                @if($product->description)
                <div class="mt-4 p-3 bg-stone-50 rounded-xl text-sm text-stone-600 leading-relaxed">{{ $product->description }}</div>
                @endif

                <div class="mt-5 pt-4 border-t border-stone-100">
                    <div class="flex items-center gap-2">
                        <form action="{{ route('agriverse.api.cart.add') }}" method="POST" class="flex items-center gap-2 flex-1">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="flex items-center border border-stone-300 rounded-lg overflow-hidden">
                                <button type="button" onclick="this.parentElement.querySelector('input[type=number]').stepDown()" class="w-9 h-10 flex items-center justify-center text-stone-500 hover:bg-stone-50 text-sm transition-colors">−</button>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock ?: 99 }}" readonly
                                    class="w-12 h-10 text-center text-sm font-semibold outline-none border-x border-stone-300 bg-white [appearance:textfield]">
                                <button type="button" onclick="this.parentElement.querySelector('input[type=number]').stepUp()" class="w-9 h-10 flex items-center justify-center text-stone-500 hover:bg-stone-50 text-sm transition-colors">+</button>
                            </div>
                            <button type="submit" class="flex-1 h-10 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center gap-1.5 hover:bg-emerald-700 disabled:opacity-50 transition-all shadow-sm">
                                <span class="material-symbols-outlined text-base">add_shopping_cart</span>
                                Thêm vào giỏ hàng
                            </button>
                        </form>
                        <form action="{{ route('agriverse.api.cart.buy-now', (int)$product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="h-10 px-5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold hover:bg-emerald-100 hover:shadow-sm transition-all whitespace-nowrap">Mua ngay</button>
                        </form>
                    </div>
                </div>

                @if($product->store)
                <div class="mt-4 p-4 rounded-xl bg-gradient-to-r from-emerald-50/50 to-white border border-emerald-100/80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-emerald-700 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-sm">{{ strtoupper(substr($product->store->name, 0, 2)) }}</div>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('agriverse.shop.stores.show', (int)$product->store->id) }}" class="text-sm font-semibold text-stone-800 hover:text-emerald-700 transition-colors">{{ $product->store->name }}</a>
                            <div class="text-[11px] text-stone-400">Xem gian hàng →</div>
                        </div>
                        <span class="material-symbols-outlined text-stone-300 text-lg">storefront</span>
                    </div>
                </div>
                @endif
            </div>

            @if($product->technical_specs && count($product->technical_specs) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5">
                <div class="text-sm font-bold text-stone-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-stone-500 text-lg">manufacturing</span>
                    Thông số kỹ thuật
                </div>
                <div class="grid grid-cols-2 gap-x-4 gap-y-0 text-xs rounded-xl overflow-hidden border border-stone-100">
                    @foreach($product->technical_specs as $key => $value)
                    <div class="flex items-center justify-between py-2.5 px-3 {{ $loop->even ? 'bg-stone-50' : 'bg-white' }}">
                        <span class="text-stone-500">{{ $key }}</span>
                        <span class="font-semibold text-stone-700">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($product->reviews->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5">
                <div class="text-sm font-bold text-stone-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-stone-500 text-lg">rate_review</span>
                    Đánh giá ({{ $product->reviews->count() }})
                </div>
                <div class="space-y-4">
                    @foreach($product->reviews as $review)
                    <div class="flex gap-3 pb-4 {{ !$loop->last ? 'border-b border-stone-100' : '' }}">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">{{ strtoupper(substr($review->user->name ?? 'N', 0, 2)) }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-stone-700">{{ $review->user->name ?? 'N/A' }}</span>
                                <span class="text-[10px] text-stone-400">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-0.5 mt-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined text-sm {{ $i <= $review->rating ? 'text-amber-400' : 'text-stone-200' }}">star</span>
                                @endfor
                            </div>
                            @if($review->comment)
                            <div class="text-xs text-stone-600 mt-1.5 leading-relaxed">{{ $review->comment }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($relatedProducts->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5">
                <div class="text-sm font-bold text-stone-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-stone-500 text-lg">currency_exchange</span>
                    Sản phẩm liên quan
                </div>
                <div class="grid grid-cols-6 md:grid-cols-12 gap-3">
                    @foreach($relatedProducts as $related)
                    <div class="col-span-6 md:col-span-3">
                        <div class="border border-stone-200 rounded-xl hover:border-emerald-200 hover:shadow-md transition-all bg-white group">
                            <a href="{{ route('agriverse.shop.products.show', (int)$related->id) }}">
                                <div class="aspect-square bg-stone-50 flex items-center justify-center rounded-t-xl overflow-hidden">
                                    <span class="text-3xl text-stone-200 font-bold group-hover:scale-110 transition-transform duration-300">{{ strtoupper(mb_substr($related->name, 0, 1)) }}</span>
                                </div>
                            </a>
                            <div class="p-2.5">
                                <a href="{{ route('agriverse.shop.products.show', (int)$related->id) }}" class="text-xs font-semibold text-stone-800 line-clamp-2 hover:text-emerald-700 leading-snug transition-colors">{{ $related->name }}</a>
                                <div class="text-sm font-bold text-red-500 mt-1.5">{{ number_format($related->price, 0) }}<span class="text-xs underline">₫</span></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection