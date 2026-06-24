<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Modules\AgriVerse\Services\TwoFactorService;

class TwoFactorController extends Controller
{
    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Show 2FA settings page
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isEnabled = $this->twoFactorService->is2FAEnabled($user);

        return Inertia::render('Marketplace/Settings/TwoFactor', [
            'isEnabled' => $isEnabled,
            'recoveryCodesCount' => $isEnabled ? $this->twoFactorService->getRecoveryCodesCount($user) : 0,
            'enabledAt' => $isEnabled ? $user->two_factor_enabled_at->toISOString() : null,
        ]);
    }

    /**
     * Generate QR code for setup
     */
    public function setup(Request $request)
    {
        $user = $request->user();

        if ($this->twoFactorService->is2FAEnabled($user)) {
            return response()->json([
                'message' => '2FA đã được bật.',
            ], 422);
        }

        $setupData = $this->twoFactorService->generateSetupData($user);

        // Store secret temporarily for verification
        session(['2fa_setup_secret' => $setupData['secret']]);

        return response()->json([
            'secret' => $setupData['secret'],
            'qr_code_url' => $setupData['qr_code_url'],
            'otpauth_url' => $setupData['otpauth_url'],
        ]);
    }

    /**
     * Enable 2FA
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();
        $secret = session('2fa_setup_secret');

        if (!$secret) {
            return response()->json([
                'message' => 'Phiên setup đã hết hạn. Vui lòng thử lại.',
            ], 422);
        }

        $result = $this->twoFactorService->enable($user, $secret, $request->code);

        if ($result['success']) {
            session()->forget('2fa_setup_secret');
        }

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        $result = $this->twoFactorService->disable($user, $request->code);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();
        $result = $this->twoFactorService->regenerateRecoveryCodes($user, $request->code);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Verify 2FA during login
     */
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        $result = $this->twoFactorService->verifyLogin($user, $request->code);

        if ($result['success'] && $result['requires_2fa'] ?? false) {
            session(['2fa_verified' => true]);
            return response()->json([
                'success' => true,
                'message' => 'Xác thực thành công.',
                'redirect' => $this->getRedirectUrl($user),
            ]);
        }

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get redirect URL based on user role
     */
    protected function getRedirectUrl($user): string
    {
        if ($user->isAdmin()) {
            return route('admin.agriverse.dashboard');
        }
        return route('agriverse.shop.home');
    }
}
