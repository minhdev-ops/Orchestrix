import React, { Suspense, lazy } from 'react';
import { motion, AnimatePresence, useScroll, useSpring } from 'framer-motion';
import Navbar from './components/layout/Navbar';
import Footer from './components/layout/Footer';

// Lazy load heavy components
const Hero = lazy(() => import('./sections/Hero'));
const About = lazy(() => import('./sections/About'));
const Stack = lazy(() => import('./sections/Stack'));
const Projects = lazy(() => import('./sections/Projects'));
const Experience = lazy(() => import('./sections/Experience'));
const Testimonials = lazy(() => import('./sections/Testimonials'));
const Certifications = lazy(() => import('./sections/Certifications'));
const Architecture = lazy(() => import('./sections/Architecture'));
const Blog = lazy(() => import('./sections/Blog'));
const Contact = lazy(() => import('./sections/Contact'));
import CustomCursor from './components/ui/CustomCursor';
import GlobalCanvas from './canvas/GlobalCanvas';

const ScrollProgress = () => {
    const { scrollYProgress } = useScroll();
    const scaleX = useSpring(scrollYProgress, {
        stiffness: 100,
        damping: 30,
        restDelta: 0.001
    });

    return (
        <motion.div
            className="fixed top-0 left-0 right-0 h-1.5 bg-primary origin-left z-[100]"
            style={{ scaleX }}
        />
    );
};

const App = () => {
    return (
        <div className="min-h-screen bg-transparent font-sans text-[#1b1c1e] transition-colors duration-300 selection:bg-primary/20 relative">
            {/* Background Layer - Fixed and Memoized internally by Three.js/Fiber */}
            <GlobalCanvas />
            
            {/* Static Progress Bar - Doesn't trigger App re-render */}
            <ScrollProgress />

            {/* Premium Grain Texture Overlay */}
            <div className="fixed inset-0 pointer-events-none z-[9999] opacity-[0.02] mix-blend-overlay" 
                 style={{ backgroundImage: `url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E")` }} />

            <CustomCursor />
            <Navbar />

            <main className="relative z-10">
                <Suspense fallback={<div className="h-screen flex items-center justify-center">Loading...</div>}>
                    <AnimatePresence mode="wait">
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            transition={{ duration: 0.5 }}
                        >
                            <Hero />
                            <About />
                            <Stack />
                            <Projects />
                            <Experience />
                            <Certifications />
                            <Architecture />
                            <Testimonials />
                            <Blog />
                            <Contact />
                        </motion.div>
                    </AnimatePresence>
                </Suspense>
            </main>

            <Footer />
        </div>
    );
};

export default App;
