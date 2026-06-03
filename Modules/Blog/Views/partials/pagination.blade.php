@if($paginator->hasPages())
    <nav class="flex items-center justify-center gap-2">
        {{-- Previous --}}
        @if($paginator->onFirstPage())
            <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed">
                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all">
                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </a>
        @endif

        {{-- Pages --}}
        @foreach($elements as $element)
            @if(is_string($element))
                <span class="w-9 h-9 flex items-center justify-center text-slate-400 text-sm">…</span>
            @endif
            @if(is_array($element))
                @foreach($element as $page => $url)
                    @if($page == $paginator->currentPage())
                        <span
                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-600 text-white font-semibold text-sm shadow-md shadow-blue-600/30">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all text-sm font-medium">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all">
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </a>
        @else
            <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed">
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </span>
        @endif
    </nav>
@endif