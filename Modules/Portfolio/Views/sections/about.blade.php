<section id="about" class="py-32 relative overflow-hidden bg-transparent">
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-20 items-start">
            <div class="lg:col-span-12 mb-8">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0052ff]"></span>
                    <span class="text-xs font-bold tracking-widest text-[#0052ff] uppercase">Bio // Archive</span>
                </div>
            </div>

            <div class="lg:col-span-12 xl:col-span-7 space-y-12">
                <h2 class="text-[48px] md:text-[80px] font-display text-white leading-tight">
                    {{ $about->title ?? 'Engineering human interfaces.' }}
                </h2>
                <div
                    class="space-y-8 text-[20px] md:text-[24px] text-gray-400 font-coinbase-text leading-relaxed prose prose-invert max-w-none">
                    @if($about && $about->description)
                        {!! \Illuminate\Support\Str::markdown($about->description) !!}
                    @else
                        <p>Senior Software Architect with a passion for building robust, scalable systems that power the
                            next generation of digital infrastructure.</p>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-12 xl:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-6 pt-10 xl:pt-0">
                @if(isset($aboutStats) && $aboutStats->count() > 0)
                    @foreach($aboutStats as $stat)
                        <div
                            class="bg-[#282b31] p-10 rounded-[32px] flex flex-col justify-between aspect-square group hover:bg-[#32363d] transition-all border border-white/5">
                            <span
                                class="text-[12px] font-bold text-[#0052ff] uppercase tracking-widest">METRIC_{{ $loop->iteration }}</span>
                            <div>
                                <div class="text-[56px] font-display text-white mb-2">{{ $stat->value }}</div>
                                <div class="text-[14px] text-gray-400 uppercase tracking-widest font-bold">{{ $stat->label }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    @php
                        $placeholders = [
                            ['val' => '10+', 'lab' => 'Years Experience'],
                            ['val' => '150+', 'lab' => 'System Deploys'],
                            ['val' => '24/7', 'lab' => 'Ops Readiness'],
                            ['val' => '0', 'lab' => 'System Latency']
                        ];
                    @endphp
                    @foreach($placeholders as $p)
                        <div
                            class="bg-[#282b31] p-10 rounded-[32px] flex flex-col justify-between aspect-square group hover:bg-[#32363d] transition-all border border-white/5">
                            <span class="text-[12px] font-bold text-[#0052ff] uppercase tracking-widest">SYSTEM_VAL</span>
                            <div>
                                <div class="text-[56px] font-display text-white mb-2">{{ $p['val'] }}</div>
                                <div class="text-[14px] text-gray-400 uppercase tracking-widest font-bold">{{ $p['lab'] }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</section>