import React from 'react';
import { motion } from 'framer-motion';

const Navbar = () => {
    const [scrolled, setScrolled] = React.useState(false);

    React.useEffect(() => {
        const handleScroll = () => setScrolled(window.scrollY > 50);
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    return (
        <nav className={`fixed top-0 left-0 right-0 z-50 transition-all duration-500 px-6 ${
            scrolled ? 'py-4' : 'py-8'
        }`}>
            <div className={`max-w-7xl mx-auto flex justify-between items-center transition-all duration-500 ${
                scrolled 
                    ? 'bg-white/70 backdrop-blur-2xl border border-gray-100 shadow-xl shadow-blue-500/5 rounded-full px-8 py-3' 
                    : 'bg-transparent py-2'
            }`}>
                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/20">
                        OX
                    </div>
                    <span className={`text-[22px] font-display font-bold tracking-tight uppercase transition-colors ${
                        scrolled ? 'text-[#1b1c1e]' : 'text-[#1b1c1e]'
                    }`}>
                        Orchestrix
                    </span>
                </div>

                <div className="hidden xl:flex items-center gap-8">
                    {[
                        { name: 'Trang chủ', id: 'base' },
                        { name: 'Hồ sơ', id: 'dossier' },
                        { name: 'Công nghệ', id: 'stack' },
                        { name: 'Dự án', id: 'registry' },
                        { name: 'Kiến trúc', id: 'architecture' },
                        { name: 'Liên hệ', id: 'signals' }
                    ].map((item) => (
                        <a
                            key={item.id}
                            href={`#${item.id}`}
                            className="text-[13px] font-bold text-gray-500 hover:text-primary transition-all uppercase tracking-widest relative group"
                        >
                            {item.name}
                            <span className="absolute bottom-[-4px] left-0 w-0 h-[2px] bg-primary transition-all group-hover:w-full" />
                        </a>
                    ))}
                </div>

                <div className="flex items-center gap-4">
                    <button className="px-8 py-3 rounded-full bg-primary text-white text-[14px] font-bold hover:bg-blue-600 transition-all flex items-center gap-2 group shadow-lg shadow-blue-500/20">
                        Bắt đầu
                        <span className="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                </div>
            </div>
        </nav>
    );
};

export default Navbar;
