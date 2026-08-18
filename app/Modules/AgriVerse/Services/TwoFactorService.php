<?php

namespace App\Modules\AgriVerse\Services;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FALaravel\Google2FA;

class TwoFactorService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = app(Google2FA::class);
    }

    /**
     * Generate new secret key
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Generate secret and QR code URL for setup
     */
    public function generateSetupData(User $user): array
    {
        $secret = $this->google2fa->generateSecretKey();
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'AgriVerse'),
            $user->email,
            $secret
        );

        return [
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'otpauth_url' => 'otpauth://totp/'.urlencode(config('app.name', 'AgriVerse')).':'.urlencode($user->email)."?secret={$secret}&issuer=".urlencode(config('app.name', 'AgriVerse')),
        ];
    }

    /**
     * Verify TOTP code
     */
    public function verifyCode(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code, 1); // 1 window = 30 seconds
    }

    /**
     * Enable 2FA for user
     */
    public function enable(User $user, string $secret, string $code): array
    {
        if (! $this->verifyCode($secret, $code)) {
            return ['success' => false, 'message' => 'Mã xác thực không đúng. Vui lòng thử lại.'];
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->update([
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_recovery_codes' => $recoveryCodes,
            'two_factor_enabled_at' => now(),
        ]);

        return [
            'success' => true,
            'message' => 'Đã bật xác thực hai yếu tố.',
            'recovery_codes' => $recoveryCodes,
        ];
    }

    /**
     * Disable 2FA for user
     */
    public function disable(User $user, string $code): array
    {
        if (! $this->is2FAEnabled($user)) {
            return ['success' => false, 'message' => '2FA chưa được bật.'];
        }

        $secret = $this->getSecret($user);

        if (! $this->verifyCode($secret, $code)) {
            return ['success' => false, 'message' => 'Mã xác thực không đúng.'];
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_enabled_at' => null,
        ]);

        return ['success' => true, 'message' => 'Đã tắt xác thực hai yếu tố.'];
    }

    /**
     * Check if 2FA is enabled
     */
    public function is2FAEnabled(User $user): bool
    {
        return ! is_null($user->two_factor_secret) && ! is_null($user->two_factor_enabled_at);
    }

    /**
     * Get decrypted secret
     */
    public function getSecret(User $user): ?string
    {
        if (! $user->two_factor_secret) {
            return null;
        }

        try {
            return Crypt::decryptString($user->two_factor_secret);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Verify during login (code or recovery code)
     */
    public function verifyLogin(User $user, string $code): array
    {
        if (! $this->is2FAEnabled($user)) {
            return ['success' => true, 'requires_2fa' => false];
        }

        // Try TOTP code first
        $secret = $this->getSecret($user);
        if ($secret && $this->verifyCode($secret, $code)) {
            return ['success' => true, 'requires_2fa' => true];
        }

        // Try recovery code
        $recoveryCodes = $user->two_factor_recovery_codes ?? [];
        if (in_array($code, $recoveryCodes)) {
            // Remove used recovery code
            $recoveryCodes = array_values(array_diff($recoveryCodes, [$code]));
            $user->update(['two_factor_recovery_codes' => $recoveryCodes]);

            return ['success' => true, 'requires_2fa' => true, 'recovery_used' => true];
        }

        return ['success' => false, 'message' => 'Mã xác thực không đúng.'];
    }

    /**
     * Generate recovery codes
     */
    protected function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(4).'-'.Str::random(4));
        }

        return $codes;
    }

    /**
     * Get remaining recovery codes count
     */
    public function getRecoveryCodesCount(User $user): int
    {
        return count($user->two_factor_recovery_codes ?? []);
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(User $user, string $code): array
    {
        $secret = $this->getSecret($user);
        if (! $secret || ! $this->verifyCode($secret, $code)) {
            return ['success' => false, 'message' => 'Mã xác thực không đúng.'];
        }

        $recoveryCodes = $this->generateRecoveryCodes();
        $user->update(['two_factor_recovery_codes' => $recoveryCodes]);

        return [
            'success' => true,
            'message' => 'Đã tạo lại mã khôi phục.',
            'recovery_codes' => $recoveryCodes,
        ];
    }
}
