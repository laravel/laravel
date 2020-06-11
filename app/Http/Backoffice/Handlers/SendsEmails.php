<?php

namespace App\Http\Backoffice\Handlers;

use Digbang\Security\Users\User;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

trait SendsEmails
{
    public function sendPasswordReset(User $user, string $link): void
    {
        $this->send('backoffice::emails.reset-password', $user, $link, trans(
            'backoffice::emails.reset-password.subject'
        ));
    }

    public function sendActivation(User $user, string $link): void
    {
        $this->send('backoffice::emails.activation', $user, $link, trans(
            'backoffice::emails.activation.subject'
        ));
    }

    protected function send(string $view, User $user, string $link, string $subject): void
    {
        $from = config('backoffice.emails');

        $name = $user->getName() ?: $user->getUsername();

        Mail::send($view, ['name' => $name, 'link' => $link], function (Message $message) use ($user, $from, $subject, $name): void {
            $message
                ->from($from['address'], $from['name'])
                ->to($user->getEmail(), $name)
                ->subject($subject);
        });
    }
}
