@extends('layouts.auth-light')

@section('title', 'Reset Password')

@section('content')
    <div class="space-y-8">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight font-display">Forgot password?</h2>
            <p class="text-slate-500 text-sm mt-1">Enter your email to receive a reset link.</p>
        </div>

        @if (session('status'))
            <div class="p-3 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="email" class="text-xs font-bold text-slate-500 ml-1">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full h-12 px-4 input-clean @error('email') border-red-300 bg-red-50/10 @enderror" 
                    placeholder="name@company.com">
                @error('email')
                    <span class="text-red-500 text-[10px] font-bold ml-1">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="w-full h-12 btn-action text-sm">
                Send Reset Link
            </button>
        </form>

        <p class="text-center text-sm text-slate-500">
            <a href="{{ route('login') }}" class="font-bold text-slate-900 hover:text-blue-600 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Back to sign in
            </a>
        </p>
    </div>
@endsection