<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends Controller
{
    public function checkGoogle(Request $r)
    {
        $token = $r->token ?? '';
        try {
            $response = Http::get("https://www.googleapis.com/oauth2/v3/tokeninfo", [
                'id_token' => $token,
            ]);
            $responseGoogle = $response->json();
            $email = $responseGoogle['email'];
            if (! User::where('email', $email)->exists()) {
                $user = new User;
                $user->name = $responseGoogle['name'];
                $user->email = $email;
                $user->avatar = $responseGoogle['picture'];
                $user->is_active = true;
                $user->role = 'user';
                $user->save();
            }
            $user = User::where('email', $email)->first();
            if (! $user) {
                return response()->json([
                    'status' => 400,
                    'error' => 'Không tìm thấy tài khoản',
                ]);
            }
            if (! $user->is_active) {
                Log::warning('MOBILE APP - LOGIN: Account locked - '.$email);

                return response()->json([
                    'status' => 400,
                    'error' => 'Tài khoản đang tạm khóa, Vui lòng liên hệ Admin để được hỗ trợ',
                ]);
            }
            $tokenResult = $user->createToken($user->id.'-'.now());
            Log::info('ANDROID APP - LOGIN success: '.$email);

            return response()->json([
                'status' => 200,
                'token' => $tokenResult->accessToken,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('MOBILE APP - LOGIN: Token Invalid - '.$e->getMessage());

            return response()->json([
                'status' => 404,
                'error' => 'Token không hợp lệ',
            ]);
        }
    }

    public function checkFacebook(Request $r)
    {
        $accessToken = $r->token ?? '';

        if (! $accessToken) {
            return response()->json([
                'status' => 400,
                'error' => 'Không có access token gửi lên',
            ]);
        }

        try {
            $exchange = Http::get('https://graph.facebook.com/oauth/access_token', [
                'grant_type' => 'fb_exchange_token',
                'client_id' => Config::get('services.facebook.app_id'),
                'client_secret' => Config::get('services.facebook.app_secret'),
                'fb_exchange_token' => $accessToken,
            ]);

            $longToken = $exchange->json()['access_token'] ?? $accessToken;

            $response = Http::get('https://graph.facebook.com/v24.0/me', [
                'fields' => 'id,name,email,picture',
                'access_token' => $longToken,
            ]);

            $data = $response->json();
            Log::info('FACEBOOK_DATA: '.json_encode($data));

            $facebookId = $data['id'] ?? null;
            $name = $data['name'] ?? 'Facebook User';
            $email = $data['email'] ?? "{$facebookId}@facebook.callparts";
            $avatar = $data['picture']['data']['url'] ?? null;

            $user = User::where('fb', $facebookId)->first();
            if (! $user) {
                $user = new User;
                $user->email = $email;
                $user->name = $name;
                $user->fb = $facebookId;
                $user->avatar = $avatar;
                $user->is_active = true;
                $user->role = 'user';
                $user->save();
            }

            if (! $user->is_active) {
                Log::warning('MOBILE APP - LOGIN: Account locked - '.$email);

                return response()->json([
                    'status' => 400,
                    'error' => 'Tài khoạn đang tạm khóa, Vui lòng liên hệ Admin để được hỗ trợ',
                ]);
            }

            $tokenResult = $user->createToken($user->id.'-'.now());

            return response()->json([
                'status' => 200,
                'token' => $tokenResult->accessToken,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Facebook API Error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
