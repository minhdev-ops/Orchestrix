<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Access') | Orchestrix</title>

    <!-- Professional Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-body: #FDFDFF;
            --surface: #FFFFFF;
            --accent: #2563EB; /* Sapphire Blue - Modern & Trustworthy */
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --border-soft: #F1F5F9;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-primary);
            margin: 0;
            letter-spacing: -0.01em;
        }

        .font-display { font-family: 'Outfit', sans-serif; }

        /* Minimalist Tech Background */
        .tech-grid {
            position: fixed;
            inset: 0;
            z-index: -1;
            background-image: radial-gradient(#2563EB 0.5px, transparent 0);
            background-size: 40px 40px;
            opacity: 0.05;
        }

        .auth-card-clean {
            background: var(--surface);
            border: 1px solid #E2E8F0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 20px 50px -12px rgba(0, 0, 0, 0.03);
        }

        .input-clean {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            color: var(--text-primary);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .input-clean:focus {
            background: #FFFFFF;
            border-color: var(--accent);
            box-shadow: 0 0 0 1px var(--accent);
            outline: none;
        }

        .btn-action {
            background: var(--text-primary);
            color: white;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-action:hover {
            background: #1E293B;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
        }

        .btn-social-outline {
            border: 1px solid #E2E8F0;
            color: var(--text-secondary);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-social-outline:hover {
            background: #F8FAFC;
            border-color: #CBD5E1;
            color: var(--text-primary);
        }
    </style>
</head>

<body class="antialiased min-h-screen flex items-center justify-center p-6 bg-[#FDFDFF]">
    <div class="tech-grid"></div>
    
    {{-- Auth Atmosphere Vue background --}}
    <div id="auth-atmosphere-root" class="fixed inset-0 pointer-events-none z-[-1]"></div>

    <main class="w-full max-w-[420px] relative">
        {{-- Clean Logo --}}
        <div class="flex justify-center mb-10">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-900 rounded-lg flex items-center justify-center text-white font-black">OX</div>
                <span class="font-display font-black text-xl tracking-tight text-slate-900 uppercase">Orchestrix</span>
            </a>
        </div>

        {{-- Auth Card --}}
        <div class="auth-card-clean rounded-2xl p-8 md:p-10">
            @yield('content')
        </div>

        {{-- Minimal Footer --}}
        <div class="mt-12 text-center opacity-30 font-bold text-[10px] uppercase tracking-[0.2em] text-slate-500">
            &copy; 2026 Orchestrix Engineering
        </div>
    </main>

    @yield('scripts')
</body>

</html>