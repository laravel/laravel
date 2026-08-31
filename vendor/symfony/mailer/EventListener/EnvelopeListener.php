<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;

/**
 * Manipulates the Envelope of a Message.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class EnvelopeListener implements EventSubscriberInterface
{
    private ?Address $sender = null;

    /**
     * @var Address[]|null
     */
    private ?array $recipients = null;

    /**
     * @param array<Address|string> $recipients
     * @param string[]              $allowedRecipients An array of regex to match the allowed recipients
     */
    public function __construct(
        Address|string|null $sender = null,
        ?array $recipients = null,
        private array $allowedRecipients = [],
    ) {
        if (null !== $sender) {
            $this->sender = Address::create($sender);
        }
        if (null !== $recipients) {
            $this->recipients = Address::createArray($recipients);
        }
    }

    public function onMessage(MessageEvent $event): void
    {
        if ($this->sender) {
            $event->getEnvelope()->setSender($this->sender);

            $message = $event->getMessage();
            if ($message instanceof Message) {
                if (!$message->getHeaders()->has('Sender') && !$message->getHeaders()->has('From')) {
                    $message->getHeaders()->addMailboxHeader('Sender', $this->sender);
                }
            }
        }

        if ($this->recipients) {
            $recipients = $this->recipients;
            if ($this->allowedRecipients) {
                foreach ($event->getEnvelope()->getRecipients() as $recipient) {
                    foreach ($this->allowedRecipients as $allowedRecipient) {
                        if (!preg_match('{\A'.$allowedRecipient.'\z}', $recipient->getAddress())) {
                            continue;
                        }
                        // dedup
                        foreach ($recipients as $r) {
                            if ($r->getName() === $recipient->getName() && $r->getAddress() === $recipient->getAddress()) {
                                continue 2;
                            }
                        }

                        $recipients[] = $recipient;
                        continue 2;
                    }
                }
            }

            $event->getEnvelope()->setRecipients($recipients);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // should be the last one to allow header changes by other listeners first
            MessageEvent::class => ['onMessage', -255],
        ];
    }
}
