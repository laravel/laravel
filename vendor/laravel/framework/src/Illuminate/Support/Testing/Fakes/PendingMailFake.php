<?php

namespace Illuminate\Support\Testing\Fakes;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Mail\PendingMail;

class PendingMailFake extends PendingMail
{
    /**
     * Create a new instance.
     *
     * @param  \Illuminate\Support\Testing\Fakes\MailFake  $mailer
     */
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send a new mailable message instance.
     *
     * @param  \Illuminate\Contracts\Mail\Mailable  $mailable
     * @return void
     */
    public function send(Mailable $mailable)
    {
        $this->mailer->send($this->fill($mailable));
    }

    /**
     * Send a new mailable message instance synchronously.
     *
     * @param  \Illuminate\Contracts\Mail\Mailable  $mailable
     * @return void
     */
    public function sendNow(Mailable $mailable)
    {
        $this->mailer->sendNow($this->fill($mailable));
    }

    /**
     * Push the given mailable onto the queue.
     *
     * @param  \Illuminate\Contracts\Mail\Mailable  $mailable
     * @return mixed
     */
    public function queue(Mailable $mailable)
    {
        return $this->mailer->queue($this->fill($mailable));
    }
}
