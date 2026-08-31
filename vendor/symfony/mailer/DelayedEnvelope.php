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

use Symfony\Component\Mailer\Exception\LogicException;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Message;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class DelayedEnvelope extends Envelope
{
    private bool $senderSet = false;
    private bool $recipientsSet = false;

    public function __construct(
        private Message $message,
    ) {
    }

    public function setSender(Address $sender): void
    {
        parent::setSender($sender);

        $this->senderSet = true;
    }

    public function getSender(): Address
    {
        if (!$this->senderSet) {
            parent::setSender(self::getSenderFromHeaders($this->message->getHeaders()));
        }

        return parent::getSender();
    }

    public function setRecipients(array $recipients): void
    {
        parent::setRecipients($recipients);

        $this->recipientsSet = (bool) parent::getRecipients();
    }

    /**
     * @return Address[]
     */
    public function getRecipients(): array
    {
        if ($this->recipientsSet) {
            return parent::getRecipients();
        }

        return self::getRecipientsFromHeaders($this->message->getHeaders());
    }

    private static function getRecipientsFromHeaders(Headers $headers): array
    {
        $recipients = [];
        foreach (['to', 'cc', 'bcc'] as $name) {
            foreach ($headers->all($name) as $header) {
                foreach ($header->getAddresses() as $address) {
                    $recipients[] = $address;
                }
            }
        }

        return $recipients;
    }

    private static function getSenderFromHeaders(Headers $headers): Address
    {
        if ($sender = $headers->get('Sender')) {
            return $sender->getAddress();
        }
        if ($return = $headers->get('Return-Path')) {
            return $return->getAddress();
        }
        if ($from = $headers->get('From')) {
            return $from->getAddresses()[0];
        }

        throw new LogicException('Unable to determine the sender of the message.');
    }
}
