@php
    $catColor = $post->category->color ?? 'var(--primary)';
    $large = $large ?? false;
@endphp
<article
    class="relative group cursor-pointer overflow-hidden rounded-xl bg-surface-container-low border border-outline-variant/10 glowing-border transition-all duration-700 hover:shadow-2xl hover:shadow-primary/10"
    onclick="window.location='{{ route('blog.show', [$post->category->slug, $post->slug]) }}'">

    {{-- Background Image / Pattern --}}
    <div class="absolute inset-0 z-0">
        @if($post->thumbnail)
            <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}"
                class="w-full h-full object-cover opacity-60 transition-transform duration-1000 group-hover:scale-105 group-hover:opacity-40">
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-surface-container-high to-surface">
                <div class="absolute inset-0 opacity-10"
                    style="background-image: radial-gradient({{ $catColor }} 1px, transparent 1px); background-size: 30px 30px;">
                </div>
            </div>
        @endif
        {{-- High-end overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-surface via-surface/60 to-transparent"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 p-8 md:p-12 h-full flex flex-col justify-end min-h-[360px]">

        {{-- Badges --}}
        <div class="flex items-center gap-3 mb-6">
            <span
                class="bg-surface-container-highest/80 backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-label font-bold uppercase tracking-widest text-primary border border-primary/20">
                {{ $post->category->name }}
            </span>
            @if($post->is_featured)
                <span
                    class="bg-amber-400/20 backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-label font-bold uppercase tracking-widest text-amber-400 border border-amber-400/20 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[12px]">star</span>
                    Featured
                </span>
            @endif
        </div>

        {{-- Title & Excerpt --}}
        <div class="max-w-3xl space-y-4">
            <h3
                class="text-3xl md:text-5xl font-headline font-black text-on-surface leading-tight tracking-tighter group-hover:text-primary transition-colors duration-500">
                {{ $post->title }}
            </h3>

            @if($large && $post->excerpt)
                <p class="text-on-surface-variant/80 font-light leading-relaxed line-clamp-2 max-w-2xl text-lg italic">
                    "{{ $post->excerpt }}"
                </p>
            @endif
        </div>

        {{-- Meta Footer --}}
        <div
            class="mt-10 flex flex-wrap items-center gap-6 pt-6 border-t border-outline-variant/10 text-[10px] font-label uppercase tracking-[0.2em] text-on-surface-variant/50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px]">schedule</span>
                <span>{{ $post->reading_time }} MIN READ</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px]">visibility</span>
                <span>{{ number_format($post->views_count) }} VIEWS</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px]">favorite</span>
                <span>{{ $post->likes_count }} LIKES</span>
            </div>
            <div
                class="ml-auto flex items-center gap-2 text-primary font-bold opacity-0 group-hover:opacity-100 transition-all transform translate-x-[-10px] group-hover:translate-x-0">
                <span>READ FULL INSIGHT</span>
                <span class="material-symbols-outlined text-[18px]">trending_flat</span>
            </div>
        </div>
    </div>
</article>