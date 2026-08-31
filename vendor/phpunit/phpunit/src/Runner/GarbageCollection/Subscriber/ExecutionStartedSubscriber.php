<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\GarbageCollection;

use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber as TestRunnerExecutionStartedSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ExecutionStartedSubscriber extends Subscriber implements TestRunnerExecutionStartedSubscriber
{
    /**
     * @throws \PHPUnit\Framework\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function notify(ExecutionStarted $event): void
    {
        $this->handler()->executionStarted();
    }
}
