import React from 'react';
import { motion } from 'framer-motion';
const Hero = () => {
    return (
        <section id="base" className="relative min-h-screen flex items-center justify-center overflow-hidden bg-transparent">
            {/* Local canvas removed, using GlobalCanvas */}

            <div className="relative z-10 max-w-7xl mx-auto px-6 pt-20 flex flex-col items-center text-center">
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.8 }}
                    className="space-y-6"
                >
                    <span className="text-primary font-bold text-[14px] uppercase tracking-widest flex items-center justify-center gap-2">
                        <span className="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.3)]"></span>
                        Sẵn sàng cho các dự án mới
                    </span>

                    <h1 className="text-[48px] sm:text-[72px] md:text-[88px] font-display font-medium tracking-tight text-[#1b1c1e] leading-[0.95] max-w-4xl mx-auto drop-shadow-sm">
                        Xây dựng tương lai <br className="hidden sm:block" />của hệ thống số.
                    </h1>

                    <p className="text-[18px] md:text-[24px] text-gray-600 max-w-2xl mx-auto font-normal leading-relaxed">
                        Kiến trúc sư phần mềm chuyên về các hệ thống sẵn sàng cao,
                        triển khai cloud-native và nghệ thuật điều phối DevOps.
                    </p>

                    <div className="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
                        <button className="px-10 py-5 bg-primary text-white rounded-full text-[18px] font-bold hover:bg-blue-600 transition-all flex items-center gap-3 group shadow-lg shadow-blue-500/20">
                            Bắt đầu ngay
                            <span className="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </button>
                        <div className="flex gap-4">
                            <button className="px-8 py-5 bg-white/40 backdrop-blur-xl text-[#1b1c1e] rounded-full text-[16px] font-bold hover:bg-white/60 transition-all border border-white/40 shadow-sm flex items-center gap-2">
                                <span className="material-symbols-outlined text-[20px]">download</span>
                                Tải CV
                            </button>
                            <button className="px-8 py-5 bg-white/40 backdrop-blur-xl text-[#1b1c1e] rounded-full text-[16px] font-bold hover:bg-white/60 transition-all border border-white/40 shadow-sm">
                                Liên hệ
                            </button>
                        </div>
                    </div>
                </motion.div>
            </div>

            {/* Subtle Gradient Overlay for depth */}
            <div className="absolute inset-0 bg-gradient-to-b from-white/0 via-white/10 to-white/60 pointer-events-none z-[5]" />
        </section>
    );
};

export default Hero;
