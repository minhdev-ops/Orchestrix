<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConsultationBookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $booking;

    public function __construct(array $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('[AgriVerse] Yêu cầu đặt lịch tư vấn mới')
            ->view('emails.consultation-booking');
    }
}