import React from 'react';
import { motion } from 'framer-motion';
import { SKILLS } from '../constants';

const Counter = ({ value, duration = 2 }) => {
    const [count, setCount] = React.useState(0);
    const nodeRef = React.useRef(null);
    const [isInView, setIsInView] = React.useState(false);

    React.useEffect(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) setIsInView(true);
            },
            { threshold: 0.1 }
        );
        if (nodeRef.current) observer.observe(nodeRef.current);
        return () => observer.disconnect();
    }, []);

    React.useEffect(() => {
        if (!isInView) return;
        
        let start = 0;
        const end = parseInt(value.replace(/\D/g, ''));
        if (start === end) return;

        let totalMiliseconds = duration * 1000;
        let incrementTime = totalMiliseconds / end;

        let timer = setInterval(() => {
            start += 1;
            setCount(start);
            if (start === end) clearInterval(timer);
        }, incrementTime);

        return () => clearInterval(timer);
    }, [isInView, value, duration]);

    return <span ref={nodeRef}>{count}{value.replace(/[0-9]/g, '')}</span>;
};

const About = () => {
    return (
        <section id="dossier" className="relative py-32 px-6 bg-transparent overflow-hidden">
            {/* Subtle Background 3D Effect for this section */}
            <div className="absolute top-0 right-0 w-1/2 h-full opacity-20 pointer-events-none">
                <div className="w-full h-full bg-[radial-gradient(circle_at_center,_var(--primary)_0%,_transparent_70%)] blur-[120px]" />
            </div>

            <div className="max-w-7xl mx-auto relative z-10">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <motion.div
                        initial={{ opacity: 0, x: -30 }}
                        whileInView={{ opacity: 1, x: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.8 }}
                        className="space-y-8"
                    >
                        <div className="space-y-6">
                            <span className="text-[14px] font-bold text-primary uppercase tracking-[0.2em]">Về tôi</span>
                            <h2 className="text-[48px] md:text-[64px] font-display font-medium text-[#1b1c1e] mt-0 leading-tight">
                                Kỹ thuật <br />xuất sắc.
                            </h2>
                            <p className="text-[22px] text-[#1b1c1e] leading-relaxed max-w-xl font-medium">
                                Chuyển đổi các nút thắt cổ chai kế thừa thành hạ tầng
                                cloud-native hiệu năng cao với độ chính xác theo mô-đun.
                            </p>
                            <p className="text-[18px] text-gray-500 leading-relaxed max-w-xl">
                                Với hơn một thập kỷ kinh nghiệm xây dựng các hệ thống phân tán,
                                tôi chuyên về môi trường sẵn sàng cao, điều phối DevOps
                                và bảo mật các hệ thống giao dịch khối lượng lớn.
                            </p>
                        </div>

                        <div className="grid grid-cols-2 md:grid-cols-3 gap-8 pt-8 border-t border-gray-100">
                            <div>
                                <p className="text-[12px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kinh nghiệm</p>
                                <p className="text-[24px] font-display font-medium text-[#1b1c1e]">
                                    <Counter value="10+" />
                                </p>
                            </div>
                            <div>
                                <p className="text-[12px] font-bold text-gray-400 uppercase tracking-widest mb-2">Dự án</p>
                                <p className="text-[24px] font-display font-medium text-[#1b1c1e]">
                                    <Counter value="50+" />
                                </p>
                            </div>
                            <div>
                                <p className="text-[12px] font-bold text-gray-400 uppercase tracking-widest mb-2">Trạng thái</p>
                                <p className="text-[24px] font-display font-medium text-primary">Sẵn sàng</p>
                            </div>
                        </div>
                    </motion.div>

                    <motion.div
                        initial={{ opacity: 0, y: 30 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 1 }}
                        className="relative"
                    >
                        {/* Interactive Card with Glassmorphism */}
                        <div className="aspect-square bg-white border border-gray-100 rounded-[40px] p-12 overflow-hidden flex flex-col justify-center relative group shadow-2xl shadow-blue-500/5">
                            <div className="absolute inset-0 bg-primary/5 blur-[100px] group-hover:bg-primary/10 transition-colors" />
                            
                            <div className="relative z-10 space-y-6">
                                <div className="w-16 h-1 bg-primary rounded-full animate-pulse" />
                                <h3 className="text-[28px] font-display font-medium text-[#1b1c1e]">Triết lý cốt lõi</h3>
                                <p className="text-[16px] text-gray-500 leading-relaxed italic">
                                    "Tôi không chỉ xây dựng phần mềm, tôi xây dựng những hệ thống có khả năng tự vận hành, 
                                    tự phục hồi và sẵn sàng cho những thử thách lớn nhất của kỷ nguyên số."
                                </p>
                                <div className="flex flex-wrap gap-3 pt-4">
                                    {['Bền bỉ', 'Bảo mật', 'Hiệu năng'].map(tag => (
                                        <div key={tag} className="px-4 py-2 bg-blue-50 text-primary border border-blue-100 rounded-full text-[12px] font-bold uppercase tracking-wider">
                                            {tag}
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Floating Background Icon with parallax effect via hover */}
                            <span className="absolute bottom-[-40px] right-[-40px] material-symbols-outlined text-[240px] text-primary/5 rotate-12 group-hover:rotate-0 group-hover:scale-110 transition-all duration-1000 ease-out">
                                engineering
                            </span>
                        </div>
                    </motion.div>
                </div>
            </div>
        </section>
    );
};

export default About;
