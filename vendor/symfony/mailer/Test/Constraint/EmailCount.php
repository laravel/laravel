<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\Mailer\Event\MessageEvents;

final class EmailCount extends Constraint
{
    public function __construct(
        private int $expectedValue,
        private ?string $transport = null,
        private bool $queued = false,
    ) {
    }

    public function toString(): string
    {
        return \sprintf('%shas %s "%d" emails', $this->transport ? $this->transport.' ' : '', $this->queued ? 'queued' : 'sent', $this->expectedValue);
    }

    /**
     * @param MessageEvents $events
     */
    protected function matches($events): bool
    {
        return $this->expectedValue === $this->countEmails($events);
    }

    /**
     * @param MessageEvents $events
     */
    protected function failureDescription($events): string
    {
        return \sprintf('the Transport %s (%d %s)', $this->toString(), $this->countEmails($events), $this->queued ? 'queued' : 'sent');
    }

    private function countEmails(MessageEvents $events): int
    {
        $count = 0;
        foreach ($events->getEvents($this->transport) as $event) {
            if (
                ($this->queued && $event->isQueued())
                || (!$this->queued && !$event->isQueued())
            ) {
                ++$count;
            }
        }

        return $count;
    }
}
