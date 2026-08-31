<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Logging\TeamCity;

use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\MarkedIncompleteSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestMarkedIncompleteSubscriber extends Subscriber implements MarkedIncompleteSubscriber
{
    /**
     * @throws InvalidArgumentException
     */
    public function notify(MarkedIncomplete $event): void
    {
        $this->logger()->testMarkedIncomplete($event);
    }
}
