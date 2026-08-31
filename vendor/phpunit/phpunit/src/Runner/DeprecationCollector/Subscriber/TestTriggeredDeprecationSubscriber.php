<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\DeprecationCollector;

use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\DeprecationTriggeredSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestTriggeredDeprecationSubscriber extends Subscriber implements DeprecationTriggeredSubscriber
{
    public function notify(DeprecationTriggered $event): void
    {
        $this->collector()->testTriggeredDeprecation($event);
    }
}
