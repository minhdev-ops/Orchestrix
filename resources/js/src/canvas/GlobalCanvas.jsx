import React, { useRef, Suspense, useMemo } from 'react';
import { Canvas, useFrame, useThree } from '@react-three/fiber';
import { Float, MeshDistortMaterial, Sphere, Stars, PerspectiveCamera, Environment, Float as DreiFloat } from '@react-three/drei';
import * as THREE from 'three';

const AnimatedShape = ({ position, size, color, speed, distort, scrollY }) => {
    const meshRef = useRef();

    useFrame((state) => {
        const time = state.clock.getElapsedTime();
        if (meshRef.current) {
            // Base rotation
            meshRef.current.rotation.x = Math.sin(time / 4) * 0.2;
            meshRef.current.rotation.y = Math.cos(time / 4) * 0.2;
            
            // Scroll reaction: move up/down slightly and rotate
            const targetY = position[1] + (window.scrollY * 0.005);
            meshRef.current.position.y = THREE.MathUtils.lerp(meshRef.current.position.y, targetY, 0.1);
            meshRef.current.rotation.z = window.scrollY * 0.001;
        }
    });

    return (
        <DreiFloat speed={speed} rotationIntensity={2} floatIntensity={2}>
            <Sphere ref={meshRef} args={[size, 64, 64]} position={position}>
                <MeshDistortMaterial
                    color={color}
                    speed={speed}
                    distort={distort}
                    radius={size}
                    metalness={0.4}
                    roughness={0.3}
                    transparent
                    opacity={0.2}
                />
            </Sphere>
        </DreiFloat>
    );
};

const BackgroundElements = () => {
    const { camera } = useThree();
    
    useFrame(() => {
        // Subtle camera tilt on scroll
        camera.position.y = -(window.scrollY * 0.002);
    });

    return (
        <>
            <Stars radius={100} depth={50} count={5000} factor={4} saturation={0} fade speed={1} />
            <fog attach="fog" args={['#f5f7fa', 5, 25]} />
        </>
    );
};

const GlobalCanvas = () => {
    return (
        <div className="fixed inset-0 z-0 bg-gradient-to-br from-[#f5f7fa] to-[#c3cfe2] pointer-events-none">
            <Canvas dpr={[1, 2]}>
                <PerspectiveCamera makeDefault position={[0, 0, 15]} fov={50} />
                <ambientLight intensity={1.2} />
                <pointLight position={[10, 10, 10]} intensity={1.5} color="#0052ff" />
                <pointLight position={[-10, -10, -10]} intensity={0.8} color="#4f46e5" />
                <spotLight position={[0, 20, 0]} intensity={2} angle={0.3} penumbra={1} castShadow />

                <Suspense fallback={null}>
                    <BackgroundElements />
                    
                    {/* Main Morphing Shape - Much smaller and more subtle */}
                    <AnimatedShape
                        position={[5, 2, 0]}
                        size={2.5}
                        color="#e2e8f0"
                        speed={0.8}
                        distort={0.3}
                    />

                    {/* Subtle Drifting Accents */}
                    <AnimatedShape
                        position={[-10, 8, -5]}
                        size={1.2}
                        color="#0052ff"
                        speed={1.2}
                        distort={0.4}
                    />
                    <AnimatedShape
                        position={[12, -8, -10]}
                        size={1.8}
                        color="#f8fafc"
                        speed={1.5}
                        distort={0.2}
                    />
                    <AnimatedShape
                        position={[-15, -20, -15]}
                        size={2}
                        color="#e2e8f0"
                        speed={1}
                        distort={0.3}
                    />
                    <AnimatedShape
                        position={[18, -35, -20]}
                        size={1.5}
                        color="#0052ff"
                        speed={1.8}
                        distort={0.5}
                    />

                    <Environment preset="city" />
                </Suspense>
            </Canvas>
        </div>
    );
};

export default React.memo(GlobalCanvas);
