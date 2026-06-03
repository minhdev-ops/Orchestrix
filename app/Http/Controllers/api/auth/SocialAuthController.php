<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Models\Logging;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SocialAuthController extends Controller {
    private Client $client;

    public function __construct() {
        $this->client = new Client();
    }

    public function checkGoogle( Request $r ) {
        $token = $r->token ?? "";
        try {
            $checkToken     = $this->client->get( "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=$token" );
            $responseGoogle = json_decode( $checkToken->getBody()->getContents(), true );
            $email          = $responseGoogle['email'];
            if ( ! User::where( 'email', $email )->exists() ) {
                $user         = new User();
                $user->name   = $responseGoogle['name'];
                $user->email  = $email;
                $user->avatar    = $responseGoogle['picture'];
                $user->is_active = 1;
                $user->role      = 'user';
                $user->save();
            }
            $user = User::where('email', $email)->first();
            if ( $user->is_active != 1 && $user->is_active != 0 ) {
                Logging::MOBILE_LOGIN_FALSE( 'MOBILE APP - LOGIN', 'ANDROID LOGIN FALSE -> Tài khoản đang khóa:  ' . $email );

                return response()->json( [
                    'status' => 400,
                    'error'  => 'Tài khoản đang tạm khóa, Vui lòng liên hệ Admin để được hỗ trợ'
                ] );
            }
            if ( $user->is_active == 0 ) {
                $user->is_active = 1;
                $user->save();
            }
            $token = $user->createToken( $user->idNlu . '-' . now() );
            Logging::MOBILE_LOGIN_OK( 'ANDROID APP - LOGIN', 'ANDROID APP - đăng nhập thành công -> ' . $email, $email );

            return response()->json( [
                'status' => 200,
                'token'  => $token->accessToken,
                'user'   => $user
            ] );
        } catch ( \Exception $e ) {
            Logging::MOBILE_LOGIN_FALSE( 'MOBILE APP - LOGIN', 'ANDROID LOGIN FALSE -> Token Login Invalid' );

            return response()->json( [
                'status' => 404,
                'error'  => 'Token không hợp lệ'
            ] );
        }
    }

    public function checkFacebook(Request $r)
    {
        $accessToken = $r->token ?? '';

        if (!$accessToken) {
            return response()->json([
                'status' => 400,
                'error' => 'Không có access token gửi lên'
            ]);
        }

        try {
            $exchange = Http::get('https://graph.facebook.com/oauth/access_token', [
                'grant_type' => 'fb_exchange_token',
                'client_id' => env('FB_APP_ID'),
                'client_secret' => env('FB_APP_SECRET'),
                'fb_exchange_token' => $accessToken
            ]);

            $longToken = $exchange->json()['access_token'] ?? $accessToken;

            $response = Http::get('https://graph.facebook.com/v24.0/me', [
                'fields' => 'id,name,email,picture',
                'access_token' => $longToken
            ]);

            $data = $response->json();
            Logging::ALERT("FACEBOOK_DATA", json_encode($data));

            $facebookId = $data['id'] ?? null;
            $name = $data['name'] ?? 'Facebook User';
            $email = $data['email'] ?? "{$facebookId}@facebook.callparts";
            $avatar = $data['picture']['data']['url'] ?? null;

            if (!User::where('fb', $facebookId)->exists()) {
                $user = new User();
                $user->email = $email;
                $user->name = $name;
                $user->fb = $facebookId;
                $user->avatar = $avatar;
                $user->active = 1;
                $user->group = 0;
                $user->save();
            }
            $users = User::select('email', 'avatar', 'name', 'gender', 'phone', 'group')->where('fb', $facebookId)->get();
            $user = $users[0];
            if ($user->active != 1 && $user->active != 0) {
                Logging::MOBILE_LOGIN_FALSE('MOBILE APP - LOGIN', 'ANDROID LOGIN FALSE -> Tài khoản đang khóa:  ' . $email);

                return response()->json([
                    'status' => 400,
                    'error' => 'Tài khoạn đang tạm khóa, Vui lòng liên hệ Admin để được hỗ trợ'
                ]);
            }
            if ($user->active == 0) {
                $user->active = 1;
                $user->save();
            }
            $token = $user->createToken($user->idNlu . '-' . now());

            return response()->json([
                'status' => 200,
                'token' => $token->accessToken,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Facebook API Error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
