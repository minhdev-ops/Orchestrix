@extends('layouts.admin')

@section('page-title', 'Cấu hình Trang chủ')

@section('module-nav')
    @include('portfolio::admin.partials.sidebar')
@endsection

@section('content')
    <div class="max-w-4xl space-y-6 animate-fade-up">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.portfolio.index') }}"
                class="w-10 h-10 rounded-xl bg-surface-container-high flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <h2 class="text-2xl font-black text-on-surface uppercase tracking-tight">Cấu hình Trang chủ</h2>
        </div>

        <form action="{{ route('admin.portfolio.settings.home.update') }}" method="POST"
            class="bg-surface-container-low border border-outline-variant/10 rounded-2xl p-10 space-y-10">
            @csrf

            <div class="space-y-3">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant pl-1">Tiêu đề
                    chính (Main Heading)</label>
                <input type="text" name="hero_title" value="{{ $heroTitle }}"
                    class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-black text-2xl uppercase tracking-tighter focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                <p class="text-[10px] text-on-surface-variant/50 pl-1 italic">Gợi ý: Thu hút sự chú ý ngay lập tức bằng
                    slogan mạnh mẽ.</p>
            </div>

            <div class="space-y-3">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant pl-1">Tiêu đề
                    phụ (Tagline)</label>
                <input type="text" name="hero_subtitle" value="{{ $heroSubtitle }}"
                    class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-light text-lg focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
            </div>

            <div class="pt-6">
                <button type="submit"
                    class="bg-primary text-on-primary px-10 py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] shadow-xl shadow-primary/30 hover:opacity-90 active:scale-95 transition-all">
                    Lưu cấu hình
                </button>
            </div>
        </form>

        <div class="bg-primary/5 rounded-2xl p-8 border border-primary/10">
            <div class="flex gap-4">
                <span class="material-symbols-outlined text-primary">info</span>
                <div class="space-y-1">
                    <h5 class="font-black text-on-surface text-xs uppercase tracking-tight">Thông tin hiển thị</h5>
                    <p class="text-xs text-on-surface-variant leading-relaxed">Nội dung này xuất hiện tại đầu trang chủ
                        Portfolio. Đảm bảo súc tích để tối ưu hiệu ứng thị giác.</p>
                </div>
            </div>
        </div>
    </div>
@endsection