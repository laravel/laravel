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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\RawMessage;

/**
 * Uses several Transports using a round robin algorithm.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RoundRobinTransport implements TransportInterface
{
    /**
     * @var \SplObjectStorage<TransportInterface, float>
     */
    private \SplObjectStorage $deadTransports;
    private int $cursor = -1;

    /**
     * @param TransportInterface[] $transports
     */
    public function __construct(
        private array $transports,
        private int $retryPeriod = 60,
        private LoggerInterface $logger = new NullLogger(),
    ) {
        if (!$transports) {
            throw new TransportException(\sprintf('"%s" must have at least one transport configured.', static::class));
        }

        $this->deadTransports = new \SplObjectStorage();
    }

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        $exception = null;

        while ($transport = $this->getNextTransport()) {
            try {
                return $transport->send(clone $message, $envelope);
            } catch (TransportExceptionInterface $e) {
                $exception ??= new TransportException('All transports failed.');
                $exception->appendDebug(\sprintf("Transport \"%s\": %s\n", $transport, $e->getDebug()));
                $this->logger->error(\sprintf('Transport "%s" failed.', $transport), ['exception' => $e]);
                $this->deadTransports[$transport] = microtime(true);
            }
        }

        throw $exception ?? new TransportException('No transports found.');
    }

    public function __toString(): string
    {
        return $this->getNameSymbol().'('.implode(' ', array_map('strval', $this->transports)).')';
    }

    /**
     * Rotates the transport list around and returns the first instance.
     */
    protected function getNextTransport(): ?TransportInterface
    {
        if (-1 === $this->cursor) {
            $this->cursor = $this->getInitialCursor();
        }

        $cursor = $this->cursor;
        while (true) {
            $transport = $this->transports[$cursor];

            if (!$this->isTransportDead($transport)) {
                break;
            }

            if ((microtime(true) - $this->deadTransports[$transport]) > $this->retryPeriod) {
                unset($this->deadTransports[$transport]);

                break;
            }

            if ($this->cursor === $cursor = $this->moveCursor($cursor)) {
                return null;
            }
        }

        $this->cursor = $this->moveCursor($cursor);

        return $transport;
    }

    protected function isTransportDead(TransportInterface $transport): bool
    {
        return $this->deadTransports->offsetExists($transport);
    }

    protected function getInitialCursor(): int
    {
        // the cursor initial value is randomized so that
        // when are not in a daemon, we are still rotating the transports
        return mt_rand(0, \count($this->transports) - 1);
    }

    protected function getNameSymbol(): string
    {
        return 'roundrobin';
    }

    private function moveCursor(int $cursor): int
    {
        return ++$cursor >= \count($this->transports) ? 0 : $cursor;
    }
}

// @php-cs-fixer-ignore random_api_migration
