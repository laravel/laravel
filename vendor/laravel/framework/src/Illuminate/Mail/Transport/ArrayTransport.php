<?php

namespace Illuminate\Mail\Transport;

use Illuminate\Support\Collection;
use Stringable;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;

class ArrayTransport implements Stringable, TransportInterface
{
    /**
     * The collection of Symfony Messages.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $messages;

    /**
     * Create a new array transport instance.
     */
    public function __construct()
    {
        $this->messages = new Collection;
    }

    /**
     * {@inheritdoc}
     */
    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        return $this->messages[] = new SentMessage($message, $envelope ?? Envelope::create($message));
    }

    /**
     * Retrieve the collection of messages.
     *
     * @return \Illuminate\Support\Collection
     */
    public function messages()
    {
        return $this->messages;
    }

    /**
     * Clear all of the messages from the local collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function flush()
    {
        return $this->messages = new Collection;
    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'array';
    }
}
