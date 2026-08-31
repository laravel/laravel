<?php

namespace Illuminate\Mail\Events;

use Symfony\Component\Mime\Email;

class MessageSending
{
    /**
     * Create a new event instance.
     *
     * @param  \Symfony\Component\Mime\Email  $message  The Symfony Email instance.
     * @param  array  $data  The message data.
     */
    public function __construct(
        public Email $message,
        public array $data = [],
    ) {
    }
}
