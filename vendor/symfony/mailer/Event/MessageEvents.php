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

use Symfony\Component\Mime\RawMessage;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MessageEvents
{
    /**
     * @var MessageEvent[]
     */
    private array $events = [];

    /**
     * @var array<string, bool>
     */
    private array $transports = [];

    public function add(MessageEvent $event): void
    {
        $this->events[] = $event;
        $this->transports[$event->getTransport()] = true;
    }

    public function getTransports(): array
    {
        return array_keys($this->transports);
    }

    /**
     * @return MessageEvent[]
     */
    public function getEvents(?string $name = null): array
    {
        if (null === $name) {
            return $this->events;
        }

        $events = [];
        foreach ($this->events as $event) {
            if ($name === $event->getTransport()) {
                $events[] = $event;
            }
        }

        return $events;
    }

    /**
     * @return RawMessage[]
     */
    public function getMessages(?string $name = null): array
    {
        $events = $this->getEvents($name);
        $messages = [];
        foreach ($events as $event) {
            $messages[] = $event->getMessage();
        }

        return $messages;
    }
}
