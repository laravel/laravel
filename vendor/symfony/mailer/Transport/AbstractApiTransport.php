<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Transport;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\RuntimeException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class AbstractApiTransport extends AbstractHttpTransport
{
    abstract protected function doSendApi(SentMessage $sentMessage, Email $email, Envelope $envelope): ResponseInterface;

    protected function doSendHttp(SentMessage $message): ResponseInterface
    {
        try {
            $email = MessageConverter::toEmail($message->getOriginalMessage());
        } catch (\Exception $e) {
            throw new RuntimeException(\sprintf('Unable to send message with the "%s" transport: ', __CLASS__).$e->getMessage(), 0, $e);
        }

        return $this->doSendApi($message, $email, $message->getEnvelope());
    }

    /**
     * @return Address[]
     */
    protected function getRecipients(Email $email, Envelope $envelope): array
    {
        return array_filter($envelope->getRecipients(), static fn (Address $address) => false === \in_array($address, array_merge($email->getCc(), $email->getBcc()), true));
    }
}
