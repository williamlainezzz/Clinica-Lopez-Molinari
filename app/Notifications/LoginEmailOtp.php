<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginEmailOtp extends Notification
{
    use Queueable;

    public function __construct(
        public string $code,
        public int $ttlMinutes = 5
    ) {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tu codigo de verificacion')
            ->markdown('emails.login-otp', [
                'code' => $this->code,
                'ttlMinutes' => $this->ttlMinutes,
                'appName' => config('mail.from.name', 'Complejo Dental Lopez Molinari'),
            ]);
    }
}
