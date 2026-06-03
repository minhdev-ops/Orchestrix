@extends('layouts.portfolio')

@section('title', $project->title . ' | Hồ sơ dự án')

@section('content')
    <div class="bg-background pt-48 pb-32 relative overflow-hidden">
        <main class="max-w-7xl mx-auto px-6 relative z-10">
            {{-- Header --}}
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-20 gap-8">
                <div class="space-y-6">
                    <a href="{{ route('portfolio.projects.index') }}"
                        class="group flex items-center gap-4 text-brand font-bold text-[10px] uppercase tracking-[0.3em] active:scale-95 transition-transform">
                        <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        Quay lại danh mục
                    </a>
                    <h1 class="text-[40px] md:text-[80px] font-display font-medium text-text tracking-tight leading-none">
                        {{ $project->title }}<span class="text-brand">.</span>
                    </h1>
                </div>
                <div class="px-8 py-3 bg-surface-secondary border border-border rounded-full flex flex-col items-end gap-1">
                    <span class="text-[9px] font-mono font-bold text-text-muted opacity-40 uppercase tracking-widest">Phân loại dự án</span>
                    <span class="text-[14px] font-bold text-brand uppercase">{{ $project->category }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- Primary Visual --}}
                <div class="lg:col-span-12 relative rounded-[32px] overflow-hidden border border-border aspect-[21/9]">
                    <img src="{{ $project->image ? asset('storage/' . $project->image) : 'https://images.unsplash.com/photo-1614850523296-d8c1af93d400?auto=format&fit=crop&q=80&w=1200' }}"
                        class="w-full h-full object-cover grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-[2s]" 
                        alt="{{ $project->title }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent opacity-60"></div>
                </div>

                {{-- Sidebar: Specs --}}
                <aside class="lg:col-span-4 space-y-6">
                    <div class="bg-surface-secondary border border-border rounded-[24px] p-10 space-y-10">
                        <div class="space-y-6">
                            <h3 class="text-[10px] font-mono font-bold text-brand uppercase tracking-[0.4em]">Công nghệ sử dụng</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($project->tech_stack ?? [$project->category] as $tech)
                                    <div class="px-4 py-2 bg-surface-muted border border-border rounded-full text-[11px] font-bold text-text-muted uppercase tracking-widest hover:border-brand/40 transition-colors">
                                        {{ $tech }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-[10px] font-mono font-bold text-brand uppercase tracking-[0.4em]">Liên kết trực tiếp</h3>
                            @if($project->link)
                                <a href="{{ $project->link }}" target="_blank"
                                    class="group flex items-center justify-between bg-brand text-background p-5 rounded-[12px] hover:bg-brand-hover transition-all shadow-lg shadow-brand/10">
                                    <span class="text-[12px] font-bold uppercase tracking-widest">Truy cập dự án</span>
                                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">open_in_new</span>
                                </a>
                            @else
                                <div class="p-5 border border-border rounded-[12px] opacity-40 text-[10px] uppercase font-bold tracking-widest text-center">
                                    Hệ thống nội bộ / Bảo mật
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-surface-secondary border border-border rounded-[24px] p-10 space-y-6">
                        <h3 class="text-[10px] font-mono font-bold text-brand uppercase tracking-[0.4em]">Tóm tắt dự án</h3>
                        <p class="text-text-muted leading-relaxed text-[15px] font-light italic">
                            "{{ $project->description }}"
                        </p>
                    </div>
                </aside>

                {{-- Main Research / Content --}}
                <div class="lg:col-span-8 bg-surface-secondary border border-border rounded-[32px] p-12 md:p-16">
                    <article class="prose prose-invert max-w-none 
                                    prose-headings:font-display prose-headings:font-medium prose-headings:tracking-tight prose-headings:text-text
                                    prose-p:text-text-muted prose-p:text-[17px] prose-p:leading-relaxed
                                    prose-strong:text-brand prose-strong:font-bold
                                    prose-code:text-brand prose-code:bg-surface-muted prose-code:px-2 prose-code:py-0.5 prose-code:rounded
                                    prose-pre:bg-surface-secondary prose-pre:border prose-pre:border-border prose-pre:rounded-[12px]">
                        {!! \Illuminate\Support\Str::markdown($project->content_md ?? 'Initializing technical log...') !!}
                    </article>
                </div>
            </div>
        </main>
    </div>
@endsection