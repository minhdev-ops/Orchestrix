<?php


    namespace App\Services;


    use App\Models\User;

    class UserService implements Service {
        private static $instant = null;

        private function __construct() {
        }

        public static function getInstant(): UserService {
            if ( self::$instant == null ) {
                self::$instant = new UserService();
            }

            return self::$instant;
        }
        public function saveSession(User $user){
            $user->loginAt = now();
            session()->put( 'auth', $user );
            session()->forget( 'logf' );
            $_SESSION['auth']  = true;
            $_SESSION['email'] = $user->email;
            $_SESSION['group'] = $user->group;
            $_SESSION['host']  = env( 'APP_URL' );
        }

        public function getAll() {
            // TODO: Implement getAll() method.
        }

        public function getByGroup(array $group){
            return User::select('id','name','email','phone','group','gender','birthday')->where('active',1)->whereIn('group',$group)->get();
        }
    }
