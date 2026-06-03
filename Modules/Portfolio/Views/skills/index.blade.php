@extends('layouts.portfolio')

@section('title', 'Skills Matrix | Orchestrix')

@section('content')
    <div class="pt-24 min-h-screen bg-surface">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col lg:flex-row gap-16 py-20">
                {{-- Left Sidebar: Category Navigation --}}
                <aside class="lg:w-1/4 lg:sticky lg:top-32 h-fit space-y-12">
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                            <span class="text-[10px] font-bold tracking-widest text-primary uppercase">Blueprint</span>
                        </div>
                        <h1 class="text-4xl font-black text-on-surface tracking-tight leading-tight">
                            Technical <br/> Arsenal
                        </h1>
                        <p class="text-sm text-on-surface-variant leading-relaxed font-light">
                            A systematic breakdown of my architectural capabilities and proficiency levels.
                        </p>
                    </div>

                    <nav class="space-y-2">
                        @php $groupedSkills = $skills->groupBy('category'); @endphp
                        @foreach($groupedSkills as $category => $categorySkills)
                            <a href="#{{ Str::slug($category) }}" 
                               class="group flex items-center justify-between p-4 rounded-xl hover:bg-surface-container-low transition-all border border-transparent hover:border-outline-variant/10">
                                <span class="text-xs font-bold text-on-surface-variant group-hover:text-primary uppercase tracking-widest transition-colors">{{ $category }}</span>
                                <span class="text-[10px] font-medium text-on-surface-variant/40">{{ $categorySkills->count() }}</span>
                            </a>
                        @endforeach
                    </nav>

                    <div class="p-6 rounded-2xl bg-primary/5 border border-primary/10 hidden lg:block">
                        <h4 class="text-[10px] font-black text-primary uppercase tracking-widest mb-3">Looking for a specific stack?</h4>
                        <p class="text-xs text-on-surface-variant font-light leading-relaxed mb-4">
                            My expertise covers cloud-native, real-time, and enterprise-grade system design.
                        </p>
                        <a href="{{ route('portfolio.contact') }}" class="text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">Get Detailed CV →</a>
                    </div>
                </aside>

                {{-- Right Content: Category Sections --}}
                <main class="lg:w-3/4 space-y-32">
                    @foreach($groupedSkills as $category => $categorySkills)
                        <section id="{{ Str::slug($category) }}" class="space-y-12 animate-fade-up">
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <h2 class="text-2xl font-black text-on-surface uppercase tracking-tight">{{ $category }}</h2>
                                    <div class="h-px flex-1 bg-outline-variant/20"></div>
                                </div>
                                <p class="text-sm text-on-surface-variant/60 font-light max-w-2xl italic">
                                    Specialized tools and methodologies utilized within the {{ strtolower($category) }} domain.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                @foreach($categorySkills as $skill)
                                    <div class="group bg-surface-container-low border border-outline-variant/10 rounded-2xl p-8 hover:border-primary/40 transition-all duration-300 hover:shadow-xl hover:shadow-primary/5">
                                        <div class="space-y-6">
                                            <div class="flex items-start justify-between">
                                                <div class="w-14 h-14 rounded-2xl bg-surface-container-high flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-500 shadow-sm border border-outline-variant/5">
                                                    <x-portfolio.icon :name="$skill->icon" width="32" class="text-3xl" />
                                                </div>
                                                <div class="flex flex-col items-end">
                                                    <span class="text-[10px] font-black text-primary uppercase tracking-widest">{{ $skill->level }}%</span>
                                                    <div class="mt-2 h-1 w-12 bg-surface-container-highest rounded-full overflow-hidden">
                                                        <div class="h-full bg-primary rounded-full" style="width: {{ $skill->level }}%"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="space-y-2">
                                                <h3 class="text-lg font-bold text-on-surface group-hover:text-primary transition-colors tracking-tight">
                                                    {{ $skill->name }}
                                                </h3>
                                                <p class="text-xs text-on-surface-variant font-light leading-relaxed">
                                                    {{ $skill->description }}
                                                </p>
                                            </div>

                                            <div class="flex items-center gap-1.5 pt-2">
                                                @php $filled = round($skill->level / 20); @endphp
                                                @for($i = 0; $i < 5; $i++)
                                                    <div class="h-1 w-full rounded-full transition-colors {{ $i < $filled ? 'bg-primary' : 'bg-outline-variant/20' }}"></div>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </main>
            </div>
        </div>
    </div>
@endsection