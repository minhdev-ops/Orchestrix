<?php

    namespace App\Http\Controllers\api;

    use App\Http\Controllers\Controller;
    use App\Mail\ActiveMail;
    use App\Mail\ForgetPass;
    use App\Mail\ReActiveMail;
    use App\Mail\ResetPass;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Str;

    class AuthController extends Controller {


        public function login( Request $request ) {
            $mes = [
                'email.required'    => 'Email is required',
                'password.required' => 'Password is required',
            ];
            $v   = Validator::make( $request->all(), [
                'email'    => 'required',
                'password' => 'required'
            ], $mes );
            if ( $v->fails() ) {
                return response( [ 'error' => $v->errors() ], 400 );
            } else {

                if ( Auth::attempt( [ 'email' => $request->email, 'password' => $request->password, 'is_active' => 1 ] ) ) {
                    $user  = User::where( 'email', $request->email )->first();
                    $token = $user->createToken( $user->email . '-' . now() );
                    return response()->json( [
                        'token' => $token->accessToken,
                        'user'  => $user
                    ] );
                } else {
                    return response()->json( [
                        'error' => 'Login failed, please check Email and Password!'
                    ], 404 );
                }
            }
        }
        public function register( Request $request ) {
            $mes = [
                'email.required'    => 'Email is required',
                'email.unique'      => 'Email Already in Use',
                'password.required' => 'Password is required',
                'password.min'      => 'Password need more then 8 character',
                'repass.same'       => 'Re-password not same password',
                'name.required'     => 'Name is required',
            ];
            $v   = Validator::make( $request->all(), [
                'email'    => 'required|unique:users,email',
                'password' => 'required|min:8',
                'repass'   => 'required|same:password',
                'name'     => 'required',
                'phone'    => '',
            ], $mes );
            if ( $v->fails() ) {
                return response( [ 'error' => $v->errors() ], 400 );
            }
            // create key to active account
            $key  = Str::random( 200 );
            $timeString = now()->toDateTimeString();
            $hash = md5( $key . $timeString );

            \Illuminate\Support\Facades\Log::info("User Registration Hash for {$request->email}: {$hash}");

            $u           = new User();
            $u->email    = $request->email;
            $u->password = bcrypt( $request->password );
            $u->name      = $request->name;
            $u->role      = 'user';
            $u->is_active = 0;
            $u->metadata  = [
                'activation_key' => $key,
                'key_time'       => $timeString,
            ];
            $u->save();
            Mail::to( $u->email )->send( new ActiveMail( $u->email, $hash, $u->name ) );

            return response( [ 'mes' => 'Account registration is successful, please check your email to activate your account.' ] );

        }

        public function activeMail( Request $r, $email, $key ) {
            $users = User::where( 'email', $email )->where( 'is_active', 0 )->get();
            if ( count( $users ) == 1 ) {
                $user = $users[0];
                $metadata = $user->metadata ?? [];
                $hash = md5( ($metadata['activation_key'] ?? '') . ($metadata['key_time'] ?? '') );
                \Illuminate\Support\Facades\Log::info("Verification attempt for {$email}: Expected {$hash}, Got {$key}");
                if ( $hash == $key ) {
                    $user->is_active = 1;
                    $user->metadata = array_merge($metadata, [
                        'activation_key' => null,
                        'key_time'       => null
                    ]);
                    $user->save();

                    return view( 'auth.activeMail', [ 'ok' => 1 ] );
                } else {
                    return view( 'auth.activeMail', [ 'ok' => 0 ] );
                }
            } else {
                return view( 'auth.activeMail', [ 'ok' => 0 ] );
            }
        }

        public function reActive( Request $r ) {
            $v = Validator::make( $r->all(), [
                'email' => 'required'
            ], [ 'email.required' => 'Email is required', ] );
            if ( $v->fails() ) {
                return response( [ 'error' => $v->errors() ], 400 );
            }
            if ( User::where( 'email', $r->email )->where( 'is_active', 0 )->count() == 1 ) {
                $key  = $this->strRandom( 200 );
                $time = now();
                $hash = md5( $key . $time );
                $user = User::where( 'email', $r->email )->first();
                if ( $user->email == $r->email ) {
                    $metadata = $user->metadata ?? [];
                    $user->metadata = array_merge($metadata, [
                        'activation_key' => $key,
                        'key_time'       => $time->toDateTimeString(),
                    ]);
                    $user->save();
                    Mail::to( $user->email )->send( new ReActiveMail( $user->email, $hash, $user->name ) );

                    return response( [
                        'mes' => 'Email activation link has been sent to your email, please check your email!'
                    ] );
                } else {
                    return response( [ 'error' => 'You are a hacker! Please Go!' ], 400 );
                }

            } else {
                return response( [ 'error' => 'Email not found or Email has been activated!' ], 400 );
            }
        }

        public function forgetPass( Request $r ) {
            $v = Validator::make( $r->all(), [
                'email' => 'required'
            ], [ 'email.required' => 'Email is required', ] );
            if ( $v->fails() ) {
                return response( [ 'error' => $v->errors() ], 400 );
            }
            $key  = $this->strRandom( 200 );
            $time = now();
            $hash = md5( $key . $time );
            if ( User::where( 'email', $r->email )->count() ) {
                $user = User::where( 'email', $r->email )->first();
                $name = $user->name;
                if ( $user->email == $r->email ) {
                    $metadata = $user->metadata ?? [];
                    $user->metadata = array_merge($metadata, [
                        'activation_key' => $key,
                        'key_time'       => $time->toDateTimeString(),
                    ]);
                    $user->save();
                    Mail::to( $user->email )->send( new ForgetPass  ( $user->email, $hash, $name ) );

                    return response( [ 'mes' => 'Account recovery email sent to your email, please check your email ' ] );
                } else {
                    return response( [ 'error' => 'You are a hacker! Please Go!' ], 400 );
                }
            } else {
                return response( [ 'error' => 'Email not found!' ], 400 );
            }
        }

        public function resetPass( Request $r, $email, $key ) {
            $users = User::where( 'email', $email )->where( 'is_active', 1 )->get();
            if ( count( $users ) == 1 ) {
                $user = $users[0];
                $metadata = $user->metadata ?? [];
                $hash = md5( ($metadata['activation_key'] ?? '') . ($metadata['key_time'] ?? '') );
                if ( $hash == $key ) {
                    $pass               = $this->strrandom();
                    $user->password     = bcrypt( $pass );
                    $user->metadata = array_merge($metadata, [
                        'activation_key' => null,
                        'key_time'       => null
                    ]);
                    $user->save();
                    Mail::to( $user->email )->send( new ResetPass  ( $pass, $user->name ) );

                    return view( 'auth.resetPass', [ 'ok' => 1 ] );
                } else {
                    return view( 'auth.resetPass', [ 'ok' => 0 ] );
                }


            } else {
                return view( 'auth.resetPass', [ 'ok' => 0 ] );
            }
        }

        public function changePass( Request $r ) {
            $messages = [
                'oldpass.required' => 'Old password is required',
                'newpass.required' => 'New password is required',
                'repass.required'  => 'Re-password is required',
                'repass.same'      => 'Re-password not same password',
                'newpass.min'      => 'Password need more then 8 character',
            ];

            $validator = Validator::make( $r->all(), [
                'oldpass' => 'required',
                'newpass' => 'required|min:8',
                'repass'  => 'required|same:newpass',
            ], $messages );
            if ( $validator->fails() ) {
                return response( [ 'error' => $validator->errors()->getMessages() ], 400 );
            }
            $u = User::where( 'email', $r->user()->email )->first();
            if ( hash::check( $r->oldpass, $u->password ) ) {
                $u->password = Hash::make( $r->newpass );
                $u->save();
                \auth()->user()->token()->revoke();

                return response( [
                    'mes' => "Change password successfully! You are logout, Please login!"
                ] );
            } else {
                return response( [
                    'mes' => "Incorrect password!"
                ], 400 );
            }
        }


        public function logout( Request $request ) {
            $request->user()->token()->revoke();
            return response( [ 'mes' => 'You have been successfully logged out!' ] );
        }

        private function strrandom( $length = 8 ) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz';
            $string     = '';
            for ( $p = 0; $p < $length; $p ++ ) {
                $string .= $characters[ mt_rand( 0, strlen( $characters ) - 1 ) ];
            }

            return $string;
        }

        protected function generateAccessToken( $user ) {
            $token = $user->createToken( $user->email . '-' . now() );

            return $token->accessToken;
        }

        public function show( Request $request ) {
            $u = \auth()->user();
            return response($u);

        }


    }
