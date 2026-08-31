<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Logging\TestDox;

use function array_keys;
use function array_merge;
use function assert;
use function is_subclass_of;
use function ksort;
use function uksort;
use function usort;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade;
use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\NoticeTriggered;
use PHPUnit\Event\Test\Passed;
use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpNoticeTriggered;
use PHPUnit\Event\Test\PhpunitDeprecationTriggered;
use PHPUnit\Event\Test\PhpunitErrorTriggered;
use PHPUnit\Event\Test\PhpunitWarningTriggered;
use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\Skipped;
use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\Event\UnknownSubscriberTypeException;
use PHPUnit\Framework\TestStatus\TestStatus;
use PHPUnit\Logging\TestDox\TestResult as TestDoxTestMethod;
use PHPUnit\TestRunner\IssueFilter;
use ReflectionMethod;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestResultCollector
{
    private readonly IssueFilter $issueFilter;

    /**
     * @var array<string, list<TestDoxTestMethod>>
     */
    private array $tests          = [];
    private ?TestStatus $status   = null;
    private ?Throwable $throwable = null;
    private bool $prepared        = false;

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     */
    public function __construct(Facade $facade, IssueFilter $issueFilter)
    {
        $this->issueFilter = $issueFilter;

        $this->registerSubscribers($facade);
    }

    /**
     * @return array<string, TestResultCollection>
     */
    public function testMethodsGroupedByClass(): array
    {
        $result = [];

        foreach ($this->tests as $prettifiedClassName => $tests) {
            $testsByDeclaringClass = [];

            foreach ($tests as $test) {
                $declaringClassName = (new ReflectionMethod($test->test()->className(), $test->test()->methodName()))->getDeclaringClass()->getName();

                if (!isset($testsByDeclaringClass[$declaringClassName])) {
                    $testsByDeclaringClass[$declaringClassName] = [];
                }

                $testsByDeclaringClass[$declaringClassName][] = $test;
            }

            foreach (array_keys($testsByDeclaringClass) as $declaringClassName) {
                usort(
                    $testsByDeclaringClass[$declaringClassName],
                    static function (TestDoxTestMethod $a, TestDoxTestMethod $b): int
                    {
                        return $a->test()->line() <=> $b->test()->line();
                    },
                );
            }

            uksort(
                $testsByDeclaringClass,
                /**
                 * @param class-string $a
                 * @param class-string $b
                 */
                static function (string $a, string $b): int
                {
                    if (is_subclass_of($b, $a)) {
                        return -1;
                    }

                    if (is_subclass_of($a, $b)) {
                        return 1;
                    }

                    return 0;
                },
            );

            $tests = [];

            foreach ($testsByDeclaringClass as $_tests) {
                $tests = array_merge($tests, $_tests);
            }

            $result[$prettifiedClassName] = TestResultCollection::fromArray($tests);
        }

        ksort($result);

        return $result;
    }

    public function testPrepared(Prepared $event): void
    {
        if (!$event->test()->isTestMethod()) {
            return;
        }

        $this->status    = TestStatus::unknown();
        $this->throwable = null;
        $this->prepared  = true;
    }

    public function testErrored(Errored $event): void
    {
        if (!$event->test()->isTestMethod()) {
            return;
        }

        $this->status    = TestStatus::error($event->throwable()->message());
        $this->throwable = $event->throwable();

        if (!$this->prepared) {
            $test = $event->test();

            assert($test instanceof TestMethod);

            $this->process($test);
        }
    }

    public function testFailed(Failed $event): void
    {
        if (!$event->test()->isTestMethod()) {
            return;
        }

        $this->status    = TestStatus::failure($event->throwable()->message());
        $this->throwable = $event->throwable();
    }

    public function testPassed(Passed $event): void
    {
        if (!$event->test()->isTestMethod()) {
            return;
        }

        $this->updateTestStatus(TestStatus::success());
    }

    public function testSkipped(Skipped $event): void
    {
        if (!$event->test()->isTestMethod()) {
            return;
        }

        $this->updateTestStatus(TestStatus::skipped($event->message()));
    }

    public function testMarkedIncomplete(MarkedIncomplete $event): void
    {
        if (!$event->test()->isTestMethod()) {
            return;
        }

        $this->updateTestStatus(TestStatus::incomplete($event->throwable()->message()));

        $this->throwable = $event->throwable();
    }

    public function testConsideredRisky(ConsideredRisky $event): void
    {
        if (!$event->test()->isTestMethod()) {
            return;
        }

        $this->updateTestStatus(TestStatus::risky());
    }

    public function testTriggeredDeprecation(DeprecationTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event, true)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            return;
        }

        $this->updateTestStatus(TestStatus::deprecation());
    }

    public function testTriggeredNotice(NoticeTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event, true)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            return;
        }

        $this->updateTestStatus(TestStatus::notice());
    }

    public function testTriggeredWarning(WarningTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event, true)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            return;
        }

        $this->updateTestStatus(TestStatus::warning());
    }

    public function testTriggeredPhpDeprecation(PhpDeprecationTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event, true)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            return;
        }

        $this->updateTestStatus(TestStatus::deprecation());
    }

    public function testTriggeredPhpNotice(PhpNoticeTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event, true)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            return;
        }

        $this->updateTestStatus(TestStatus::notice());
    }

    public function testTriggeredPhpWarning(PhpWarningTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event, true)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            return;
        }

        $this->updateTestStatus(TestStatus::warning());
    }

    public function testTriggeredPhpunitDeprecation(PhpunitDeprecationTriggered $event): void
    {
        if (!$event->test()->isTestMethod()) {
            return;
        }

        $this->updateTestStatus(TestStatus::deprecation());
    }

    public function testTriggeredPhpunitError(PhpunitErrorTriggered $event): void
    {
        if (!$event->test()->isTestMethod()) {
            return;
        }

        $this->updateTestStatus(TestStatus::error());
    }

    public function testTriggeredPhpunitWarning(PhpunitWarningTriggered $event): void
    {
        if (!$event->test()->isTestMethod()) {
            return;
        }

        $this->updateTestStatus(TestStatus::warning());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testFinished(Finished $event): void
    {
        if (!$event->test()->isTestMethod()) {
            return;
        }

        $test = $event->test();

        assert($test instanceof TestMethod);

        $this->process($test);

        $this->status    = null;
        $this->throwable = null;
        $this->prepared  = false;
    }

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     */
    private function registerSubscribers(Facade $facade): void
    {
        $facade->registerSubscribers(
            new TestConsideredRiskySubscriber($this),
            new TestErroredSubscriber($this),
            new TestFailedSubscriber($this),
            new TestFinishedSubscriber($this),
            new TestMarkedIncompleteSubscriber($this),
            new TestPassedSubscriber($this),
            new TestPreparedSubscriber($this),
            new TestSkippedSubscriber($this),
            new TestTriggeredDeprecationSubscriber($this),
            new TestTriggeredNoticeSubscriber($this),
            new TestTriggeredPhpDeprecationSubscriber($this),
            new TestTriggeredPhpNoticeSubscriber($this),
            new TestTriggeredPhpunitDeprecationSubscriber($this),
            new TestTriggeredPhpunitErrorSubscriber($this),
            new TestTriggeredPhpunitWarningSubscriber($this),
            new TestTriggeredPhpWarningSubscriber($this),
            new TestTriggeredWarningSubscriber($this),
        );
    }

    private function updateTestStatus(TestStatus $status): void
    {
        if ($this->status !== null &&
            $this->status->isMoreImportantThan($status)) {
            return;
        }

        $this->status = $status;
    }

    private function process(TestMethod $test): void
    {
        if (!isset($this->tests[$test->testDox()->prettifiedClassName()])) {
            $this->tests[$test->testDox()->prettifiedClassName()] = [];
        }

        $this->tests[$test->testDox()->prettifiedClassName()][] = new TestDoxTestMethod(
            $test,
            $this->status,
            $this->throwable,
        );
    }
}
