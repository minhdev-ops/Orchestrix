import React from 'react';
import { motion } from 'framer-motion';

const AuthAtmosphere = () => {
    return (
        <div className="absolute inset-0 overflow-hidden pointer-events-none">
            {/* Clean Geometric Accents */}
            <motion.div
                initial={{ opacity: 0, scale: 0.8 }}
                animate={{ opacity: 0.1, scale: 1 }}
                transition={{ duration: 2, ease: "easeOut" }}
                className="absolute top-[10%] right-[10%] w-[30vw] h-[30vw] border border-blue-500 rounded-full"
            />
            
            <motion.div
                animate={{
                    y: [0, -20, 0],
                }}
                transition={{
                    duration: 10,
                    repeat: Infinity,
                    ease: "easeInOut"
                }}
                className="absolute top-20 left-[15%] w-64 h-64 bg-blue-50 blur-[120px] rounded-full"
            />

            <motion.div
                animate={{
                    y: [0, 20, 0],
                }}
                transition={{
                    duration: 12,
                    repeat: Infinity,
                    ease: "easeInOut"
                }}
                className="absolute bottom-20 right-[15%] w-80 h-80 bg-slate-100 blur-[140px] rounded-full"
            />
            
            {/* Floating particles */}
            {[...Array(5)].map((_, i) => (
                <motion.div
                    key={i}
                    animate={{
                        y: [-20, 20, -20],
                        opacity: [0.2, 0.5, 0.2]
                    }}
                    transition={{
                        duration: 5 + i,
                        repeat: Infinity,
                        ease: "linear"
                    }}
                    style={{
                        left: `${20 * i}%`,
                        top: `${15 * i}%`,
                    }}
                    className="absolute w-1 h-1 bg-blue-400 rounded-full"
                />
            ))}
        </div>
    );
};

export default AuthAtmosphere;