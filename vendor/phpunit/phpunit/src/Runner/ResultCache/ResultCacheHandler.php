<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\ResultCache;

use function round;
use PHPUnit\Event\Event;
use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade;
use PHPUnit\Event\Telemetry\HRTime;
use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\Skipped;
use PHPUnit\Event\UnknownSubscriberTypeException;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\TestStatus\TestStatus;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ResultCacheHandler
{
    private readonly ResultCache $cache;
    private ?HRTime $time  = null;
    private int $testSuite = 0;

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     */
    public function __construct(ResultCache $cache, Facade $facade)
    {
        $this->cache = $cache;

        $this->registerSubscribers($facade);
    }

    public function testSuiteStarted(): void
    {
        $this->testSuite++;
    }

    public function testSuiteFinished(): void
    {
        $this->testSuite--;

        if ($this->testSuite === 0) {
            $this->cache->persist();
        }
    }

    public function testPrepared(Prepared $event): void
    {
        $this->time = $event->telemetryInfo()->time();
    }

    public function testMarkedIncomplete(MarkedIncomplete $event): void
    {
        $this->cache->setStatus(
            ResultCacheId::fromTest($event->test()),
            TestStatus::incomplete($event->throwable()->message()),
        );
    }

    public function testConsideredRisky(ConsideredRisky $event): void
    {
        $this->cache->setStatus(
            ResultCacheId::fromTest($event->test()),
            TestStatus::risky($event->message()),
        );
    }

    public function testErrored(Errored $event): void
    {
        $this->cache->setStatus(
            ResultCacheId::fromTest($event->test()),
            TestStatus::error($event->throwable()->message()),
        );
    }

    public function testFailed(Failed $event): void
    {
        $this->cache->setStatus(
            ResultCacheId::fromTest($event->test()),
            TestStatus::failure($event->throwable()->message()),
        );
    }

    /**
     * @throws \PHPUnit\Event\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function testSkipped(Skipped $event): void
    {
        $this->cache->setStatus(
            ResultCacheId::fromTest($event->test()),
            TestStatus::skipped($event->message()),
        );

        $this->cache->setTime(ResultCacheId::fromTest($event->test()), $this->duration($event));
    }

    /**
     * @throws \PHPUnit\Event\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function testFinished(Finished $event): void
    {
        $this->cache->setTime(ResultCacheId::fromTest($event->test()), $this->duration($event));

        $this->time = null;
    }

    /**
     * @throws \PHPUnit\Event\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    private function duration(Event $event): float
    {
        if ($this->time === null) {
            return 0.0;
        }

        return round($event->telemetryInfo()->time()->duration($this->time)->asFloat(), 3);
    }

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     */
    private function registerSubscribers(Facade $facade): void
    {
        $facade->registerSubscribers(
            new TestSuiteStartedSubscriber($this),
            new TestSuiteFinishedSubscriber($this),
            new TestPreparedSubscriber($this),
            new TestMarkedIncompleteSubscriber($this),
            new TestConsideredRiskySubscriber($this),
            new TestErroredSubscriber($this),
            new TestFailedSubscriber($this),
            new TestSkippedSubscriber($this),
            new TestFinishedSubscriber($this),
        );
    }
}
