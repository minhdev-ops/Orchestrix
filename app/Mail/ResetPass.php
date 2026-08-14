<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPass extends Mailable
{
    use Queueable, SerializesModels;

    public $pass;

    public $name;

    public function __construct($pass, $name)
    {
        $this->pass = $pass;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Mật khẩu mới của bạn')->view('emails.resetPass');
    }
}
