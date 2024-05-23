<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $activationLink;

    /**
     * Create a new message instance.
     *
     * @param  User  $user
     * @param  string  $activationLink
     * @return void
     */
    public function __construct(User $user, string $activationLink)
    {
        $this->user = $user;
        $this->activationLink = $activationLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('IIM ATM 2024 Greeting Email') // Set your custom subject here
                    ->markdown('emails.activation');
    }
}
