<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SocialAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function google(Request $request)
    {
        $request->validate([
            'credential' => 'required|string',
        ]);

        try {
            $response = Http::timeout(10)->get(
                'https://oauth2.googleapis.com/tokeninfo',
                ['id_token' => $request->credential]
            );

            if (!$response->successful()) {
                return $this->socialError('Xác thực Google thất bại');
            }

            $data = $response->json();
            $email = $data['email'] ?? null;
            $name = $data['name'] ?? null;
            $avatar = $data['picture'] ?? null;

            if (!$email) {
                return $this->socialError('Không thể lấy email từ Google');
            }

            return $this->findOrCreateUser($email, $name, $avatar, 'google');

        } catch (\Exception $e) {
            return $this->socialError('Lỗi kết nối Google: ' . $e->getMessage());
        }
    }

    public function facebook(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
            'user_id' => 'required|string',
            'name' => 'required|string',
            'email' => 'nullable|string|email',
            'picture' => 'nullable|string',
        ]);

        try {
            $email = $request->email ?? $request->user_id . '@facebook.user';
            $name = $request->name;
            $avatar = $request->picture;

            return $this->findOrCreateUser($email, $name, $avatar, 'facebook');

        } catch (\Exception $e) {
            return $this->socialError('Lỗi kết nối Facebook: ' . $e->getMessage());
        }
    }

    private function findOrCreateUser(string $email, ?string $name, ?string $avatar, string $provider)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $name ?? 'Người dùng ' . $provider,
                'email' => $email,
                'password' => bcrypt(\Illuminate\Support\Str::random(32)),
                'avatar' => $avatar,
                'is_active' => 1,
                'role' => 'buyer',
                'provider' => $provider,
                'metadata' => [
                    'social_provider' => $provider,
                    'registered_via' => 'social',
                ],
            ]);
        } else {
            if (!$user->is_active) {
                return $this->socialError('Tài khoản chưa được kích hoạt');
            }
            if ($avatar && !$user->avatar) {
                $user->update(['avatar' => $avatar]);
            }
        }

        Auth::login($user, true);
        $request = request();
        $request->session()->regenerate(true);
        session(['auth_user_id' => $user->id]);

        return redirect()->intended('/agriverse');
    }

    private function socialError(string $message)
    {
        return redirect()->route('login')->withErrors([
            'social' => $message,
        ]);
    }
}
