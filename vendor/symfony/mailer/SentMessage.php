<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer;

use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\RawMessage;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SentMessage
{
    private RawMessage $original;
    private RawMessage $raw;
    private string $messageId;
    private string $debug = '';

    /**
     * @internal
     */
    public function __construct(
        RawMessage $message,
        private Envelope $envelope,
    ) {
        $message->ensureValidity();

        $this->original = $message;

        if ($message instanceof Message) {
            $message = clone $message;
            $headers = $message->getHeaders();
            if (!$headers->has('Message-ID')) {
                $headers->addIdHeader('Message-ID', $message->generateMessageId());
            }
            $this->messageId = $headers->get('Message-ID')->getId();
            $this->raw = new RawMessage($message->toIterable());
        } else {
            $this->raw = $message;
        }
    }

    public function getMessage(): RawMessage
    {
        return $this->raw;
    }

    public function getOriginalMessage(): RawMessage
    {
        return $this->original;
    }

    public function getEnvelope(): Envelope
    {
        return $this->envelope;
    }

    /**
     * Sets the transport-level message ID.
     */
    public function setMessageId(string $id): void
    {
        $this->messageId = $id;
    }

    /**
     * Gets the transport-level message ID.
     *
     * Not to be confused with the Message-ID header, which is available via getOriginalMessage()
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getDebug(): string
    {
        return $this->debug;
    }

    public function appendDebug(string $debug): void
    {
        $this->debug .= $debug;
    }

    public function toString(): string
    {
        return $this->raw->toString();
    }

    public function toIterable(): iterable
    {
        return $this->raw->toIterable();
    }
}
