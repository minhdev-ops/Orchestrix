import React from 'react';
import { motion } from 'framer-motion';

const TESTIMONIALS = [
    {
        name: "Nguyễn Văn A",
        role: "CTO, TechCorp",
        content: "Hệ thống mà Orchestrix xây dựng đã giúp chúng tôi xử lý lượng truy cập gấp 10 lần mà không gặp bất kỳ sự cố nào. Một kiến trúc sư thực thụ.",
        avatar: "https://i.pravatar.cc/150?u=a"
    },
    {
        name: "Lê Thị B",
        role: "Product Manager, StartupX",
        content: "Khả năng tối ưu hóa quy trình DevOps của anh ấy là không thể tin được. Đội ngũ của chúng tôi đã tăng tốc độ release lên 5 lần.",
        avatar: "https://i.pravatar.cc/150?u=b"
    }
];

const Testimonials = () => {
    return (
        <section id="testimonials" className="relative py-32 px-6 bg-transparent overflow-hidden">
            <div className="max-w-7xl mx-auto">
                <div className="text-center mb-24">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        className="space-y-6"
                    >
                        <h2 className="text-[48px] md:text-[64px] font-display font-medium tracking-tight">
                            Lời chứng thực.
                        </h2>
                        <p className="text-text-muted text-[18px] max-w-2xl mx-auto leading-relaxed">
                            Những phản hồi từ đối tác và đồng nghiệp về hiệu quả
                            của các giải pháp kiến trúc đã triển khai.
                        </p>
                    </motion.div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {TESTIMONIALS.map((item, index) => (
                        <motion.div
                            key={index}
                            initial={{ opacity: 0, scale: 0.95 }}
                            whileInView={{ opacity: 1, scale: 1 }}
                            viewport={{ once: true }}
                            transition={{ delay: index * 0.1 }}
                            className="p-10 bg-surface-secondary border border-border rounded-[32px] space-y-8"
                        >
                            <p className="text-[20px] text-text leading-relaxed italic">
                                "{item.content}"
                            </p>
                            <div className="flex items-center gap-4">
                                <img src={item.avatar} alt={item.name} className="w-12 h-12 rounded-full grayscale" />
                                <div>
                                    <h4 className="text-[18px] font-bold text-text">{item.name}</h4>
                                    <p className="text-[14px] text-text-muted uppercase tracking-widest">{item.role}</p>
                                </div>
                            </div>
                        </motion.div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default Testimonials;
