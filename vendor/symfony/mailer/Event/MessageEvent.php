<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Event;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\LogicException;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Allows the transformation of a Message and the Envelope before the email is sent.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class MessageEvent extends Event
{
    private bool $rejected = false;

    /** @var StampInterface[] */
    private array $stamps = [];

    public function __construct(
        private RawMessage $message,
        private Envelope $envelope,
        private string $transport,
        private bool $queued = false,
    ) {
    }

    public function getMessage(): RawMessage
    {
        return $this->message;
    }

    public function setMessage(RawMessage $message): void
    {
        $this->message = $message;
    }

    public function getEnvelope(): Envelope
    {
        return $this->envelope;
    }

    public function setEnvelope(Envelope $envelope): void
    {
        $this->envelope = $envelope;
    }

    public function getTransport(): string
    {
        return $this->transport;
    }

    public function isQueued(): bool
    {
        return $this->queued;
    }

    public function isRejected(): bool
    {
        return $this->rejected;
    }

    public function reject(): void
    {
        $this->rejected = true;
        $this->stopPropagation();
    }

    public function addStamp(StampInterface $stamp): void
    {
        if (!$this->queued) {
            throw new LogicException(\sprintf('Cannot call "%s()" on a message that is not meant to be queued.', __METHOD__));
        }

        $this->stamps[] = $stamp;
    }

    /**
     * @return StampInterface[]
     */
    public function getStamps(): array
    {
        if (!$this->queued) {
            throw new LogicException(\sprintf('Cannot call "%s()" on a message that is not meant to be queued.', __METHOD__));
        }

        return $this->stamps;
    }
}
