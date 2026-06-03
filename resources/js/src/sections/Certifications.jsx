import React from 'react';
import { motion } from 'framer-motion';

const CERTS = [
    {
        name: "AWS Certified Solutions Architect – Professional",
        issuer: "Amazon Web Services",
        date: "2024",
        icon: "cloud_done"
    },
    {
        name: "Certified Kubernetes Administrator (CKA)",
        issuer: "The Linux Foundation",
        date: "2023",
        icon: "verified"
    },
    {
        name: "HashiCorp Certified: Terraform Associate",
        issuer: "HashiCorp",
        date: "2023",
        icon: "architecture"
    }
];

const Certifications = () => {
    return (
        <section id="certs" className="relative py-20 px-6 bg-transparent overflow-hidden">
            <div className="max-w-7xl mx-auto">
                <div className="flex items-center gap-4 mb-12">
                    <div className="h-px flex-1 bg-border" />
                    <h2 className="text-[14px] font-bold text-text-muted uppercase tracking-[0.2em]">Chứng chỉ chuyên môn</h2>
                    <div className="h-px flex-1 bg-border" />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {CERTS.map((cert, index) => (
                        <motion.div
                            key={index}
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ delay: index * 0.1 }}
                            className="flex items-center gap-6 p-6 bg-surface-secondary/50 border border-border rounded-2xl hover:border-brand/40 transition-all group"
                        >
                            <div className="w-12 h-12 rounded-xl bg-background flex items-center justify-center border border-border group-hover:scale-110 transition-transform">
                                <span className="material-symbols-outlined text-brand text-[24px]">{cert.icon}</span>
                            </div>
                            <div>
                                <h4 className="text-[14px] font-bold text-text leading-tight mb-1">{cert.name}</h4>
                                <p className="text-[12px] text-text-muted uppercase tracking-wider">{cert.issuer} • {cert.date}</p>
                            </div>
                        </motion.div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default Certifications;
