<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActiveMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public $hash;

    public $name;

    public function __construct($email, $hash, $name)
    {
        $this->email = $email;
        $this->hash = $hash;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Kích hoạt tài khoản')->view('emails.active');
    }
}
