import React from 'react';
import { motion } from 'framer-motion';

const EXPERIENCES = [
    {
        company: "Global Tech Solutions",
        role: "Senior Solutions Architect",
        period: "2022 - Hiện tại",
        description: "Thiết kế và triển khai hạ tầng cloud-native cho các hệ thống tài chính quy mô lớn. Tối ưu hóa chi phí AWS lên đến 30%."
    },
    {
        company: "Innovate AI",
        role: "DevOps Lead",
        period: "2020 - 2022",
        description: "Xây dựng pipeline CI/CD tự động hóa hoàn toàn quy trình release. Triển khai kiến trúc microservices trên Kubernetes."
    },
    {
        company: "Digital Vanguard",
        role: "Fullstack Engineer",
        period: "2018 - 2020",
        description: "Phát triển các ứng dụng web hiệu năng cao sử dụng React và Laravel. Quản lý hệ thống cơ sở dữ liệu phân tán."
    }
];

const Experience = () => {
    return (
        <section id="experience" className="relative py-32 px-6 bg-transparent overflow-hidden">
            <div className="max-w-7xl mx-auto">
                <div className="flex flex-col md:flex-row justify-between items-end mb-24 gap-8">
                    <motion.div
                        initial={{ opacity: 0, x: -20 }}
                        whileInView={{ opacity: 1, x: 0 }}
                        viewport={{ once: true }}
                        className="space-y-6"
                    >
                        <h2 className="text-[48px] md:text-[64px] font-display font-medium tracking-tight leading-[1.0]">
                            Hành trình <br /> kinh nghiệm.
                        </h2>
                        <p className="text-text-muted text-[18px] max-w-xl leading-relaxed">
                            Quá trình tích lũy kiến thức và xây dựng những hệ thống
                            mang tính đột phá qua các giai đoạn sự nghiệp.
                        </p>
                    </motion.div>
                </div>

                <div className="space-y-12">
                    {EXPERIENCES.map((exp, index) => (
                        <motion.div
                            key={index}
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ delay: index * 0.1 }}
                            className="group relative grid grid-cols-1 md:grid-cols-12 gap-8 py-12 border-b border-border hover:border-brand/40 transition-colors"
                        >
                            <div className="md:col-span-3">
                                <span className="text-[14px] font-bold text-brand uppercase tracking-widest">{exp.period}</span>
                            </div>
                            <div className="md:col-span-4">
                                <h3 className="text-[24px] font-display font-medium text-text">{exp.role}</h3>
                                <p className="text-[18px] text-text-muted font-medium">{exp.company}</p>
                            </div>
                            <div className="md:col-span-5">
                                <p className="text-[16px] text-text-muted leading-relaxed">
                                    {exp.description}
                                </p>
                            </div>
                        </motion.div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default Experience;
