<?php

namespace App\Domain\Accounts;

use Illuminate\Auth\Notifications\ResetPassword as Base;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Base
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('accounts.password.email.subject'))
            ->view('mail/accounts/password-reset', [
                'model' => trans('accounts.passwords.email') + [
                    'url' => route('password-resets.show', $this->token),
                ],
            ]);
    }
}
