import React from 'react';
import { motion } from 'framer-motion';

const Architecture = () => {
    return (
        <section id="architecture" className="relative py-32 px-6 bg-transparent overflow-hidden">
            {/* Blueprint grid background */}
            <div className="absolute inset-0 opacity-[0.05] pointer-events-none" 
                 style={{ backgroundImage: 'linear-gradient(#0052ff 1px, transparent 1px), linear-gradient(90deg, #0052ff 1px, transparent 1px)', backgroundSize: '100px 100px' }} />

            <div className="max-w-7xl mx-auto relative z-10">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                    <motion.div
                        initial={{ opacity: 0, x: -30 }}
                        whileInView={{ opacity: 1, x: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.8 }}
                        className="space-y-8"
                    >
                        <div className="space-y-6">
                            <span className="text-[14px] font-bold text-primary uppercase tracking-[0.2em]">Kiến trúc hệ thống</span>
                            <h2 className="text-[48px] md:text-[64px] font-display font-medium text-[#1b1c1e] mt-0 leading-tight">
                                Tư duy <br />hệ thống.
                            </h2>
                            <p className="text-[20px] text-gray-500 leading-relaxed max-w-md">
                                Thiết kế các kiến trúc phức tạp, từ Microservices đến 
                                Cloud-Native Infra, đảm bảo tính mở rộng và khả năng chịu tải.
                            </p>
                        </div>

                        <div className="space-y-4">
                            {[
                                { title: "High Availability", desc: "Hệ thống không có điểm chết (No Single Point of Failure)." },
                                { title: "Scalability", desc: "Tự động mở rộng tài nguyên dựa trên lưu lượng thực tế." },
                                { title: "Security by Design", desc: "Tích hợp bảo mật vào từng lớp kiến trúc." }
                            ].map((item, i) => (
                                <motion.div 
                                    key={i} 
                                    initial={{ opacity: 0, y: 10 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    transition={{ delay: i * 0.1 }}
                                    className="flex gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-colors group"
                                >
                                    <div className="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
                                        <span className="material-symbols-outlined text-[20px]">architecture</span>
                                    </div>
                                    <div>
                                        <h4 className="text-[18px] font-bold text-[#1b1c1e]">{item.title}</h4>
                                        <p className="text-[14px] text-gray-400">{item.desc}</p>
                                    </div>
                                </motion.div>
                            ))}
                        </div>
                    </motion.div>

                    <motion.div
                        initial={{ opacity: 0, scale: 0.95 }}
                        whileInView={{ opacity: 1, scale: 1 }}
                        viewport={{ once: true }}
                        className="relative"
                    >
                        <div className="aspect-[4/3] bg-white border border-gray-100 rounded-[40px] overflow-hidden p-6 group shadow-2xl shadow-blue-500/5 relative">
                            <div className="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity" />
                            <img 
                                src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=1200" 
                                alt="System Architecture Diagram" 
                                className="w-full h-full object-cover rounded-[32px] grayscale-[0.5] group-hover:grayscale-0 transition-all duration-1000 scale-110 group-hover:scale-100"
                            />
                            
                            {/* Technical Overlay */}
                            <div className="absolute top-10 right-10 flex flex-col gap-2">
                                <div className="px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 rounded text-[10px] text-white font-mono uppercase">Node: 1024</div>
                                <div className="px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 rounded text-[10px] text-white font-mono uppercase">Status: Active</div>
                            </div>
                        </div>
                        
                        {/* Blueprint decorative lines */}
                        <div className="absolute -top-6 -right-6 w-32 h-32 border-t border-r border-primary/20 rounded-tr-[40px]" />
                        <div className="absolute -bottom-6 -left-6 w-32 h-32 border-b border-l border-primary/20 rounded-bl-[40px]" />
                    </motion.div>
                </div>
            </div>
        </section>
    );
};

export default Architecture;
