<section id="projects" class="py-32 bg-surface">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div class="space-y-4">
                <div class="text-xs font-bold tracking-widest text-primary uppercase">Mission Archive</div>
                <h2 class="text-4xl md:text-5xl font-black text-on-surface tracking-tight">Technical Ventures</h2>
            </div>
            <a href="{{ route('portfolio.projects.index') }}"
                class="group flex items-center gap-2 text-primary font-bold text-sm tracking-tight active:scale-95 transition-transform">
                Browse Full Registry
                <span
                    class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_right_alt</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @foreach($projects as $project)
                <a href="{{ route('portfolio.projects.show', $project->slug) }}"
                    class="group block bg-surface-container-low rounded-2xl border border-outline-variant/10 overflow-hidden hover:border-primary/40 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <div class="aspect-video relative overflow-hidden bg-surface-container-high">
                        <img src="{{ $project->image ? asset('storage/' . $project->image) : 'https://ui-avatars.com/api/?name=' . urlencode($project->title) . '&background=0F172A&color=4cd7f6&size=800&bold=true' }}"
                            class="w-full h-full object-cover grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700 group-hover:scale-110">
                        <div
                            class="absolute bottom-4 left-4 bg-background/90 backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-bold text-primary uppercase tracking-widest border border-outline-variant/10">
                            {{ $project->category }}
                        </div>
                    </div>
                    <div class="p-8 space-y-4">
                        <h3
                            class="text-xl font-bold text-on-surface group-hover:text-primary transition-colors tracking-tight">
                            {{ $project->title }}</h3>
                        <p class="text-on-surface-variant font-light text-sm line-clamp-2 leading-relaxed">
                            {{ $project->description }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>