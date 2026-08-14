<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\AgriVerse\Models\Cart;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected function redirectTo()
    {
        if (Auth::check()) {
            $user = Auth::user();

            return match ($user->role) {
                'admin' => '/admin/agriverse',
                'seller' => '/admin/agriverse',
                'employee' => '/admin/agriverse',
                default => '/agriverse',
            };
        }

        return '/agriverse';
    }

    protected function authenticated(Request $request, $user)
    {
        $sessionId = session()->getId();

        DB::transaction(function () use ($user, $sessionId) {
            $guestItems = Cart::where('session_id', $sessionId)->whereNull('user_id')->get();

            foreach ($guestItems as $guestItem) {
                $existing = Cart::withTrashed()
                    ->where('user_id', $user->id)
                    ->where('product_id', $guestItem->product_id)
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                        $existing->increment('quantity', $guestItem->quantity);
                    } else {
                        $existing->increment('quantity', $guestItem->quantity);
                    }
                    $guestItem->delete();
                } else {
                    $guestItem->update(['user_id' => $user->id, 'session_id' => null]);
                }
            }
        });

        try {
            $token = $user->createToken('web')->accessToken;
            session()->flash('api_token', $token);
        } catch (\Exception $e) {
            // Passport may not be configured for this user
        }
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            $error = 'Tài khoản hoặc mật khẩu chưa đúng';
        } elseif (! $user->is_active) {
            $error = 'Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email.';
        } else {
            $error = 'Thông tin đăng nhập không đúng';
        }

        if ($request->header('X-Inertia')) {
            return Inertia::render('Auth/Login', [
                'errors' => [
                    'email' => [$error],
                ],
            ]);
        }

        throw ValidationException::withMessages([
            $this->username() => [$error],
        ]);
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
