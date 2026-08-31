<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TestRunner\TestResult;

use PHPUnit\Event\TestRunner\WarningTriggered;
use PHPUnit\Event\TestRunner\WarningTriggeredSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestRunnerTriggeredWarningSubscriber extends Subscriber implements WarningTriggeredSubscriber
{
    public function notify(WarningTriggered $event): void
    {
        $this->collector()->testRunnerTriggeredWarning($event);
    }
}
