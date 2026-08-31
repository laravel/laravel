<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

/**
 * Represents a streaming HTTP response for sending server events
 * as part of the Server-Sent Events (SSE) streaming technique.
 *
 * To broadcast events to multiple users at once, for long-running
 * connections and for high-traffic websites, prefer using the Mercure
 * Symfony Component, which relies on Software designed for these use
 * cases: https://symfony.com/doc/current/mercure.html
 *
 * @see ServerEvent
 *
 * @author Yonel Ceruto <open@yceruto.dev>
 *
 * Example usage:
 *
 *     return new EventStreamResponse(function () {
 *         yield new ServerEvent(time());
 *
 *         sleep(1);
 *
 *         yield new ServerEvent(time());
 *     });
 */
class EventStreamResponse extends StreamedResponse
{
    /**
     * @param int|null $retry The number of milliseconds the client should wait
     *                        before reconnecting in case of network failure
     */
    public function __construct(?callable $callback = null, int $status = 200, array $headers = [], private ?int $retry = null)
    {
        $headers += [
            'Connection' => 'keep-alive',
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate, max-age=0',
            'X-Accel-Buffering' => 'no',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        parent::__construct($callback, $status, $headers);
    }

    public function setCallback(callable $callback): static
    {
        if ($this->callback) {
            return parent::setCallback($callback);
        }

        $this->callback = function () use ($callback) {
            if (is_iterable($events = $callback($this))) {
                foreach ($events as $event) {
                    $this->sendEvent($event);

                    if (connection_aborted()) {
                        break;
                    }
                }
            }
        };

        return $this;
    }

    /**
     * Sends a server event to the client.
     *
     * @return $this
     */
    public function sendEvent(ServerEvent $event): static
    {
        if ($this->retry > 0 && !$event->getRetry()) {
            $event->setRetry($this->retry);
        }

        foreach ($event as $part) {
            echo $part;

            if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
                static::closeOutputBuffers(0, true);
                flush();
            }
        }

        return $this;
    }

    public function getRetry(): ?int
    {
        return $this->retry;
    }

    public function setRetry(int $retry): void
    {
        $this->retry = $retry;
    }
}
