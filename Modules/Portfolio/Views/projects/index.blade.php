@extends('layouts.portfolio')

@section('title', 'Danh mục dự án | Orchestrix')

@section('content')
    <div class="bg-background pt-48 pb-32 relative overflow-hidden">
        {{-- Background Grid Effect --}}
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
            style="background-image: radial-gradient(#58a6ff 1px, transparent 1px); background-size: 40px 40px;"></div>

        <section class="max-w-7xl mx-auto px-6 relative z-10">
            {{-- Header --}}
            <div class="max-w-4xl mb-32 space-y-8">
                <div class="flex items-center gap-3">
                    <div class="h-[1px] w-12 bg-brand"></div>
                    <span class="text-[10px] font-mono font-bold tracking-[0.4em] text-brand uppercase">Project_Registry //
                        v1.0.4</span>
                </div>
                <h1 class="text-[48px] md:text-[80px] font-display font-medium tracking-tight leading-[1.0] text-text">
                    Kho lưu trữ <br /> dự án.
                </h1>
                <p
                    class="text-[18px] md:text-[20px] text-text-muted leading-relaxed font-light border-l-2 border-brand/20 pl-8 max-w-2xl">
                    Hệ thống lưu trữ tập trung các dự án kỹ thuật, hệ thống doanh nghiệp
                    và các thử nghiệm kiến trúc phần mềm tiêu biểu.
                </p>
            </div>

            {{-- Project Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($projects as $project)
                    <article
                        class="group bg-surface-secondary border border-border rounded-[32px] overflow-hidden hover:border-brand/40 transition-all duration-500 shadow-2xl flex flex-col">
                        <a href="{{ route('portfolio.projects.show', $project->slug) }}" class="block flex-1 flex flex-col">
                            <div class="relative aspect-[16/10] overflow-hidden bg-background">
                                <img src="{{ $project->image ? asset('storage/' . $project->image) : 'https://images.unsplash.com/photo-1614850523296-d8c1af93d400?auto=format&fit=crop&q=80&w=800' }}"
                                    alt="{{ $project->title }}"
                                    class="w-full h-full object-cover grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700">

                                <div class="absolute top-6 left-6">
                                    <div
                                        class="px-4 py-1 bg-background/80 backdrop-blur-md rounded-full text-[10px] font-bold text-brand uppercase tracking-widest border border-border">
                                        {{ $project->category ?? 'Kỹ thuật' }}
                                    </div>
                                </div>
                            </div>

                            <div class="p-10 flex flex-col flex-1 space-y-6">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-[10px] font-mono font-bold text-brand uppercase tracking-[0.4em]">{{ $project->client ?? 'Internal' }}</span>
                                    <span
                                        class="text-[10px] font-mono text-text-muted/40">[{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}]</span>
                                </div>

                                <h3
                                    class="text-[28px] font-display font-medium text-text group-hover:text-brand transition-colors tracking-tight leading-[1.1]">
                                    {{ $project->title }}
                                </h3>

                                <div class="pt-8 border-t border-border mt-auto flex items-center justify-between group/link">
                                    <span
                                        class="text-[12px] font-bold uppercase tracking-widest text-text group-hover/link:text-brand transition-colors">Xem
                                        chi tiết</span>
                                    <span
                                        class="material-symbols-outlined text-[20px] text-text-muted group-hover/link:text-brand group-hover/link:translate-x-1 transition-all">arrow_forward</span>
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-24 py-12 border-t border-border flex justify-center">
                {{ $projects->links() }}
            </div>
        </section>
    </div>
@endsection