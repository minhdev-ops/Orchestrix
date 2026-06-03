import React, { useEffect, useState } from 'react';
import { motion } from 'framer-motion';

const Blog = () => {
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetch('/blog/api/latest')
            .then(res => res.json())
            .then(data => {
                setPosts(data);
                setLoading(false);
            })
            .catch(err => {
                console.error("Failed to fetch posts:", err);
                setLoading(false);
            });
    }, []);

    if (loading) return null;
    if (posts.length === 0) return null;

    return (
        <section id="blog" className="relative py-32 px-6 bg-transparent overflow-hidden">
            <div className="max-w-7xl mx-auto">
                <div className="flex flex-col md:flex-row justify-between items-end gap-8 mb-20">
                    <motion.div
                        initial={{ opacity: 0, x: -30 }}
                        whileInView={{ opacity: 1, x: 0 }}
                        viewport={{ once: true }}
                        className="space-y-6"
                    >
                        <h2 className="text-[48px] md:text-[64px] font-display font-medium tracking-tight leading-[1.0]">
                            Góc nhìn <br /> kỹ thuật.
                        </h2>
                        <p className="text-text-muted text-[18px] max-w-xl leading-relaxed">
                            Những bài viết chuyên sâu về kiến trúc phần mềm, hệ thống phân tán
                            và những xu hướng mới trong thế giới công nghệ.
                        </p>
                    </motion.div>

                    <motion.a
                        href="/blog"
                        initial={{ opacity: 0, scale: 0.95 }}
                        whileInView={{ opacity: 1, scale: 1 }}
                        viewport={{ once: true }}
                        whileHover={{ scale: 1.05 }}
                        whileTap={{ scale: 0.95 }}
                        className="px-8 py-4 rounded-full bg-surface-secondary border border-border text-text text-[14px] font-bold hover:border-brand/40 hover:bg-surface-muted transition-all flex items-center gap-2"
                    >
                        Xem tất cả bài viết
                        <span className="material-symbols-outlined">arrow_forward</span>
                    </motion.a>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {posts.map((post, index) => (
                        <motion.article
                            key={post.id}
                            initial={{ opacity: 0, y: 30 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ delay: index * 0.1 }}
                            className="group flex flex-col h-full bg-surface-secondary border border-border rounded-[24px] overflow-hidden hover:border-brand/40 transition-all duration-500 shadow-xl hover:shadow-brand/5"
                        >
                            <div className="relative h-[240px] overflow-hidden">
                                <img
                                    src={post.image}
                                    alt={post.title}
                                    className="w-full h-full object-cover grayscale opacity-50 group-hover:grayscale-0 group-hover:scale-105 group-hover:opacity-100 transition-all duration-700"
                                />
                                <div className="absolute top-4 left-4 bg-background/80 backdrop-blur-md px-3 py-1 rounded-full border border-border text-[9px] font-bold text-brand uppercase tracking-widest">
                                    {post.category}
                                </div>
                            </div>

                            <div className="p-8 flex flex-col flex-1 space-y-6">
                                <div className="space-y-4 flex-1">
                                    <span className="text-[12px] font-mono text-text-muted/60">{post.date}</span>
                                    <h3 className="text-[24px] font-display font-medium text-text group-hover:text-brand transition-colors leading-[1.2]">
                                        {post.title}
                                    </h3>
                                    <p className="text-[16px] text-text-muted leading-relaxed line-clamp-3">
                                        {post.excerpt}
                                    </p>
                                </div>

                                <a
                                    href={post.url}
                                    className="pt-6 border-t border-border flex justify-between items-center group/btn cursor-pointer"
                                >
                                    <span className="text-[12px] font-bold uppercase tracking-widest text-text group-hover/btn:text-brand transition-colors">Đọc chi tiết</span>
                                    <span className="material-symbols-outlined text-[18px] text-text-muted group-hover/btn:text-brand group-hover/btn:translate-x-1 transition-all">north_east</span>
                                </a>
                            </div>
                        </motion.article>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default Blog;

