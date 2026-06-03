<section id="contact" class="py-32 relative overflow-hidden bg-white dark:bg-[#0a0b0d]">
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-20 items-start">
            <div class="lg:col-span-5 space-y-12">
                <div class="space-y-6">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0052ff]"></span>
                        <span class="text-xs font-bold tracking-widest text-[#0052ff] uppercase">Connect //
                            Uplink</span>
                    </div>
                    <h2 class="text-[48px] md:text-[64px] font-display text-[#0a0b0d] dark:text-white leading-tight">
                        Let's build <br>the future.
                    </h2>
                </div>

                <p class="text-[20px] text-[#5b616e] dark:text-gray-400 leading-relaxed font-coinbase-text">
                    Available for architectural challenges, technical leadership, or high-agency product ventures.
                </p>

                <div class="space-y-6 pt-6">
                    <div class="flex items-center gap-6 group">
                        <div
                            class="w-14 h-14 rounded-2xl bg-[#eef0f3] dark:bg-[#282b31] flex items-center justify-center text-[#0052ff]">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <div>
                            <div class="text-[12px] font-bold text-[#5b616e] uppercase tracking-widest">Protocol.Email
                            </div>
                            <div
                                class="text-[18px] font-bold text-[#0a0b0d] dark:text-white group-hover:text-[#0052ff] transition-colors">
                                hello@orchestrix.core
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 group">
                        <div
                            class="w-14 h-14 rounded-2xl bg-[#eef0f3] dark:bg-[#282b31] flex items-center justify-center text-[#0052ff]">
                            <span class="material-symbols-outlined">location_on</span>
                        </div>
                        <div>
                            <div class="text-[12px] font-bold text-[#5b616e] uppercase tracking-widest">Base.Coordinates
                            </div>
                            <div class="text-[18px] font-bold text-[#0a0b0d] dark:text-white">
                                Hanoi // Sector-4
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-[#eef0f3] dark:bg-[#1a1b1d] p-10 md:p-16 rounded-[40px]">
                    <form action="{{ route('portfolio.contact.store') }}" method="POST" class="space-y-8">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label
                                    class="text-[14px] font-bold text-[#0a0b0d] dark:text-white uppercase tracking-widest">Name</label>
                                <input type="text" name="name" placeholder="ARCHITECT_NAME"
                                    class="w-full bg-white dark:bg-[#0a0b0d] border border-transparent focus:border-[#0052ff] rounded-2xl px-6 py-4 text-[#0a0b0d] dark:text-white font-bold outline-none transition-all shadow-sm">
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="text-[14px] font-bold text-[#0a0b0d] dark:text-white uppercase tracking-widest">Email</label>
                                <input type="email" name="email" placeholder="UPLINK@PROTOCOL"
                                    class="w-full bg-white dark:bg-[#0a0b0d] border border-transparent focus:border-[#0052ff] rounded-2xl px-6 py-4 text-[#0a0b0d] dark:text-white font-bold outline-none transition-all shadow-sm">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[14px] font-bold text-[#0a0b0d] dark:text-white uppercase tracking-widest">Message</label>
                            <textarea name="message" rows="5" placeholder="DESCRIBE MISSION PARAMETERS..."
                                class="w-full bg-white dark:bg-[#0a0b0d] border border-transparent focus:border-[#0052ff] rounded-2xl px-6 py-4 text-[#0a0b0d] dark:text-white font-bold outline-none transition-all shadow-sm resize-none"></textarea>
                        </div>
                        <div class="pt-4">
                            <button type="submit"
                                class="w-full btn-pill btn-primary py-6 text-sm uppercase tracking-[0.2em]">
                                Transmit Signal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>