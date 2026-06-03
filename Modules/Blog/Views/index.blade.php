@extends('layouts.portfolio')

@section('title', 'Góc nhìn kỹ thuật | Orchestrix')

@section('content')
    <div class="bg-background pt-48 pb-32">
        <div class="max-w-7xl mx-auto px-6">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row justify-between items-end gap-8 mb-24">
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="h-[1px] w-12 bg-brand"></div>
                        <span
                            class="text-[10px] font-mono font-bold tracking-[0.4em] text-brand uppercase">Knowledge_Archive
                            // v2.6</span>
                    </div>
                    <h1 class="text-[48px] md:text-[80px] font-display font-medium tracking-tight leading-[1.0] text-text">
                        Góc nhìn <br /> kỹ thuật.
                    </h1>
                    <p class="text-text-muted text-[18px] max-w-xl leading-relaxed">
                        Khám phá kho kiến thức về kiến trúc phần mềm, hệ thống phân tán
                        và những trải nghiệm thực chiến trong thế giới công nghệ.
                    </p>
                </div>
            </div>

            {{-- Categories Filter --}}
            <div class="flex flex-wrap items-center gap-4 mb-20">
                <a href="{{ route('blog.index') }}"
                    class="px-6 py-2 rounded-full text-[14px] font-bold transition-all {{ request()->is('blog') ? 'bg-brand text-background' : 'bg-surface-muted text-text-muted hover:text-brand border border-border' }}">
                    Tất cả bài viết
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}"
                        class="px-6 py-2 rounded-full text-[14px] font-bold transition-all {{ request()->is('blog/' . $cat->slug) ? 'bg-brand text-background' : 'bg-surface-muted text-text-muted hover:text-brand border border-border' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>

            {{-- Post Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <article
                        class="group flex flex-col h-full bg-surface-secondary border border-border rounded-[24px] overflow-hidden hover:border-brand/40 transition-all duration-500 shadow-xl hover:shadow-brand/5">
                        <div class="relative h-[240px] overflow-hidden">
                            <img src="{{ $post->thumbnail ? asset('storage/' . $post->thumbnail) : 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&q=80&w=1200' }}"
                                alt="{{ $post->title }}"
                                class="w-full h-full object-cover grayscale opacity-50 group-hover:grayscale-0 group-hover:scale-105 group-hover:opacity-100 transition-all duration-700">
                            <div
                                class="absolute top-4 left-4 bg-background/80 backdrop-blur-md px-3 py-1 rounded-full border border-border text-[9px] font-bold text-brand uppercase tracking-widest">
                                {{ $post->category?->name ?? 'Project' }}
                            </div>
                        </div>

                        <div class="p-8 flex flex-col flex-1 space-y-6">
                            <div class="space-y-4 flex-1">
                                <span
                                    class="text-[12px] font-mono text-text-muted/60">{{ $post->published_at_formatted }}</span>
                                <h3
                                    class="text-[24px] font-display font-medium text-text group-hover:text-brand transition-colors leading-[1.2]">
                                    {{ $post->title }}
                                </h3>
                                <p class="text-[16px] text-text-muted leading-relaxed line-clamp-3">
                                    {{ $post->excerpt }}
                                </p>
                            </div>

                            <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}"
                                class="pt-6 border-t border-border flex justify-between items-center group/btn">
                                <span
                                    class="text-[12px] font-bold uppercase tracking-widest text-text group-hover/btn:text-brand transition-colors">Đọc
                                    chi tiết</span>
                                <span
                                    class="material-symbols-outlined text-[18px] text-text-muted group-hover/btn:text-brand group-hover/btn:translate-x-1 transition-all">north_east</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-24 py-12 border-t border-border flex justify-center">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection