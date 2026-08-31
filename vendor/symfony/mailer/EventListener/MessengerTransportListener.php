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
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;
use Symfony\Component\Mime\Message;

/**
 * Allows messages to be sent to specific Messenger transports via the "X-Bus-Transport" MIME header.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class MessengerTransportListener implements EventSubscriberInterface
{
    public function onMessage(MessageEvent $event): void
    {
        if (!$event->isQueued()) {
            return;
        }

        $message = $event->getMessage();
        if (!$message instanceof Message || !$message->getHeaders()->has('X-Bus-Transport')) {
            return;
        }

        $names = $message->getHeaders()->get('X-Bus-Transport')->getBody();
        $names = array_map('trim', explode(',', $names));
        $event->addStamp(new TransportNamesStamp($names));
        $message->getHeaders()->remove('X-Bus-Transport');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }
}
