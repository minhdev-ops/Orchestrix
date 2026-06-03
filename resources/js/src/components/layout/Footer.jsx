import React from 'react';

const Footer = () => {
    return (
        <footer className="py-20 px-6 bg-background-dark text-text border-t border-border">
            <div className="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-10">
                <div className="flex items-center gap-3">
                    <div className="w-8 h-8 bg-brand rounded-lg flex items-center justify-center text-background font-bold shadow-lg shadow-brand/20">
                        OX
                    </div>
                    <span className="text-[20px] font-bold tracking-tight uppercase">
                        Orchestrix
                    </span>
                </div>

                <div className="flex gap-8 text-text-muted text-[14px] font-bold uppercase tracking-widest">
                    <a href="https://twitter.com/orchestrix" target="_blank" rel="noopener noreferrer" className="hover:text-brand transition-colors">Twitter</a>
                    <a href="https://github.com/orchestrix" target="_blank" rel="noopener noreferrer" className="hover:text-brand transition-colors">GitHub</a>
                    <a href="https://linkedin.com/in/orchestrix" target="_blank" rel="noopener noreferrer" className="hover:text-brand transition-colors">LinkedIn</a>
                </div>

                <div className="text-text-muted text-[14px] font-medium">
                    © 2026 Orchestrix Orchestration. Bảo lưu mọi bản quyền.
                </div>
            </div>
        </footer>
    );
};

export default Footer;
