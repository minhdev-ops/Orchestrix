import React from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { PROJECTS } from '../constants';
import Card from '../components/ui/Card';

const Projects = () => {
    const [projects, setProjects] = React.useState([]);
    const [filteredProjects, setFilteredProjects] = React.useState([]);
    const [categories, setCategories] = React.useState(['Tất cả']);
    const [activeCategory, setActiveCategory] = React.useState('Tất cả');
    const [loading, setLoading] = React.useState(true);

    React.useEffect(() => {
        fetch('/portfolio/api/projects')
            .then(res => res.json())
            .then(data => {
                setProjects(data);
                setFilteredProjects(data);
                const uniqueCategories = ['Tất cả', ...new Set(data.map(p => p.category))];
                setCategories(uniqueCategories);
                setLoading(false);
            })
            .catch(err => {
                console.error("Failed to fetch projects:", err);
                setLoading(false);
            });
    }, []);

    const filterByCategory = (cat) => {
        setActiveCategory(cat);
        if (cat === 'Tất cả') {
            setFilteredProjects(projects);
        } else {
            setFilteredProjects(projects.filter(p => p.category === cat));
        }
    };

    if (loading) return null;
    if (projects.length === 0) return null;

    return (
        <section id="registry" className="relative py-32 px-6 bg-transparent text-[#1b1c1e]">
            {/* Background pattern */}
            <div className="absolute inset-0 opacity-[0.03] pointer-events-none" 
                 style={{ backgroundImage: 'radial-gradient(#0052ff 0.5px, transparent 0.5px)', backgroundSize: '24px 24px' }} />

            <div className="max-w-7xl mx-auto relative z-10">
                <div className="flex flex-col md:flex-row justify-between items-end mb-24 gap-8">
                    <motion.div
                        initial={{ opacity: 0, x: -20 }}
                        whileInView={{ opacity: 1, x: 0 }}
                        viewport={{ once: true }}
                        className="space-y-6"
                    >
                        <span className="text-[14px] font-bold text-primary uppercase tracking-[0.2em]">Danh mục</span>
                        <h2 className="text-[48px] md:text-[64px] font-display font-medium tracking-tight leading-[1.0]">
                            Dự án <br /> tiêu biểu.
                        </h2>
                        
                        {/* Filter Buttons */}
                        <div className="flex flex-wrap gap-3 pt-4">
                            {categories.map(cat => (
                                <button
                                    key={cat}
                                    onClick={() => filterByCategory(cat)}
                                    className={`px-6 py-2.5 rounded-full text-[12px] font-bold uppercase tracking-widest transition-all border ${
                                        activeCategory === cat 
                                            ? 'bg-primary text-white border-primary shadow-xl shadow-blue-500/20' 
                                            : 'bg-white text-gray-400 border-gray-100 hover:border-primary/40 hover:text-primary'
                                    }`}
                                >
                                    {cat}
                                </button>
                            ))}
                        </div>
                    </motion.div>

                    <motion.a
                        href="/portfolio/projects"
                        whileHover={{ scale: 1.05 }}
                        whileTap={{ scale: 0.95 }}
                        className="px-10 py-5 rounded-full bg-[#f8fafc] border border-gray-100 text-[#1b1c1e] text-[16px] font-bold hover:border-primary hover:bg-white transition-all flex items-center gap-2 shadow-sm"
                    >
                        Khám phá tất cả
                        <span className="material-symbols-outlined">arrow_forward</span>
                    </motion.a>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <AnimatePresence mode="popLayout">
                        {filteredProjects.map((project, index) => (
                            <motion.div
                                layout
                                key={project.id}
                                initial={{ opacity: 0, y: 30 }}
                                animate={{ opacity: 1, y: 0 }}
                                exit={{ opacity: 0, scale: 0.95 }}
                                transition={{ duration: 0.5, delay: index * 0.1 }}
                            >
                                <Card {...project} />
                            </motion.div>
                        ))}
                    </AnimatePresence>
                </div>
            </div>
        </section>
    );
};

export default Projects;
