<?php

namespace App\Domain\Accounts\Verification;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Domain\Accounts\Account;
use App\Domain\Translations\Translations;

class VerifyCodeNotification extends Notification
{
    /**
     * Verify code
     *
     * @var string
     */
    public $code;

    /**
     * Unencrypted token to verify code
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $code
     * @param  string  $token
     * @return void
     */
    public function __construct(string $code, string $token)
    {
        $this->code = $code;
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('accounts.verification.email.subject'))
            ->view('mail/accounts/verification/verify-code', [
                'model' => [
                    'url' => route('verify-codes.show', [ $this->code, 'secret' => $this->token ]),
                    'title' => trans('accounts.verification.email.title'),
                    'message' => trans('accounts.verification.email.message'),
                    'button' => trans('accounts.verification.email.button'),
                ],
            ]);
    }
}
