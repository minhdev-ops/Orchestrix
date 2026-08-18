<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function login()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callBack()
    {
        try {
            $socialUser = Socialite::driver('google')->user();
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                Auth::login($user);
                Log::info('Login with Google success: '.$user->email);
            } else {
                $user = new User;
                $user->name = $socialUser->getName();
                $user->email = $socialUser->getEmail();
                $user->avatar = $socialUser->getAvatar();
                $user->is_active = true;
                $user->role = 'user';
                $user->save();
                Auth::login($user);
                Log::info('Register and Login with Google: '.$user->email);
            }

            return redirect('/');
        } catch (\Exception $e) {
            Log::error('Google login error: '.$e->getMessage());

            return redirect()->route('login');
        }
    }
}
