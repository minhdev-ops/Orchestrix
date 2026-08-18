<?php

namespace App\Http\Middleware;

use App\Modules\AgriVerse\Models\Cart;
use App\Modules\AgriVerse\Models\Store;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        $token = session('api_token');
        $tokenUserId = session('api_token_user_id');

        if ($user && $token && $tokenUserId && $tokenUserId !== $user->id) {
            session()->forget('api_token');
            session()->forget('api_token_user_id');
            $token = null;
        }

        if (! $token && $user) {
            try {
                $tokenResult = $user->createToken('inertia');
                $token = $tokenResult->accessToken;
                session(['api_token' => $token, 'api_token_user_id' => $user->id]);
            } catch (\Exception $e) {
                $token = null;
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'is_seller' => $user->role === 'seller',
                    'phone' => $user->phone,
                    'seller_verified_at' => $user->seller_verified_at,
                ] : null,
                'api_token' => $token,
            ],
            'cartCount' => $user
                ? Cart::where('user_id', $user->id)->count()
                : Cart::where('session_id', session()->getId())->whereNull('user_id')->count(),
            'store' => $user && $user->role === 'seller'
                ? Store::where('owner_id', $user->id)->first()
                : null,
        ];
    }
}
