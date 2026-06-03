@extends('layouts.auth-light')

@section('title', 'Register')

@section('content')
    <div class="space-y-8">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight font-display">Create account</h2>
            <p class="text-slate-500 text-sm mt-1">Start your orchestration journey today.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="name" class="text-xs font-bold text-slate-500 ml-1">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full h-12 px-4 input-clean @error('name') border-red-300 bg-red-50/10 @enderror" 
                    placeholder="Enter your name">
                @error('name')
                    <span class="text-red-500 text-[10px] font-bold ml-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="email" class="text-xs font-bold text-slate-500 ml-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="w-full h-12 px-4 input-clean @error('email') border-red-300 bg-red-50/10 @enderror" 
                    placeholder="name@company.com">
                @error('email')
                    <span class="text-red-500 text-[10px] font-bold ml-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="password" class="text-xs font-bold text-slate-500 ml-1">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full h-12 px-4 input-clean @error('password') border-red-300 bg-red-50/10 @enderror"
                        placeholder="••••••••">
                </div>
                <div class="space-y-2">
                    <label for="password-confirm" class="text-xs font-bold text-slate-500 ml-1">Confirm</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required
                        class="w-full h-12 px-4 input-clean"
                        placeholder="••••••••">
                </div>
            </div>
            @error('password')
                <span class="text-red-500 text-[10px] font-bold ml-1 block">{{ $message }}</span>
            @enderror

            <button type="submit" class="w-full h-12 btn-action text-sm mt-2">
                Create Account
            </button>
        </form>

        <p class="text-center text-sm text-slate-500">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-700 transition-colors ml-1">Sign in</a>
        </p>
    </div>
@endsection