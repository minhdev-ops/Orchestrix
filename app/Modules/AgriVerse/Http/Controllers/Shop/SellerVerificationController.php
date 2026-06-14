<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Mail\SellerVerificationMail;
use App\Modules\AgriVerse\Models\SellerVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class SellerVerificationController
{
    public function showForm()
    {
        $user = auth()->user();
        $verification = SellerVerification::where('user_id', $user->id)->latest()->first();

        return Inertia::render('Marketplace/Seller/Register', [
            'verification' => $verification,
            'user' => $user->only(['id', 'name', 'email', 'phone']),
        ]);
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);

        $user = auth()->user();
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        SellerVerification::updateOrCreate(
            ['user_id' => $user->id],
            ['email' => $request->email, 'email_verification_code' => $code, 'email_verified_at' => null],
        );

        try {
            Mail::to($request->email)->send(new SellerVerificationMail($user->name, $code));
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể gửi mã xác thực. Vui lòng thử lại sau.');
        }

        // Queue resend ability after 60s
        session()->put('seller_code_sent_at', now());

        return back()->with('success', 'Mã xác thực đã được gửi đến email của bạn.');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $verification = SellerVerification::where('user_id', auth()->id())->latest()->first();

        if (!$verification || $verification->email_verification_code !== $request->code) {
            return back()->with('error', 'Mã xác thực không đúng.');
        }

        $sentAt = session('seller_code_sent_at');
        if ($sentAt && now()->diffInMinutes($sentAt) > 10) {
            return back()->with('error', 'Mã xác thực đã hết hạn. Vui lòng yêu cầu mã mới.');
        }

        $verification->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
        ]);

        return back()->with('success', 'Email đã được xác thực.');
    }

    public function submit(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'id_card_number' => 'nullable|string|max:20',
            'id_card_image_front' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'id_card_image_back' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'portrait_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'phone' => 'nullable|string|max:20',
        ]);

        $verification = SellerVerification::where('user_id', $user->id)->latest()->first();

        if (!$verification || !$verification->email_verified_at) {
            return back()->with('error', 'Vui lòng xác thực email trước khi gửi yêu cầu.');
        }

        $data = [
            'user_id' => $user->id,
            'id_card_number' => $validated['id_card_number'] ?? $verification->id_card_number,
            'phone' => $validated['phone'] ?? $user->phone,
            'email' => $verification->email,
            'status' => 'pending',
        ];

        if ($request->hasFile('id_card_image_front')) {
            $data['id_card_image_front'] = $request->file('id_card_image_front')
                ->store('seller-verifications', 'public');
        }
        if ($request->hasFile('id_card_image_back')) {
            $data['id_card_image_back'] = $request->file('id_card_image_back')
                ->store('seller-verifications', 'public');
        }
        if ($request->hasFile('portrait_image')) {
            $data['portrait_image'] = $request->file('portrait_image')
                ->store('seller-verifications', 'public');
        }

        $verification->update($data);

        return redirect()->route('agriverse.shop.seller.register')
            ->with('success', 'Đã gửi yêu cầu đăng ký người bán. Vui lòng chờ xác nhận từ quản trị viên.');
    }

    public function status()
    {
        $verification = SellerVerification::where('user_id', auth()->id())->latest()->first();

        return response()->json([
            'has_submitted' => !is_null($verification),
            'status' => $verification?->status ?? 'none',
            'email_verified' => !is_null($verification?->email_verified_at),
            'verification' => $verification,
        ]);
    }
}
