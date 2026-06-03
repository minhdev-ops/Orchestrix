import React from 'react';
import { motion } from 'framer-motion';

const Card = ({ title, category, image, description, tech, codeLink, demoLink }) => {
    return (
        <motion.div
            whileHover={{ y: -8 }}
            transition={{ duration: 0.4 }}
            className="group cursor-pointer bg-surface-secondary rounded-xl overflow-hidden border border-border hover:border-border-accent/50 hover:shadow-[0_20px_40px_rgba(0,0,0,0.4)] transition-all duration-500"
        >
            <div className="space-y-0">
                <div className="aspect-[16/9] overflow-hidden bg-background relative border-b border-border">
                    <img
                        src={image}
                        alt={title}
                        className="w-full h-full object-cover grayscale opacity-50 group-hover:grayscale-0 group-hover:scale-105 group-hover:opacity-100 transition-all duration-700"
                    />
                    <div className="absolute top-4 right-4 bg-background/80 backdrop-blur-md px-3 py-1 rounded-full border border-border text-[10px] font-bold text-white uppercase tracking-widest z-20">
                        {category}
                    </div>
                </div>
                <div className="p-8 space-y-6">
                    <div className="space-y-3">
                        <h3 className="text-[24px] font-display font-medium text-text group-hover:text-brand transition-colors leading-tight">
                            {title}
                        </h3>
                        <p className="text-[15px] text-text-muted leading-relaxed line-clamp-2 font-normal">
                            {description}
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-2">
                        {tech?.map(t => (
                            <span key={t} className="px-3 py-1 rounded-full bg-surface-muted border border-border text-[10px] font-mono text-text-muted uppercase tracking-wider">
                                {t}
                            </span>
                        ))}
                    </div>

                    <div className="grid grid-cols-2 gap-4 pt-4 border-t border-border">
                        <a
                            href={codeLink}
                            target="_blank"
                            rel="noreferrer"
                            className="flex items-center justify-center gap-2 py-3 rounded-lg border border-border text-[13px] font-bold text-text hover:bg-surface-muted hover:border-brand/40 transition-all"
                        >
                            <span className="material-symbols-outlined text-[18px]">code</span>
                            Mã nguồn
                        </a>
                        <a
                            href={demoLink}
                            target="_blank"
                            rel="noreferrer"
                            className="flex items-center justify-center gap-2 py-3 rounded-lg bg-brand/10 border border-brand/20 text-[13px] font-bold text-brand hover:bg-brand/20 transition-all"
                        >
                            <span className="material-symbols-outlined text-[18px]">rocket_launch</span>
                            Bản demo
                        </a>
                    </div>
                </div>
            </div>
        </motion.div>
    );
};

export default Card;
