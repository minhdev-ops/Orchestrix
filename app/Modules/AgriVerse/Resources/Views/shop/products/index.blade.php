@extends('agriverse::shop.layout')

@section('title', 'Sản phẩm')

@section('content')
<section class="max-w-[1200px] mx-auto px-3 py-4">
    <div class="text-xs text-stone-400 mb-3 flex items-center gap-1">
        <a href="{{ route('agriverse.shop.home') }}" class="hover:text-emerald-700">Trang chủ</a>
        <span class="text-stone-300">/</span>
        <span class="text-stone-600">Sản phẩm</span>
    </div>

    <div class="flex items-center justify-between mb-3">
        <h1 class="text-base font-bold text-stone-800">Sản phẩm <span class="font-normal text-stone-400">({{ $products->total() }})</span></h1>
        <select onchange="window.location.href=this.value" class="h-8 px-2.5 rounded-lg border border-stone-300 bg-white text-xs outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 transition-all">
            <option value="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" {{ request('sort') === 'latest' ? 'selected' : '' }}>Mới nhất</option>
            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá thấp → cao</option>
            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá cao → thấp</option>
        </select>
    </div>

    <div class="grid grid-cols-12 gap-4">
        <aside class="col-span-12 md:col-span-3">
            <form method="GET" action="{{ route('agriverse.shop.products.index') }}" class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-4 space-y-4">
                <div>
                    <div class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-2.5">Danh mục</div>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()" class="w-3.5 h-3.5 accent-emerald-600">
                            <span class="text-xs text-stone-600 group-hover:text-stone-800 transition-colors">Tất cả</span>
                        </label>
                        @foreach($categories as $cat)
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'checked' : '' }} onchange="this.form.submit()" class="w-3.5 h-3.5 accent-emerald-600">
                            <span class="text-xs text-stone-600 group-hover:text-stone-800 transition-colors">{{ $cat->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="h-px bg-stone-100"></div>
                <div>
                    <div class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-2.5">Khoảng giá</div>
                    <div class="flex items-center gap-1.5">
                        <input type="number" name="min_price" placeholder="Từ" value="{{ request('min_price') }}" class="w-full h-8 px-2 rounded-lg border border-stone-300 text-xs outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 transition-all">
                        <span class="text-stone-300 text-xs">—</span>
                        <input type="number" name="max_price" placeholder="Đến" value="{{ request('max_price') }}" class="w-full h-8 px-2 rounded-lg border border-stone-300 text-xs outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 transition-all">
                    </div>
                    <button type="submit" class="w-full mt-2.5 h-8 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-all shadow-sm">Áp dụng</button>
                </div>
            </form>
        </aside>

        <div class="col-span-12 md:col-span-9">
            @if($products->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-4">
                <div class="grid grid-cols-6 md:grid-cols-12 gap-3">
                    @foreach($products as $product)
                    <div class="col-span-6 md:col-span-4">
                        <div class="border border-stone-200 rounded-xl hover:border-emerald-200 hover:shadow-md transition-all bg-white group">
                            <a href="{{ route('agriverse.shop.products.show', $product->id) }}">
                                <div class="aspect-square bg-stone-50 flex items-center justify-center relative rounded-t-xl overflow-hidden">
                                    <span class="text-4xl text-stone-200 font-bold group-hover:scale-110 transition-transform duration-300">{{ strtoupper(mb_substr($product->name, 0, 1)) }}</span>
                                    @if($product->compare_price && $product->compare_price > $product->price)
                                        @php $discount = round((1 - $product->price / $product->compare_price) * 100); @endphp
                                        <span class="absolute top-2 left-2 bg-gradient-to-r from-red-500 to-rose-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-sm">{{ $discount }}%</span>
                                    @endif
                                </div>
                            </a>
                            <div class="p-2.5">
                                <a href="{{ route('agriverse.shop.products.show', $product->id) }}" class="text-xs font-semibold text-stone-800 line-clamp-2 hover:text-emerald-700 leading-snug min-h-[2rem] transition-colors">{{ $product->name }}</a>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-sm font-bold text-red-500">{{ number_format($product->price, 0) }}<span class="text-xs underline">₫</span></span>
                                    @if($product->compare_price)
                                        <span class="text-[10px] text-stone-300 line-through">{{ number_format($product->compare_price, 0) }}₫</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-stone-100">
                                    <span class="text-[10px] text-stone-400">Đã bán {{ $product->sold_count ?? 0 }}</span>
                                    <form action="{{ route('agriverse.api.cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="w-7 h-7 rounded-lg border border-stone-200 flex items-center justify-center text-stone-400 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300 transition-all">
                                            <span class="material-symbols-outlined text-sm">add_shopping_cart</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-3 border-t border-stone-100">{{ $products->links() }}</div>
            </div>
            @else
            <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-10 text-center">
                <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto">
                    <span class="text-3xl text-stone-300">!</span>
                </div>
                <div class="text-sm font-semibold text-stone-600 mt-4">Không tìm thấy sản phẩm</div>
                <div class="text-xs text-stone-400 mt-1">Thử thay đổi bộ lọc hoặc tìm kiếm từ khóa khác.</div>
                <a href="{{ route('agriverse.shop.products.index') }}" class="inline-block mt-5 h-9 px-5 rounded-lg bg-emerald-600 text-white text-xs font-bold leading-9 hover:bg-emerald-700 transition-all shadow-sm">Xem tất cả</a>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection