<?php

    namespace App\Http\Controllers\auth;

    use App\Http\Controllers\Controller;
    use App\Models\Logging;
    use App\Models\User;
    use App\Services\UserService;
    use Laravel\Socialite\Facades\Socialite;
    use Laravel\Socialite\Two\InvalidStateException;

    class GoogleAuthController extends Controller {
        public function login() {
            return Socialite::driver( 'google' )->redirect();
        }

        public function callBack() {
            session_set_cookie_params(0, '/', '.' . env( 'APP_DOMAIN' ));
            session_start();
            try {
                $u    = Socialite::driver( 'google' )->user();
                $user = User::select( 'id', 'email', 'name', 'avatar', 'gender', 'phone', 'group' )->where( 'email', $u->getEmail() )->get();
                if ( count( $user ) == 1 ) {
                    UserService::getInstant()->saveSession( $user[0] );
                    Logging::LOGIN_OK( 'Login with Google', 'Đăng nhập thành công -> ' . $user[0]->email );

                    return redirect( '/' )->withCookie( cookie( 'auth', $user[0], 24 * 60 ) );
                } else if ( count( $user ) == 0 ) {
                    $user         = new User();
                    $user->name   = $u->getName();
                    $user->email  = $u->getEmail();
                    $user->avatar = $u->getAvatar();
                    $user->active = 1;
                    $user->group  = 0;
                    $user->save();
                    UserService::getInstant()->saveSession( $user );
                    Logging::LOGIN_OK( 'Register and Login with Google', 'Đăng nhập thành công -> ' . $user->email );

                    return redirect( '/' )->withCookie( cookie( 'auth', $user, 24 * 60 ) );
                } else {
                    return redirect()->route( 'login.google' );
                }
            } catch ( InvalidStateException $e ) {
                return redirect()->route( 'login.google' );
            }
        }
    }
