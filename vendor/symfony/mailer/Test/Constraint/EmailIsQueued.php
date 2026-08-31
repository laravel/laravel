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
use Symfony\Component\Mailer\Event\MessageEvent;

final class EmailIsQueued extends Constraint
{
    public function toString(): string
    {
        return 'is queued';
    }

    /**
     * @param MessageEvent $event
     */
    protected function matches($event): bool
    {
        return $event->isQueued();
    }

    /**
     * @param MessageEvent $event
     */
    protected function failureDescription($event): string
    {
        return 'the Email '.$this->toString();
    }
}
