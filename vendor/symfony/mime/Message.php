<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime;

use Symfony\Component\Mime\Exception\LogicException;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Component\Mime\Part\TextPart;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Message extends RawMessage
{
    private Headers $headers;

    public function __construct(
        ?Headers $headers = null,
        private ?AbstractPart $body = null,
    ) {
        $this->headers = $headers ? clone $headers : new Headers();
    }

    public function __clone()
    {
        $this->headers = clone $this->headers;

        if (null !== $this->body) {
            $this->body = clone $this->body;
        }
    }

    /**
     * @return $this
     */
    public function setBody(?AbstractPart $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getBody(): ?AbstractPart
    {
        return $this->body;
    }

    /**
     * @return $this
     */
    public function setHeaders(Headers $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    public function getPreparedHeaders(): Headers
    {
        $headers = clone $this->headers;

        if (!$headers->has('From')) {
            if (!$headers->has('Sender')) {
                throw new LogicException('An email must have a "From" or a "Sender" header.');
            }
            $headers->addMailboxListHeader('From', [$headers->get('Sender')->getAddress()]);
        }

        if (!$headers->has('MIME-Version')) {
            $headers->addTextHeader('MIME-Version', '1.0');
        }

        if (!$headers->has('Date')) {
            $headers->addDateHeader('Date', new \DateTimeImmutable());
        }

        // determine the "real" sender
        if (!$headers->has('Sender') && \count($froms = $headers->get('From')->getAddresses()) > 1) {
            $headers->addMailboxHeader('Sender', $froms[0]);
        }

        if (!$headers->has('Message-ID')) {
            $headers->addIdHeader('Message-ID', $this->generateMessageId());
        }

        // remove the Bcc field which should NOT be part of the sent message
        $headers->remove('Bcc');

        return $headers;
    }

    public function toString(): string
    {
        if (null === $body = $this->getBody()) {
            $body = new TextPart('');
        }

        return $this->getPreparedHeaders()->toString().$body->toString();
    }

    public function toIterable(): iterable
    {
        if (null === $body = $this->getBody()) {
            $body = new TextPart('');
        }

        yield $this->getPreparedHeaders()->toString();
        yield from $body->toIterable();
    }

    public function ensureValidity(): void
    {
        if (!$this->headers->get('To')?->getBody() && !$this->headers->get('Cc')?->getBody() && !$this->headers->get('Bcc')?->getBody()) {
            throw new LogicException('An email must have a "To", "Cc", or "Bcc" header.');
        }

        if (!$this->headers->get('From')?->getBody() && !$this->headers->get('Sender')?->getBody()) {
            throw new LogicException('An email must have a "From" or a "Sender" header.');
        }

        parent::ensureValidity();
    }

    public function generateMessageId(): string
    {
        if ($this->headers->has('Sender')) {
            $sender = $this->headers->get('Sender')->getAddress();
        } elseif ($this->headers->has('From')) {
            if (!$froms = $this->headers->get('From')->getAddresses()) {
                throw new LogicException('A "From" header must have at least one email address.');
            }
            $sender = $froms[0];
        } else {
            throw new LogicException('An email must have a "From" or a "Sender" header.');
        }

        return bin2hex(random_bytes(16)).strstr($sender->getAddress(), '@');
    }

    public function __serialize(): array
    {
        return [$this->headers, $this->body];
    }

    public function __unserialize(array $data): void
    {
        [$this->headers, $this->body] = $data;
    }
}
