<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ActiveMail;
use App\Models\User;
use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class WebAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function login(Request $request)
    {
        $captcha = app(CaptchaService::class);

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        if ($captcha->isEnabled()) {
            $rules['captcha_token'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->withErrors($request, $validator->errors()->toArray());
        }

        if ($captcha->isEnabled() && !$captcha->verify($request->captcha_token ?? '', $request->ip())) {
            return $this->withErrors($request, ['captcha' => ['Xác minh bảo mật thất bại. Vui lòng thử lại.']]);
        }

        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();

        if (!$user || !Auth::attempt($credentials, $request->boolean('remember'))) {
            return $this->withErrors($request, ['email' => ['Tài khoản hoặc mật khẩu chưa đúng']]);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return $this->withErrors($request, ['email' => ['Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email.']]);
        }

        $request->session()->regenerate(true);
        session(['auth_user_id' => $user->id]);

        $role = $user->role;
        $redirectTo = match ($role) {
            'admin', 'seller', 'employee' => '/admin/agriverse',
            default => '/agriverse',
        };

        return redirect($redirectTo);
    }

    public function register(Request $request)
    {
        $captcha = app(CaptchaService::class);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
        if ($captcha->isEnabled()) {
            $rules['captcha_token'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->withErrors($request, $validator->errors()->toArray());
        }

        if ($captcha->isEnabled() && !$captcha->verify($request->captcha_token ?? '', $request->ip())) {
            return $this->withErrors($request, ['captcha' => ['Xác minh bảo mật thất bại. Vui lòng thử lại.']]);
        }

        $key = Str::random(200);
        $timeString = now()->toDateTimeString();
        $hash = md5($key . $timeString);

        Log::info("User Registration Hash for {$request->email}: {$hash}");

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'buyer',
            'is_active' => 1,
            'metadata' => [
                'activation_key' => $key,
                'key_time' => $timeString,
            ],
        ]);

        try {
            Mail::to($user->email)->send(new ActiveMail($user->email, $hash, $user->name));
        } catch (\Exception $e) {
            Log::warning('Failed to send activation email: ' . $e->getMessage());
        }

        Auth::login($user, true);
        $request->session()->regenerate(true);
        session(['auth_user_id' => $user->id]);

        return redirect('/agriverse');
    }

    private function withErrors(Request $request, array $errors)
    {
        if ($request->header('X-Inertia')) {
            $view = str_contains($request->path(), 'login') ? 'Auth/Login' : 'Auth/Register';
            return Inertia::render($view, ['errors' => $errors]);
        }

        throw ValidationException::withMessages($errors);
    }
}
