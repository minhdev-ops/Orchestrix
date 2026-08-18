<?php

/**
 * @deprecated Legacy interface with only 1 implementation (UserService).
 * Use dedicated service classes instead.
 * Will be removed in next major version.
 */

namespace App\Services;

interface Service
{
    public static function getInstant();

    public function getAll();
}
