<section id="home"
    class="relative min-h-[90vh] w-full overflow-hidden flex flex-col justify-center px-6 bg-white dark:bg-[#0a0b0d]">
    {{-- Three.js Canvas Container --}}
    <div id="hero-canvas-container" class="absolute inset-0 z-0 opacity-40 dark:opacity-20 pointer-events-none"></div>

    <div
        class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 items-center gap-12 lg:gap-24 relative z-10 w-full pt-20">
        {{-- Left Content: Typrographic Impact --}}
        <div class="lg:col-span-12 xl:col-span-8 space-y-12">
            <div class="space-y-8">
                <div class="inline-flex items-center gap-2 group cursor-default">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0052ff] animate-pulse"></span>
                    <span class="text-xs font-bold tracking-widest text-[#0052ff] uppercase">
                        Available for new ventures
                    </span>
                </div>
                <h1 class="text-[64px] md:text-[100px] font-display text-[#0a0b0d] dark:text-white max-w-4xl">
                    Building the future <br>of digital systems.
                </h1>
            </div>

            <div class="max-w-2xl space-y-10">
                <p
                    class="text-[20px] md:text-[24px] text-[#5b616e] dark:text-gray-400 leading-relaxed font-coinbase-text">
                    {{ $heroSubtitle ?? 'Senior Software Architect specializing in high-availability infrastructure and distributed system architecture.' }}
                </p>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="{{ route('portfolio.projects.index') }}" class="btn-pill btn-primary min-w-[200px]">
                        Get started
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                    <a href="{{ route('portfolio.contact') }}" class="btn-pill btn-secondary min-w-[200px]">
                        Contact me
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const init = () => {
                    const container = document.getElementById('hero-canvas-container');
                    if (!container) return;

                    if (typeof THREE === 'undefined') {
                        console.error('Three.js library not loaded yet. Retrying...');
                        setTimeout(init, 500);
                        return;
                    }

                    console.log('Initializing Three.js Hero...');

                    const scene = new THREE.Scene();
                    const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
                    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });

                    renderer.setSize(container.clientWidth, container.clientHeight);
                    renderer.setPixelRatio(window.devicePixelRatio);
                    container.appendChild(renderer.domElement);

                    // Create a plane with wireframe for a "grid/network" look
                    const geometry = new THREE.PlaneGeometry(50, 50, 40, 40);
                    const material = new THREE.MeshBasicMaterial({
                        color: 0x0052ff,
                        wireframe: true,
                        transparent: true,
                        opacity: 0.15
                    });
                    const plane = new THREE.Mesh(geometry, material);

                    plane.rotation.x = -Math.PI / 2.5;
                    plane.position.y = -5;
                    scene.add(plane);

                    camera.position.z = 15;
                    camera.position.y = 5;

                    // Animation
                    const animate = function () {
                        requestAnimationFrame(animate);

                        plane.rotation.z += 0.001;

                        const time = Date.now() * 0.001;
                        const positionAttribute = geometry.getAttribute('position');

                        for (let i = 0; i < positionAttribute.count; i++) {
                            const x = positionAttribute.getX(i);
                            const y = positionAttribute.getY(i);
                            const z = Math.sin(x * 0.3 + time) * 1.2 + Math.cos(y * 0.3 + time) * 1.2;
                            positionAttribute.setZ(i, z);
                        }
                        positionAttribute.needsUpdate = true;

                        renderer.render(scene, camera);
                    };

                    animate();

                    // Resize handler
                    window.addEventListener('resize', () => {
                        camera.aspect = container.clientWidth / container.clientHeight;
                        camera.updateProjectionMatrix();
                        renderer.setSize(container.clientWidth, container.clientHeight);
                    });
                };

                if (document.readyState === 'complete') {
                    init();
                } else {
                    window.addEventListener('load', init);
                }
            })();
        </script>
    @endpush
</section>