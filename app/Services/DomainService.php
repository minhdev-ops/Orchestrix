<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

/**
 * @deprecated Domain/AppModule models no longer exist.
 * Kept for reference; will be removed in next major version.
 */
class DomainService implements Service
{
    private static $instant = null;

    private function __construct() {}

    public static function getInstant()
    {
        if (self::$instant === null) {
            self::$instant = new DomainService;
        }

        return self::$instant;
    }

    public function getAll()
    {
        return collect();
    }

    public function getAllAppName($sub = '*')
    {
        return collect();
    }

    public function getAllowDomain()
    {
        return collect();
    }

    public function isMainDomain()
    {
        $this->checkCacheDomain();

        return request()->getHost() === cache('MAIN_DOMAIN');
    }

    public function subDomain()
    {
        $this->checkCacheDomain();
        $sub = request()->getHost();

        return substr($sub, 0, strlen($sub) - strlen(cache('MAIN_DOMAIN')) - 1);
    }

    public function checkCacheDomain()
    {
        if (cache('MAIN_DOMAIN', '') === '') {
            cache(['MAIN_DOMAIN' => Config::get('app.domain', env('APP_DOMAIN'))], 300);
        }
    }
}
