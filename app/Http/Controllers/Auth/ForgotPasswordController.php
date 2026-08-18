<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\CaptchaService;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $captcha = app(CaptchaService::class);

        $rules = ['email' => 'required|email'];
        if ($captcha->isEnabled()) {
            $rules['captcha_token'] = 'required|string';
        }

        $this->validate($request, $rules);

        if ($captcha->isEnabled() && ! $captcha->verify($request->captcha_token ?? '', $request->ip())) {
            throw ValidationException::withMessages([
                'captcha' => ['Xác minh bảo mật thất bại. Vui lòng thử lại.']
            ]);
        }

        return $this->traitSendResetLinkEmail($request);
    }

    // Call the trait's method internally by alias
    public function traitSendResetLinkEmail(Request $request)
    {
        return $this->broker()->sendResetLink(
            $this->credentials($request)
        ) == \Illuminate\Support\Facades\Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, \Illuminate\Support\Facades\Password::RESET_LINK_SENT)
                    : $this->sendResetLinkFailedResponse($request, \Illuminate\Support\Facades\Password::RESET_LINK_FAILED);
    }
}
