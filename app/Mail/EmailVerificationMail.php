<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác thực email - AgriVerse',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildHtml(),
        );
    }

    protected function buildHtml(): string
    {
        $userName = $this->user->name;
        $code = $this->code;
        $expiryMinutes = 60;

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
                .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #486730 0%, #6b9b37 100%); padding: 30px; text-align: center; }
                .header h1 { color: white; margin: 0; font-size: 24px; }
                .content { padding: 30px; }
                .code-box { background: #f8f9fa; border: 2px dashed #486730; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; }
                .code { font-size: 32px; font-weight: bold; color: #486730; letter-spacing: 8px; }
                .note { color: #666; font-size: 14px; text-align: center; margin-top: 15px; }
                .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>AgriVerse</h1>
                </div>
                <div class="content">
                    <p>Xin chào <strong>{$userName}</strong>,</p>
                    <p>Cảm ơn bạn đã đăng ký tài khoản tại AgriVerse. Vui lòng sử dụng mã xác thực bên dưới:</p>
                    
                    <div class="code-box">
                        <div class="code">{$code}</div>
                    </div>
                    
                    <p class="note">Mã xác thực có hiệu lực trong <strong>{$expiryMinutes} phút</strong>.</p>
                    <p class="note">Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.</p>
                </div>
                <div class="footer">
                    <p>© {$this->currentYear()} AgriVerse. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    protected function currentYear(): int
    {
        return (int) date('Y');
    }
}
