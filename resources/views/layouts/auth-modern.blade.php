<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'System Access') | Orchestrix Intelligence</title>

    <!-- Premium Typography Stack -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Plus+Jakarta+Sans:wght@200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    <style>
        :root {
            --quantum-void: #09090B;
            --deep-surface: #121214;
            --electric-cyan: #4CD7F6;
            --steel-mist: #71717A;
            --ghost-border: rgba(76, 215, 246, 0.1);
            --pure-white: #FAFAFA;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--quantum-void);
            color: var(--steel-mist);
            margin: 0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .font-display { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* Dynamic Mesh & Grid */
        .system-background {
            position: fixed;
            inset: 0;
            z-index: -1;
            background: radial-gradient(circle at 0% 0%, rgba(76, 215, 246, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 100% 100%, rgba(99, 102, 241, 0.05) 0%, transparent 50%);
        }

        .dot-matrix {
            position: fixed;
            inset: 0;
            z-index: -1;
            background-image: radial-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 0);
            background-size: 32px 32px;
            mask-image: radial-gradient(ellipse at center, black, transparent 90%);
        }

        /* Glassmorphism Card */
        .quantum-panel {
            background: rgba(18, 18, 20, 0.7);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid var(--ghost-border);
            box-shadow: 0 0 80px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .quantum-panel::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, var(--electric-cyan), transparent);
            opacity: 0.2;
        }

        /* High-Fidelity Inputs */
        .input-sequence {
            background: rgba(9, 9, 11, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--pure-white);
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 4px;
        }

        .input-sequence:focus {
            border-color: var(--electric-cyan);
            background: rgba(9, 9, 11, 0.7);
            box-shadow: 0 0 30px rgba(76, 215, 246, 0.1);
            outline: none;
        }

        /* Tactical Button */
        .btn-authorize {
            background: var(--pure-white);
            color: var(--quantum-void);
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-radius: 4px;
            transition: all 0.4s cubic-bezier(0.19, 1, 0.22, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-authorize:hover {
            background: var(--electric-cyan);
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(76, 215, 246, 0.3);
        }

        .btn-social-tactical {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--pure-white);
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .btn-social-tactical:hover {
            background: rgba(255, 255, 255, 0.03);
            border-color: var(--electric-cyan);
            color: var(--electric-cyan);
        }

        /* Scanning Animation */
        @keyframes scan {
            0% { transform: translateY(-100%); opacity: 0; }
            50% { opacity: 0.5; }
            100% { transform: translateY(100%); opacity: 0; }
        }

        .scan-line {
            position: absolute;
            left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, var(--electric-cyan), transparent);
            animation: scan 4s linear infinite;
            pointer-events: none;
            z-index: 20;
        }
    </style>
</head>

<body class="antialiased min-h-[100dvh] flex items-center justify-center p-6">
    <div class="system-background"></div>
    <div class="dot-matrix"></div>

    {{-- Asymmetric Technical Margin Info --}}
    <div class="fixed top-8 left-8 font-mono text-[10px] tracking-[0.2em] opacity-20 hidden md:block">
        CORE_SYS: AUTH_GATEWAY // NODE: {{ request()->ip() }}
    </div>
    <div class="fixed bottom-8 right-8 font-mono text-[10px] tracking-[0.2em] opacity-20 hidden md:block">
        ENCRYPTION: AES_256_GCM // STATUS: SECURE
    </div>

    <main class="w-full max-w-[1000px] grid md:grid-cols-2 quantum-panel rounded-2xl overflow-hidden shadow-2xl">
        {{-- Left Side: Visual Technical Atmosphere --}}
        <div class="hidden md:flex flex-col justify-between p-12 bg-black/40 relative overflow-hidden border-r border-white/5">
            <div class="scan-line"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-16">
                    <div class="w-10 h-10 bg-white flex items-center justify-center text-black font-black rounded-sm text-lg">OX</div>
                    <div class="h-6 w-[1px] bg-white/10"></div>
                    <span class="font-display font-black text-white uppercase tracking-[0.3em] text-sm">Orchestrix</span>
                </div>

                <div class="space-y-4">
                    <h3 class="font-display text-4xl font-black text-white leading-tight uppercase">
                        Quantum<br/>
                        <span class="text-electric-cyan">Encryption</span><br/>
                        Layer
                    </h3>
                    <p class="text-steel-mist text-xs leading-relaxed max-w-[280px] font-medium">
                        Access the centralized orchestration engine. Secure, modular, and high-performance neural architecture.
                    </p>
                </div>
            </div>

            <div class="relative z-10 font-mono text-[9px] uppercase tracking-[0.4em] opacity-40">
                &gt; INITIALIZING_AUTH_SEQUENCE_v1.2.0
            </div>
            
            {{-- Abstract Tech Visual (CSS only) --}}
            <div class="absolute -right-20 top-1/2 -translate-y-1/2 opacity-10">
                <div class="w-96 h-96 border-[40px] border-electric-cyan rounded-full"></div>
                <div class="w-80 h-80 border-[20px] border-white absolute top-8 left-8 rounded-full"></div>
            </div>
        </div>

        {{-- Right Side: Actual Content Area --}}
        <div class="p-8 md:p-12 flex flex-col justify-center">
            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>

</html>