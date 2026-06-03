@extends('layouts.auth-light')

@section('title', 'Login')

@section('content')
    <div class="space-y-8">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight font-display">Welcome back</h2>
            <p class="text-slate-500 text-sm mt-1">Please enter your details to sign in.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="email" class="text-xs font-bold text-slate-500 ml-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full h-12 px-4 input-clean @error('email') border-red-300 bg-red-50/10 @enderror" 
                    placeholder="name@company.com">
                @error('email')
                    <span class="text-red-500 text-[10px] font-bold ml-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-2">
                <div class="flex justify-between items-center ml-1">
                    <label for="password" class="text-xs font-bold text-slate-500">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors">
                            Forgot?
                        </a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required
                    class="w-full h-12 px-4 input-clean @error('password') border-red-300 bg-red-50/10 @enderror"
                    placeholder="••••••••">
                @error('password')
                    <span class="text-red-500 text-[10px] font-bold ml-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center ml-1">
                <input class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="ml-2.5 text-sm text-slate-600 cursor-pointer" for="remember">
                    Stay signed in
                </label>
            </div>

            <button type="submit" class="w-full h-12 btn-action text-sm">
                Sign In
            </button>
        </form>

        <div class="relative py-2">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
            <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-4 text-slate-400 font-bold tracking-widest">or continue with</span></div>
        </div>

        {{-- Social Login - Below the Main Button as requested --}}
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ url('api/login/google') }}" class="btn-social-outline h-11 flex items-center justify-center gap-3">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                </svg>
                <span>Google</span>
            </a>
            <a href="{{ url('api/login/facebook') }}" class="btn-social-outline h-11 flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24">
                    <path d="M12 2.04c-5.5 0-10 4.49-10 10.02 0 5 3.66 9.15 8.44 9.9v-7h-2.54v-2.9h2.54V9.82c0-2.51 1.49-3.89 3.65-3.89 1.04 0 2.12.19 2.12.19v2.33h-1.19c-1.25 0-1.63.77-1.63 1.57v1.89h2.62l-.42 2.9h-2.2v7c4.78-.75 8.44-4.9 8.44-9.9 0-5.53-4.5-10.02-10-10.02z" />
                </svg>
                <span>Facebook</span>
            </a>
        </div>

        <p class="text-center text-sm text-slate-500">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-700 transition-colors ml-1">Sign up</a>
        </p>
    </div>
@endsection