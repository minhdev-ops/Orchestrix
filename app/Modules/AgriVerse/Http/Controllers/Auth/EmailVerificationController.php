<?php

namespace App\Modules\AgriVerse\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Modules\AgriVerse\Services\EmailVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    protected EmailVerificationService $verificationService;

    public function __construct(EmailVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * Show verification page
     */
    public function show()
    {
        $user = Auth::user();

        if ($this->verificationService->isVerified($user)) {
            return redirect()->route('agriverse.shop.home');
        }

        return view('auth.verify-email', [
            'email' => $user->email,
        ]);
    }

    /**
     * Send verification code
     */
    public function sendCode(Request $request)
    {
        $user = $request->user();

        if ($this->verificationService->isVerified($user)) {
            return response()->json([
                'success' => true,
                'message' => 'Email đã được xác thực.',
            ]);
        }

        $success = $this->verificationService->sendVerificationCode($user);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Mã xác thực đã được gửi đến email của bạn.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không thể gửi email. Vui lòng thử lại sau.',
        ], 500);
    }

    /**
     * Verify code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();
        $result = $this->verificationService->verifyCode($user, $request->code);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'redirect' => route('agriverse.shop.home'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 422);
    }
}
