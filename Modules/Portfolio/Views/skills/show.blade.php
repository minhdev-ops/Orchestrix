@extends('layouts.portfolio')

@section('title', $skill->name . ' | Technical Matrix')

@section('content')
    <div class="pt-20 min-h-screen bg-surface">
        <main class="max-w-7xl mx-auto px-6 py-24">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16 gap-8 animate-fade-up">
                <div class="space-y-4">
                    <a href="{{ route('portfolio.skills.index') }}"
                        class="group flex items-center gap-2 text-primary font-bold text-xs uppercase tracking-widest active:scale-95 transition-transform">
                        <span
                            class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_left_alt</span>
                        Back to Matrix
                    </a>
                    <h1 class="text-5xl md:text-8xl font-black text-on-surface tracking-tight leading-none uppercase">
                        {{ $skill->name }}<span class="text-primary italic">.</span>
                    </h1>
                </div>
                <div
                    class="bg-surface-container-high px-6 py-3 rounded-lg border border-outline-variant/10 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest shadow-sm">
                    Technical Core : {{ $skill->category }}
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                {{-- Sidebar --}}
                <aside class="lg:col-span-4 space-y-8 animate-fade-up" style="animation-delay: 0.1s;">
                    <div
                        class="bg-surface-container-low p-10 rounded-2xl border border-outline-variant/10 shadow-sm relative overflow-hidden group">
                        <div
                            class="aspect-square bg-surface rounded-xl flex items-center justify-center text-primary mb-10 border border-outline-variant/5 shadow-inner transition-transform group-hover:scale-105 duration-500">
                            <span
                                class="material-symbols-outlined text-[80px] group-hover:scale-110 transition-transform">{{ $skill->icon }}</span>
                        </div>
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <div class="flex justify-between items-end">
                                    <span
                                        class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Proficiency</span>
                                    <span class="text-3xl font-black text-primary">{{ $skill->level }}%</span>
                                </div>
                                <div class="h-2 w-full bg-surface-container-highest rounded-full overflow-hidden">
                                    <div class="h-full bg-primary rounded-full transition-all duration-1000 shadow-[0_0_15px_rgba(70,72,212,0.3)]"
                                        style="width: {{ $skill->level }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($skill->description)
                        <div class="bg-surface-container-high/20 p-8 rounded-2xl border border-outline-variant/10">
                            <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-4">Technical Brief</h3>
                            <p class="text-on-surface-variant leading-relaxed font-light italic">"{{ $skill->description }}"</p>
                        </div>
                    @endif
                </aside>

                {{-- Content --}}
                <div class="lg:col-span-8 animate-fade-up" style="animation-delay: 0.2s;">
                    <div
                        class="bg-surface-container-low/40 rounded-2xl p-10 md:p-16 border border-outline-variant/10 min-h-[500px] shadow-sm">
                        @if($skill->content_md)
                            <article
                                class="prose prose-slate dark:prose-invert max-w-none prose-headings:font-black prose-headings:tracking-tight prose-headings:text-on-surface prose-p:text-on-surface-variant prose-p:font-light prose-p:leading-relaxed">
                                {!! \Illuminate\Support\Str::markdown($skill->content_md) !!}
                            </article>
                        @else
                            <div class="h-[300px] flex flex-col items-center justify-center text-center space-y-6">
                                <div
                                    class="w-20 h-20 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant/20 border border-outline-variant/10">
                                    <span class="material-symbols-outlined text-4xl animate-pulse">engineering</span>
                                </div>
                                <div class="space-y-2">
                                    <h3 class="text-xl font-bold text-on-surface uppercase tracking-tight">Syncing
                                        Documentation...</h3>
                                    <p class="text-xs text-on-surface-variant/40 font-bold uppercase tracking-widest">
                                        Verification in progress.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection