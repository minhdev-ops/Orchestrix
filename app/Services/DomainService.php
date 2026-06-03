<?php


    namespace App\Services;


    use App\Models\AppModule;
    use App\Models\Domain;

    class DomainService implements Service {
        private static $instant = null;

        private function __construct() {
        }

        public static function getInstant() {
            if ( self::$instant == null ) {
                self::$instant = new DomainService();
            }

            return self::$instant;
        }

        public function getAll() {
            return Domain::with( 'apps','mods','_own' )->select( '*' )->get();
        }

        public function getAllAppName( $sub = '*' ) {
            $query = AppModule::select( 'id', 'name' )->where( 'forSub', '*' );
            if ( $sub != '*' ) {
                $query = $query->orWhere( 'forSub', $sub );
            }

            return $query->get();
        }

        public function getAllowDomain() {
            return Domain::with( 'apps' )->select( 'id', 'name', 'status' )->where( 'status', 1 )->get()->mapWithKeys( function ( $item ) {
                return [ $item['name'] => $item ];
            } );
        }
        public function isMainDomain(){
            $this->checkCacheDomain();
            return request()->getHost() == cache( 'MAIN_DOMAIN');
        }
        public function subDomain(){
            $this->checkCacheDomain();
            $sub = request()->getHost();
            return substr( $sub, 0, strlen( $sub ) - strlen( cache( 'MAIN_DOMAIN') ) - 1 );
        }
        public function checkCacheDomain(){
            if ( cache( 'MAIN_DOMAIN', '' ) === '' ) {
                cache( [ 'MAIN_DOMAIN'=> env('APP_DOMAIN') ], 300 );
            }
        }
    }
