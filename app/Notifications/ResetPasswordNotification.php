<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expire = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return (new MailMessage)
            ->subject(Lang::get('passwords.email.subject'))
            ->greeting(Lang::get('passwords.email.greeting'))
            ->line(Lang::get('passwords.email.line1'))
            ->action(Lang::get('passwords.email.action'), $url)
            ->line(Lang::get('passwords.email.line2', ['count' => $expire]))
            ->line(Lang::get('passwords.email.line3'))
            ->salutation(Lang::get('passwords.email.salutation', ['app_name' => config('app.name')]));
    }
}