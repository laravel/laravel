<?php

namespace App\Domain\Accounts\Verification;

use Illuminate\Support\Facades\Mail;

class SendVerifyMail
{
    /**
     * Sends the opt-in email to the account's email.
     *
     * @param AccountCreatedEvent  $event
     * @return void
     */
    public function handle(VerifyCodeCreatedEvent $event)
    {
        Mail::to($event->verifyCode->account->email)
            ->send(new VerifyMail($event->verifyCode));
    }
}
