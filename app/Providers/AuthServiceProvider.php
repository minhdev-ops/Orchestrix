<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        // Đồng bộ thời hạn token với config/passport.php (PASSPORT_TOKEN_EXPIRATION, mặc định 365 ngày).
        // TRƯỚC ĐÂY set 15 phút: chỉ sau ~15 phút làm việc, token hết hạn, WebSocket bị chối
        // ("JWT Passport Verification Failed: The Token has expired") và frontend không tự cấp mới
        // → realtime chat "chết" âm thầm mãi mãi. Messenger giữ phiên nên không gặp lỗi này.
        $days = (int) config('passport.token_expiration', 365);
        Passport::tokensExpireIn(now()->addDays($days));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addDays($days));
    }
}
