import React from 'react';
import { motion } from 'framer-motion';

const Contact = () => {
    const [formData, setFormData] = React.useState({
        name: '',
        email: '',
        message: ''
    });
    const [status, setStatus] = React.useState({ type: '', message: '' });
    const [submitting, setSubmitting] = React.useState(false);

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        setStatus({ type: '', message: '' });

        try {
            const response = await fetch('/portfolio/contact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok) {
                setStatus({ type: 'success', message: data.message });
                setFormData({ name: '', email: '', message: '' });
            } else {
                setStatus({ type: 'error', message: data.message || 'Đã có lỗi xảy ra. Vui lòng thử lại.' });
            }
        } catch (err) {
            setStatus({ type: 'error', message: 'Không thể kết nối đến máy chủ.' });
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <section id="signals" className="relative py-32 px-6 bg-transparent text-[#1b1c1e]">
            <div className="max-w-7xl mx-auto relative z-10">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-20">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        className="space-y-8"
                    >
                        <span className="text-[14px] font-bold text-primary uppercase tracking-[0.2em]">Liên hệ</span>
                        <h2 className="text-[48px] md:text-[64px] font-display font-medium tracking-tight leading-[1.0]">
                            Khởi tạo <br />kết nối.
                        </h2>
                        <p className="text-gray-500 text-[20px] max-w-lg leading-relaxed">
                            Bạn đang tìm cách tích hợp các hệ thống phức tạp
                            hoặc kiểm định bảo mật đám mây? Hãy thiết lập một
                            kết nối ngay hôm nay.
                        </p>

                        <div className="space-y-6 pt-4">
                            <div className="flex items-center gap-4 text-primary">
                                <span className="material-symbols-outlined text-[32px]">mail</span>
                                <span className="text-[20px] font-bold">architect@orchestrix.dev</span>
                            </div>
                            <div className="flex items-center gap-4 text-gray-400">
                                <span className="material-symbols-outlined">location_on</span>
                                <span className="text-[18px]">Toàn cầu // Distributed Infrastructure</span>
                            </div>
                        </div>
                    </motion.div>

                    <motion.div
                        initial={{ opacity: 0, x: 20 }}
                        whileInView={{ opacity: 1, x: 0 }}
                        viewport={{ once: true }}
                        className="bg-white/40 backdrop-blur-xl border border-white/40 rounded-[32px] p-10 space-y-8 shadow-2xl shadow-blue-500/5"
                    >
                        <form onSubmit={handleSubmit} className="space-y-8">
                            {status.message && (
                                <div className={`p-4 rounded-xl text-[14px] font-bold ${status.type === 'success' ? 'bg-green-500/10 text-green-600' : 'bg-red-500/10 text-red-600'}`}>
                                    {status.message}
                                </div>
                            )}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <label className="text-[12px] font-bold uppercase tracking-widest text-gray-400">Họ tên</label>
                                    <input 
                                        name="name"
                                        value={formData.name}
                                        onChange={handleChange}
                                        required
                                        type="text" 
                                        className="w-full bg-white/50 border border-gray-100 rounded-xl px-6 py-4 outline-none focus:border-primary/50 transition-all text-[#1b1c1e] placeholder:text-gray-300 shadow-sm" 
                                        placeholder="Tên của bạn" 
                                    />
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[12px] font-bold uppercase tracking-widest text-gray-400">Email</label>
                                    <input 
                                        name="email"
                                        value={formData.email}
                                        onChange={handleChange}
                                        required
                                        type="email" 
                                        className="w-full bg-white/50 border border-gray-100 rounded-xl px-6 py-4 outline-none focus:border-primary/50 transition-all text-[#1b1c1e] placeholder:text-gray-300 shadow-sm" 
                                        placeholder="Địa chỉ kết nối" 
                                    />
                                </div>
                            </div>
                            <div className="space-y-2">
                                <label className="text-[12px] font-bold uppercase tracking-widest text-gray-400">Nội dung</label>
                                <textarea 
                                    name="message"
                                    value={formData.message}
                                    onChange={handleChange}
                                    required
                                    rows="4" 
                                    className="w-full bg-white/50 border border-gray-100 rounded-xl px-6 py-4 outline-none focus:border-primary/50 transition-all resize-none text-[#1b1c1e] placeholder:text-gray-300 shadow-sm" 
                                    placeholder="Tôi có thể giúp gì cho bạn?..."
                                ></textarea>
                            </div>
                            <button 
                                type="submit"
                                disabled={submitting}
                                className="w-full py-5 bg-primary text-white rounded-full text-[18px] font-bold hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                {submitting ? (
                                    <>
                                        <span className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                        Đang gửi...
                                    </>
                                ) : 'Gửi yêu cầu'}
                            </button>
                        </form>
                    </motion.div>
                </div>
            </div>
        </section>
    );
};

export default Contact;
