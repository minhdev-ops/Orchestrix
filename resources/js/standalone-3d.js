import * as THREE from 'three';

export function initStandalone3D(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(window.innerWidth, window.innerHeight);

    const scene = new THREE.Scene();

    const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 100);
    camera.position.set(0, 6, 12);
    camera.lookAt(0, 0, 0);

    const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
    scene.add(ambientLight);

    const pointLight = new THREE.PointLight(0x0052ff, 1.5);
    pointLight.position.set(10, 10, 10);
    scene.add(pointLight);

    // Wave Particles
    const count = 100;
    const particlesCount = count * count;
    const positions = new Float32Array(particlesCount * 3);

    for (let i = 0; i < count; i++) {
        for (let j = 0; j < count; j++) {
            const index = (i * count + j) * 3;
            positions[index] = (i - count / 2) * 0.18;
            positions[index + 1] = 0;
            positions[index + 2] = (j - count / 2) * 0.18;
        }
    }

    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

    const material = new THREE.PointsMaterial({
        size: 0.04,
        color: 0x0052ff,
        transparent: true,
        opacity: 0.3,
        sizeAttenuation: true,
        blending: THREE.AdditiveBlending,
    });

    const points = new THREE.Points(geometry, material);
    points.rotation.x = -Math.PI / 6;
    scene.add(points);

    // Resize handler
    const onResize = () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    };
    window.addEventListener('resize', onResize);

    // Animation
    const clock = new THREE.Clock();
    const animate = () => {
        const time = clock.getElapsedTime();
        const pArray = geometry.attributes.position.array;

        for (let i = 0; i < count; i++) {
            for (let j = 0; j < count; j++) {
                const index = (i * count + j) * 3;
                const x = (i - count / 2) * 0.18;
                const z = (j - count / 2) * 0.18;

                // Simple wave math
                let y = Math.sin(x * 0.5 + time) * 0.5 + Math.cos(z * 0.5 + time) * 0.5;
                y += Math.sin((x + z) * 0.2 + time * 1.2) * 0.2;

                pArray[index + 1] = y;
            }
        }
        geometry.attributes.position.needsUpdate = true;

        renderer.render(scene, camera);
        requestAnimationFrame(animate);
    };

    animate();

    return {
        destroy: () => {
            window.removeEventListener('resize', onResize);
            geometry.dispose();
            material.dispose();
            renderer.dispose();
        }
    };
}
