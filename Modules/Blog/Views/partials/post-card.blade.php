@php
    $catColor = $post->category->color ?? 'var(--primary)';
@endphp
<article
    class="group relative flex flex-col h-full bg-surface-container-low rounded-xl overflow-hidden border border-outline-variant/10 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/5">

    {{-- Image Wrap --}}
    <div class="relative aspect-[16/10] overflow-hidden">
        <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}" class="block h-full">
            @if($post->thumbnail)
                <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}"
                    class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
            @else
                <div
                    class="w-full h-full bg-surface-container-high flex flex-col items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10"
                        style="background-image: radial-gradient({{ $catColor }} 1px, transparent 1px); background-size: 20px 20px;">
                    </div>
                    <span
                        class="material-symbols-outlined text-4xl text-on-surface-variant/20">{{ $post->category->icon ?? 'article' }}</span>
                </div>
            @endif
        </a>

        {{-- Category Badge --}}
        <div class="absolute top-4 left-4 z-10">
            <a href="{{ route('blog.category', $post->category->slug) }}"
                class="bg-surface-container-highest/80 backdrop-blur-md px-3 py-1 rounded-full text-[9px] font-label font-bold uppercase tracking-widest text-primary border border-primary/20 hover:bg-primary hover:text-on-primary transition-all">
                {{ $post->category->name }}
            </a>
        </div>
    </div>

    {{-- Content --}}
    <div class="p-6 flex flex-col flex-grow">
        {{-- Meta Header --}}
        <div
            class="flex items-center gap-4 mb-4 text-[10px] font-label uppercase tracking-widest text-on-surface-variant/50">
            <time>{{ $post->published_at_formatted }}</time>
            <span>•</span>
            <span>{{ $post->reading_time }} min read</span>
        </div>

        {{-- Title --}}
        <h3
            class="text-lg font-headline font-bold text-on-surface leading-snug mb-3 group-hover:text-primary transition-colors line-clamp-2">
            <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}">{{ $post->title }}</a>
        </h3>

        {{-- Excerpt --}}
        @if($post->excerpt)
            <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2 mb-6 font-light">
                {{ $post->excerpt }}
            </p>
        @endif

        {{-- Stats Footer --}}
        <div class="mt-auto pt-6 border-t border-outline-variant/10 flex items-center justify-between">
            <div
                class="flex items-center gap-4 text-[10px] font-label uppercase tracking-widest text-on-surface-variant/40">
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                    <span>{{ number_format($post->views_count) }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">favorite</span>
                    <span>{{ $post->likes_count }}</span>
                </div>
            </div>

            <a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}"
                class="material-symbols-outlined text-primary text-[18px] opacity-0 group-hover:opacity-100 transition-all transform translate-x-[-10px] group-hover:translate-x-0">
                arrow_forward
            </a>
        </div>
    </div>

    {{-- Glowing hover line --}}
    <div class="absolute bottom-0 left-0 h-[1px] w-0 bg-primary group-hover:w-full transition-all duration-500"></div>
</article>