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

use PHPUnit\Event\TestSuite\Started;
use PHPUnit\Event\TestSuite\StartedSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestSuiteStartedSubscriber extends Subscriber implements StartedSubscriber
{
    public function notify(Started $event): void
    {
        $this->logger()->testSuiteStarted($event);
    }
}
