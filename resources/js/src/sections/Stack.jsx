import React from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { SKILLS } from '../constants';

const Stack = () => {
    const [skills, setSkills] = React.useState([]);
    const [filteredSkills, setFilteredSkills] = React.useState([]);
    const [categories, setCategories] = React.useState(['Tất cả']);
    const [activeCategory, setActiveCategory] = React.useState('Tất cả');
    const [loading, setLoading] = React.useState(true);

    React.useEffect(() => {
        fetch('/portfolio/api/skills')
            .then(res => res.json())
            .then(data => {
                setSkills(data);
                setFilteredSkills(data);
                
                const uniqueCategories = ['Tất cả', ...new Set(data.map(s => s.category))];
                setCategories(uniqueCategories);
                setLoading(false);
            })
            .catch(err => {
                console.error("Failed to fetch skills:", err);
                setLoading(false);
            });
    }, []);

    const filterByCategory = (cat) => {
        setActiveCategory(cat);
        if (cat === 'Tất cả') {
            setFilteredSkills(skills);
        } else {
            setFilteredSkills(skills.filter(s => s.category === cat));
        }
    };

    if (loading) return null;
    if (skills.length === 0) return null;

    return (
        <section id="stack" className="relative py-32 px-6 bg-transparent overflow-hidden">
            {/* Background parallax text */}
            <div className="absolute top-20 left-10 text-[200px] font-display font-bold text-gray-50/50 pointer-events-none select-none z-0 leading-none">
                STACK
            </div>

            <div className="max-w-7xl mx-auto relative z-10">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        className="lg:col-span-12 xl:col-span-4 space-y-8 xl:sticky xl:top-32"
                    >
                        <div className="space-y-6">
                            <span className="text-[14px] font-bold text-primary uppercase tracking-[0.2em]">Kỹ năng & Công nghệ</span>
                            <h2 className="text-[48px] md:text-[64px] font-display font-medium text-[#1b1c1e] mt-0 leading-tight">
                                Hệ thống <br />vững chãi.
                            </h2>
                            <p className="text-[20px] text-gray-500 leading-relaxed max-w-md">
                                Tuyển tập các công nghệ được sử dụng để xây dựng
                                hạ tầng bền bỉ và có khả năng mở rộng.
                            </p>
                        </div>

                        {/* Filter Buttons */}
                        <div className="flex flex-wrap gap-2.5 pt-4">
                            {categories.map(cat => (
                                <button
                                    key={cat}
                                    onClick={() => filterByCategory(cat)}
                                    className={`px-5 py-2.5 rounded-full text-[12px] font-bold uppercase tracking-widest transition-all border ${
                                        activeCategory === cat 
                                            ? 'bg-primary text-white border-primary shadow-xl shadow-blue-500/20' 
                                            : 'bg-[#f8fafc] text-gray-400 border-gray-100 hover:border-primary/40'
                                    }`}
                                >
                                    {cat}
                                </button>
                            ))}
                        </div>
                    </motion.div>

                    <div className="lg:col-span-12 xl:col-span-8 grid grid-cols-1 md:grid-cols-6 gap-6 auto-rows-[220px]">
                        <AnimatePresence mode="popLayout">
                            {filteredSkills.map((skill, index) => (
                                <motion.div
                                    layout
                                    key={skill.name}
                                    initial={{ opacity: 0, scale: 0.95 }}
                                    whileInView={{ opacity: 1, scale: 1 }}
                                    viewport={{ once: true }}
                                    transition={{ duration: 0.5, delay: index * 0.05 }}
                                    whileHover={{ y: -8, scale: 1.02 }}
                                    style={{ "--glow-color": skill.brandColor || '#0052ff' }}
                                    className={`
                                        bg-white rounded-[32px] p-8 flex flex-col justify-between
                                        group transition-all duration-500 border border-gray-100 relative overflow-hidden
                                        hover:shadow-2xl hover:shadow-[var(--glow-color)]/10
                                        ${skill.size === 'large' ? 'md:col-span-4 md:row-span-2' : ''}
                                        ${skill.size === 'medium' ? 'md:col-span-3 md:row-span-1' : ''}
                                        ${skill.size === 'small' ? 'md:col-span-2 md:row-span-1' : ''}
                                        ${!skill.size ? 'md:col-span-2 md:row-span-1' : ''}
                                    `}
                                >
                                    <div
                                        className="w-16 h-16 bg-gray-50 rounded-[22px] flex items-center justify-center border border-gray-100 group-hover:scale-110 group-hover:bg-white group-hover:border-[var(--glow-color)]/30 transition-all duration-500 relative z-10"
                                    >
                                        <span className="material-symbols-outlined text-[36px] transition-transform duration-700 group-hover:rotate-[360deg]" style={{ color: skill.brandColor || '#0052ff' }}>
                                            {skill.icon || 'star'}
                                        </span>
                                    </div>
                                    <div className="space-y-2 relative z-10">
                                        <h4 className="text-[22px] font-display font-medium text-[#1b1c1e]">
                                            {skill.name}
                                        </h4>
                                        <p className="text-[14px] text-gray-400 leading-relaxed line-clamp-2">
                                            {skill.desc}
                                        </p>
                                    </div>
                                    {/* Subtle Ambient Glow */}
                                    <div className="absolute top-[-20%] right-[-20%] w-40 h-40 blur-[60px] opacity-0 group-hover:opacity-10 transition-opacity pointer-events-none" style={{ backgroundColor: skill.brandColor || '#0052ff' }} />
                                </motion.div>
                            ))}
                        </AnimatePresence>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default Stack;
