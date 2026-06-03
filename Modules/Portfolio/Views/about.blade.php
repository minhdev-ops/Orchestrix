@extends('layouts.portfolio')

@section('title', 'About | Orchestrix')

@section('content')
    <div class="pt-20">
        @include('portfolio::sections.about')

        {{-- Stats and Bio handled by @include --}}
        @include('portfolio::sections.about')

        {{-- Professional Timeline Section --}}
        @if(isset($experiences) && $experiences->count() > 0)
            <section class="py-32 bg-surface">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-12">
                        <div class="md:col-span-4 space-y-4">
                            <div class="text-xs font-bold tracking-widest text-primary uppercase">Career Path</div>
                            <h2 class="text-4xl font-black text-on-surface tracking-tight uppercase">Professional <span class="text-primary italic">Timeline.</span></h2>
                            <p class="text-on-surface-variant font-light leading-relaxed">A curated collection of my journey through various technological landscapes.</p>
                        </div>
                        <div class="md:col-start-6 md:col-span-7 space-y-12 relative border-l border-outline-variant/20 pl-8 ml-4">
                            @foreach($experiences as $exp)
                                <div class="relative">
                                    {{-- Dot --}}
                                    <div class="absolute -left-[41px] top-1.5 w-4 h-4 rounded-full {{ $exp->is_current ? 'bg-primary shadow-lg shadow-primary/40' : 'bg-surface-container-high border-2 border-outline-variant/20' }}"></div>
                                    <div class="space-y-2">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="text-xs font-black text-primary uppercase tracking-widest">{{ $exp->start_date }} — {{ $exp->is_current ? 'PRESENT' : $exp->end_date }}</span>
                                            @if($exp->is_current)
                                                <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary text-[8px] font-black uppercase tracking-tighter">Current</span>
                                            @endif
                                        </div>
                                        <h3 class="text-2xl font-bold text-on-surface">{{ $exp->title }}</h3>
                                        <div class="text-sm font-bold text-on-surface-variant/70 uppercase tracking-widest">{{ $exp->organization }} • {{ $exp->location }}</div>
                                        @if($exp->description)
                                            <div class="text-on-surface-variant font-light leading-relaxed prose prose-invert max-w-none mt-4">
                                                {!! \Illuminate\Support\Str::markdown($exp->description) !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Detailed Info Grid --}}
        <section class="py-32 bg-surface-container-low/40 border-t border-outline-variant/10">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="space-y-6 group">
                        <div class="text-xs font-bold tracking-widest text-primary uppercase">Location</div>
                        <div class="text-xl font-medium text-on-surface italic">{{ $about->location ?? 'Global / Remote' }}</div>
                    </div>
                    <div class="space-y-6 group">
                        <div class="text-xs font-bold tracking-widest text-primary uppercase">Direct Channel</div>
                        <div class="text-xl font-medium text-on-surface italic underline underline-offset-8 decoration-primary/30">{{ $about->email ?? '' }}</div>
                    </div>
                    @if($about->resume_url)
                        <div class="space-y-6 group">
                            <div class="text-xs font-bold tracking-widest text-primary uppercase">Registry</div>
                            <a href="{{ $about->resume_url }}" target="_blank" class="inline-flex items-center gap-2 text-xl font-black text-on-surface hover:text-primary transition-colors uppercase tracking-tighter">
                                Download Portfolio RFC
                                <span class="material-symbols-outlined">download_for_offline</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection