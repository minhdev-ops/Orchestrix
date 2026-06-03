<section id="skills" class="py-32 bg-surface-container-low/30">
    <div class="max-w-7xl mx-auto px-6">
        <div class="max-w-3xl mb-16 space-y-4">
            <div class="text-xs font-bold tracking-widest text-primary uppercase">Competency Matrix</div>
            <h2 class="text-4xl md:text-5xl font-black text-on-surface tracking-tight">Architectural Arsenal</h2>
            <p class="text-lg text-on-surface-variant font-light leading-relaxed">Systematic mastery across
                high-availability architectures, cloud-native orchestration, and end-to-end full-stack systems.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($skills as $skill)
                <div
                    class="bg-surface-container-low p-8 rounded-2xl border border-outline-variant/10 hover:border-primary/40 transition-all duration-300 hover:shadow-xl group interactive-tap">
                    <div
                        class="w-14 h-14 rounded-xl bg-surface-container-high flex items-center justify-center text-primary mb-6 border border-outline-variant/5 shadow-sm group-hover:scale-110 transition-transform">
                        <x-portfolio.icon :name="$skill->icon" width="32" class="text-3xl" />
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-lg font-bold text-on-surface group-hover:text-primary transition-colors">
                            {{ $skill->name }}
                        </h3>
                        <div
                            class="flex items-center justify-between text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">
                            <span>Proficiency</span>
                            <span class="text-primary">{{ $skill->level }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-surface-container-highest rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full transition-all duration-1000 delay-100"
                                style="width: {{ $skill->level }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-20 text-center">
            <a href="{{ route('portfolio.skills.index') }}"
                class="inline-flex items-center gap-3 px-10 py-5 bg-surface-container-high border border-outline-variant/10 text-on-surface rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-surface-container-highest hover:border-primary/20 transition-all active:scale-95">
                Detailed Skill Matrix
                <span class="material-symbols-outlined text-[18px]">grid_view</span>
            </a>
        </div>
    </div>
</section>