@props(['post', 'large' => false])

<a href="{{ route('blog.show', [$post->category->slug, $post->slug]) }}"
    class="group relative block bg-surface-container-low/40 rounded-[40px] border border-outline-variant/10 overflow-hidden transition-all duration-700 hover:border-primary/40 hover:shadow-2xl {{ $large ? 'h-[500px]' : 'h-[460px]' }}">

    {{-- Decorative Background Glow --}}
    <div
        class="absolute -right-24 -top-24 w-64 h-64 bg-primary/5 rounded-full blur-[80px] group-hover:bg-primary/10 transition-all duration-1000">
    </div>

    <div class="flex flex-col h-full">
        {{-- Image Section --}}
        <div class="relative {{ $large ? 'h-3/5' : 'h-1/2' }} overflow-hidden">
            <img src="{{ $post->image ? asset('storage/' . $post->image) : 'https://ui-avatars.com/api/?name=' . urlencode($post->title) . '&background=0F172A&color=4cd7f6&size=800&bold=true' }}"
                class="w-full h-full object-cover grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-1000">

            <div
                class="absolute inset-0 bg-gradient-to-t from-surface-container-low via-transparent to-transparent opacity-80">
            </div>

            {{-- Technical Category Label --}}
            <div class="absolute top-6 right-6">
                <span
                    class="bg-surface/80 backdrop-blur-md px-4 py-1.5 rounded-full text-[8px] font-label font-black uppercase tracking-[0.3em] text-primary border border-outline-variant/20 shadow-2xl">
                    {{ strtoupper($post->category->name) }}
                </span>
            </div>

            {{-- Scanning effect on hover --}}
            <div
                class="absolute inset-x-0 h-px bg-primary/20 shadow-[0_0_15px_rgba(76,215,246,0.2)] animate-[scan_6s_linear_infinite] pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
        </div>

        {{-- Content Section --}}
        <div class="p-10 flex flex-col flex-grow relative z-10">
            <div class="flex items-center gap-4 mb-6">
                <span
                    class="w-8 h-px bg-primary/20 group-hover:w-16 group-hover:bg-primary transition-all duration-700"></span>
                <span
                    class="text-[9px] font-label font-black uppercase tracking-[0.4em] text-on-surface-variant group-hover:text-primary transition-colors italic">LOG_v{{ substr($post->id, 0, 4) }}</span>
            </div>

            <h3
                class="{{ $large ? 'text-4xl' : 'text-2xl' }} font-headline font-black text-on-surface group-hover:translate-x-2 transition-transform duration-500 tracking-tighter uppercase leading-[1.1] mb-6">
                {{ $post->title }}
            </h3>

            @if($large)
                <p class="text-on-surface-variant/60 line-clamp-2 leading-relaxed font-light mb-8 italic">
                    {{ $post->description }}
                </p>
            @endif

            <div
                class="mt-auto flex justify-between items-center bg-surface-container-high/40 p-4 -mx-10 -mb-10 border-t border-outline-variant/5">
                <div class="flex items-center gap-6 px-4">
                    <div class="flex flex-col">
                        <span
                            class="text-[7px] font-label font-black text-on-surface-variant/20 uppercase tracking-[0.4em]">EPOCH</span>
                        <span
                            class="text-[10px] font-label font-bold text-on-surface-variant/60 uppercase tracking-widest tabular-nums">{{ $post->published_at?->format('Hms:d:m:y') ?: $post->created_at->format('Hms:d:m:y') }}</span>
                    </div>
                    <div class="w-px h-6 bg-outline-variant/10"></div>
                    <div class="flex flex-col">
                        <span
                            class="text-[7px] font-label font-black text-on-surface-variant/20 uppercase tracking-[0.4em]">METRIC</span>
                        <span
                            class="text-[10px] font-label font-bold text-on-surface-variant/60 uppercase tracking-widest tabular-nums">{{ number_format($post->views_count ?? 0) }}v</span>
                    </div>
                </div>

                <div
                    class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-on-primary transition-all shadow-xl mr-2">
                    <span class="material-symbols-outlined text-[20px]">trending_flat</span>
                </div>
            </div>
        </div>
    </div>
</a>

<style>
    @keyframes scan {
        0% {
            transform: translateY(0);
            opacity: 0;
        }

        50% {
            opacity: 0.4;
        }

        100% {
            transform: translateY(500px);
            opacity: 0;
        }
    }
</style>