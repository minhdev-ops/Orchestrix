<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Mail\ConsultationBookingNotification;
use App\Models\User;
use App\Modules\AgriVerse\Models\ConsultationBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SupportController
{
    public function storeBooking(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'topic' => 'nullable|string|max:255',
            'preferred_date' => 'nullable|date',
            'preferred_time' => 'nullable|string|max:20',
            'message' => 'nullable|string|max:2000',
        ]);

        $booking = ConsultationBooking::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'topic' => $data['topic'] ?? null,
            'preferred_date' => $data['preferred_date'] ?? null,
            'preferred_time' => $data['preferred_time'] ?? null,
            'message' => $data['message'] ?? null,
            'status' => 'pending',
            'reference' => 'TV'.strtoupper(Str::random(6)),
        ]);

        $admin = User::where('role', 'admin')->first();
        $mailTarget = $admin?->email ?: config('mail.from.address');

        Mail::to($mailTarget)->send(new ConsultationBookingNotification([
            'name' => $booking->name,
            'email' => $booking->email,
            'phone' => $booking->phone,
            'topic' => $booking->topic,
            'preferred_date' => $booking->preferred_date?->toDateString(),
            'preferred_time' => $booking->preferred_time,
            'message' => $booking->message,
            'reference' => $booking->reference,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Đặt lịch thành công! Chúng tôi sẽ liên hệ bạn trong thời gian sớm nhất.',
            'reference' => $booking->reference,
        ]);
    }
}
