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

use function array_values;
use function assert;
use function implode;
use function str_contains;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade;
use PHPUnit\Event\Test\AfterLastTestMethodErrored;
use PHPUnit\Event\Test\BeforeFirstTestMethodErrored;
use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\ErrorTriggered;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\NoticeTriggered;
use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpNoticeTriggered;
use PHPUnit\Event\Test\PhpunitDeprecationTriggered;
use PHPUnit\Event\Test\PhpunitErrorTriggered;
use PHPUnit\Event\Test\PhpunitWarningTriggered;
use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\Skipped as TestSkipped;
use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\Event\TestRunner\DeprecationTriggered as TestRunnerDeprecationTriggered;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\WarningTriggered as TestRunnerWarningTriggered;
use PHPUnit\Event\TestSuite\Finished as TestSuiteFinished;
use PHPUnit\Event\TestSuite\Skipped as TestSuiteSkipped;
use PHPUnit\Event\TestSuite\Started as TestSuiteStarted;
use PHPUnit\Event\TestSuite\TestSuiteForTestClass;
use PHPUnit\Event\TestSuite\TestSuiteForTestMethodWithDataProvider;
use PHPUnit\Event\UnknownSubscriberTypeException;
use PHPUnit\TestRunner\IssueFilter;
use PHPUnit\TestRunner\TestResult\Issues\Issue;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Collector
{
    private readonly IssueFilter $issueFilter;
    private int $numberOfTests      = 0;
    private int $numberOfTestsRun   = 0;
    private int $numberOfAssertions = 0;
    private bool $prepared          = false;

    /**
     * @var non-negative-int
     */
    private int $numberOfIssuesIgnoredByBaseline = 0;

    /**
     * @var list<AfterLastTestMethodErrored|BeforeFirstTestMethodErrored|Errored>
     */
    private array $testErroredEvents = [];

    /**
     * @var list<Failed>
     */
    private array $testFailedEvents = [];

    /**
     * @var list<MarkedIncomplete>
     */
    private array $testMarkedIncompleteEvents = [];

    /**
     * @var list<TestSuiteSkipped>
     */
    private array $testSuiteSkippedEvents = [];

    /**
     * @var list<TestSkipped>
     */
    private array $testSkippedEvents = [];

    /**
     * @var array<string,list<ConsideredRisky>>
     */
    private array $testConsideredRiskyEvents = [];

    /**
     * @var array<string,list<PhpunitDeprecationTriggered>>
     */
    private array $testTriggeredPhpunitDeprecationEvents = [];

    /**
     * @var array<string,list<PhpunitErrorTriggered>>
     */
    private array $testTriggeredPhpunitErrorEvents = [];

    /**
     * @var array<string,list<PhpunitWarningTriggered>>
     */
    private array $testTriggeredPhpunitWarningEvents = [];

    /**
     * @var list<TestRunnerWarningTriggered>
     */
    private array $testRunnerTriggeredWarningEvents = [];

    /**
     * @var list<TestRunnerDeprecationTriggered>
     */
    private array $testRunnerTriggeredDeprecationEvents = [];

    /**
     * @var array<non-empty-string, Issue>
     */
    private array $errors = [];

    /**
     * @var array<non-empty-string, Issue>
     */
    private array $deprecations = [];

    /**
     * @var array<non-empty-string, Issue>
     */
    private array $notices = [];

    /**
     * @var array<non-empty-string, Issue>
     */
    private array $warnings = [];

    /**
     * @var array<non-empty-string, Issue>
     */
    private array $phpDeprecations = [];

    /**
     * @var array<non-empty-string, Issue>
     */
    private array $phpNotices = [];

    /**
     * @var array<non-empty-string, Issue>
     */
    private array $phpWarnings = [];

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     */
    public function __construct(Facade $facade, IssueFilter $issueFilter)
    {
        $facade->registerSubscribers(
            new ExecutionStartedSubscriber($this),
            new TestSuiteSkippedSubscriber($this),
            new TestSuiteStartedSubscriber($this),
            new TestSuiteFinishedSubscriber($this),
            new TestPreparedSubscriber($this),
            new TestFinishedSubscriber($this),
            new BeforeTestClassMethodErroredSubscriber($this),
            new AfterTestClassMethodErroredSubscriber($this),
            new TestErroredSubscriber($this),
            new TestFailedSubscriber($this),
            new TestMarkedIncompleteSubscriber($this),
            new TestSkippedSubscriber($this),
            new TestConsideredRiskySubscriber($this),
            new TestTriggeredDeprecationSubscriber($this),
            new TestTriggeredErrorSubscriber($this),
            new TestTriggeredNoticeSubscriber($this),
            new TestTriggeredPhpDeprecationSubscriber($this),
            new TestTriggeredPhpNoticeSubscriber($this),
            new TestTriggeredPhpunitDeprecationSubscriber($this),
            new TestTriggeredPhpunitErrorSubscriber($this),
            new TestTriggeredPhpunitWarningSubscriber($this),
            new TestTriggeredPhpWarningSubscriber($this),
            new TestTriggeredWarningSubscriber($this),
            new TestRunnerTriggeredDeprecationSubscriber($this),
            new TestRunnerTriggeredWarningSubscriber($this),
        );

        $this->issueFilter = $issueFilter;
    }

    public function result(): TestResult
    {
        return new TestResult(
            $this->numberOfTests,
            $this->numberOfTestsRun,
            $this->numberOfAssertions,
            $this->testErroredEvents,
            $this->testFailedEvents,
            $this->testConsideredRiskyEvents,
            $this->testSuiteSkippedEvents,
            $this->testSkippedEvents,
            $this->testMarkedIncompleteEvents,
            $this->testTriggeredPhpunitDeprecationEvents,
            $this->testTriggeredPhpunitErrorEvents,
            $this->testTriggeredPhpunitWarningEvents,
            $this->testRunnerTriggeredDeprecationEvents,
            $this->testRunnerTriggeredWarningEvents,
            array_values($this->errors),
            array_values($this->deprecations),
            array_values($this->notices),
            array_values($this->warnings),
            array_values($this->phpDeprecations),
            array_values($this->phpNotices),
            array_values($this->phpWarnings),
            $this->numberOfIssuesIgnoredByBaseline,
        );
    }

    public function executionStarted(ExecutionStarted $event): void
    {
        $this->numberOfTests = $event->testSuite()->count();
    }

    public function testSuiteSkipped(TestSuiteSkipped $event): void
    {
        $testSuite = $event->testSuite();

        if (!$testSuite->isForTestClass()) {
            return;
        }

        $this->testSuiteSkippedEvents[] = $event;

        $this->numberOfTestsRun += $event->testSuite()->count();
    }

    public function testSuiteStarted(TestSuiteStarted $event): void
    {
        $testSuite = $event->testSuite();

        if (!$testSuite->isForTestClass()) {
            return;
        }
    }

    public function testSuiteFinished(TestSuiteFinished $event): void
    {
        $testSuite = $event->testSuite();

        if ($testSuite->isWithName()) {
            return;
        }

        if ($testSuite->isForTestMethodWithDataProvider()) {
            assert($testSuite instanceof TestSuiteForTestMethodWithDataProvider);

            $test = $testSuite->tests()->asArray()[0];

            assert($test instanceof TestMethod);

            foreach ($this->testFailedEvents as $testFailedEvent) {
                if ($testFailedEvent->test()->isTestMethod() && $testFailedEvent->test()->methodName() === $test->methodName()) {
                    return;
                }
            }

            PassedTests::instance()->testMethodPassed($test, null);

            return;
        }

        assert($testSuite instanceof TestSuiteForTestClass);

        PassedTests::instance()->testClassPassed($testSuite->className());
    }

    public function testPrepared(): void
    {
        $this->prepared = true;
    }

    public function testFinished(Finished $event): void
    {
        $this->numberOfAssertions += $event->numberOfAssertionsPerformed();

        $this->numberOfTestsRun++;

        $this->prepared = false;
    }

    public function beforeTestClassMethodErrored(BeforeFirstTestMethodErrored $event): void
    {
        $this->testErroredEvents[] = $event;

        $this->numberOfTestsRun++;
    }

    public function afterTestClassMethodErrored(AfterLastTestMethodErrored $event): void
    {
        $this->testErroredEvents[] = $event;
    }

    public function testErrored(Errored $event): void
    {
        $this->testErroredEvents[] = $event;

        /*
         * @todo Eliminate this special case
         */
        if (str_contains($event->asString(), 'Test was run in child process and ended unexpectedly')) {
            return;
        }

        if (!$this->prepared) {
            $this->numberOfTestsRun++;
        }
    }

    public function testFailed(Failed $event): void
    {
        $this->testFailedEvents[] = $event;
    }

    public function testMarkedIncomplete(MarkedIncomplete $event): void
    {
        $this->testMarkedIncompleteEvents[] = $event;
    }

    public function testSkipped(TestSkipped $event): void
    {
        $this->testSkippedEvents[] = $event;

        if (!$this->prepared) {
            $this->numberOfTestsRun++;
        }
    }

    public function testConsideredRisky(ConsideredRisky $event): void
    {
        if (!isset($this->testConsideredRiskyEvents[$event->test()->id()])) {
            $this->testConsideredRiskyEvents[$event->test()->id()] = [];
        }

        $this->testConsideredRiskyEvents[$event->test()->id()][] = $event;
    }

    public function testTriggeredDeprecation(DeprecationTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            $this->numberOfIssuesIgnoredByBaseline++;

            return;
        }

        $id = $this->issueId($event);

        if (!isset($this->deprecations[$id])) {
            $this->deprecations[$id] = Issue::from(
                $event->file(),
                $event->line(),
                $event->message(),
                $event->test(),
                $event->stackTrace(),
            );

            return;
        }

        $this->deprecations[$id]->triggeredBy($event->test());
    }

    public function testTriggeredPhpDeprecation(PhpDeprecationTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            $this->numberOfIssuesIgnoredByBaseline++;

            return;
        }

        $id = $this->issueId($event);

        if (!isset($this->phpDeprecations[$id])) {
            $this->phpDeprecations[$id] = Issue::from(
                $event->file(),
                $event->line(),
                $event->message(),
                $event->test(),
            );

            return;
        }

        $this->phpDeprecations[$id]->triggeredBy($event->test());
    }

    public function testTriggeredPhpunitDeprecation(PhpunitDeprecationTriggered $event): void
    {
        if (!isset($this->testTriggeredPhpunitDeprecationEvents[$event->test()->id()])) {
            $this->testTriggeredPhpunitDeprecationEvents[$event->test()->id()] = [];
        }

        $this->testTriggeredPhpunitDeprecationEvents[$event->test()->id()][] = $event;
    }

    public function testTriggeredError(ErrorTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event)) {
            return;
        }

        $id = $this->issueId($event);

        if (!isset($this->errors[$id])) {
            $this->errors[$id] = Issue::from(
                $event->file(),
                $event->line(),
                $event->message(),
                $event->test(),
            );

            return;
        }

        $this->errors[$id]->triggeredBy($event->test());
    }

    public function testTriggeredNotice(NoticeTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            $this->numberOfIssuesIgnoredByBaseline++;

            return;
        }

        $id = $this->issueId($event);

        if (!isset($this->notices[$id])) {
            $this->notices[$id] = Issue::from(
                $event->file(),
                $event->line(),
                $event->message(),
                $event->test(),
            );

            return;
        }

        $this->notices[$id]->triggeredBy($event->test());
    }

    public function testTriggeredPhpNotice(PhpNoticeTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            $this->numberOfIssuesIgnoredByBaseline++;

            return;
        }

        $id = $this->issueId($event);

        if (!isset($this->phpNotices[$id])) {
            $this->phpNotices[$id] = Issue::from(
                $event->file(),
                $event->line(),
                $event->message(),
                $event->test(),
            );

            return;
        }

        $this->phpNotices[$id]->triggeredBy($event->test());
    }

    public function testTriggeredWarning(WarningTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            $this->numberOfIssuesIgnoredByBaseline++;

            return;
        }

        $id = $this->issueId($event);

        if (!isset($this->warnings[$id])) {
            $this->warnings[$id] = Issue::from(
                $event->file(),
                $event->line(),
                $event->message(),
                $event->test(),
            );

            return;
        }

        $this->warnings[$id]->triggeredBy($event->test());
    }

    public function testTriggeredPhpWarning(PhpWarningTriggered $event): void
    {
        if (!$this->issueFilter->shouldBeProcessed($event)) {
            return;
        }

        if ($event->ignoredByBaseline()) {
            $this->numberOfIssuesIgnoredByBaseline++;

            return;
        }

        $id = $this->issueId($event);

        if (!isset($this->phpWarnings[$id])) {
            $this->phpWarnings[$id] = Issue::from(
                $event->file(),
                $event->line(),
                $event->message(),
                $event->test(),
            );

            return;
        }

        $this->phpWarnings[$id]->triggeredBy($event->test());
    }

    public function testTriggeredPhpunitError(PhpunitErrorTriggered $event): void
    {
        if (!isset($this->testTriggeredPhpunitErrorEvents[$event->test()->id()])) {
            $this->testTriggeredPhpunitErrorEvents[$event->test()->id()] = [];
        }

        $this->testTriggeredPhpunitErrorEvents[$event->test()->id()][] = $event;
    }

    public function testTriggeredPhpunitWarning(PhpunitWarningTriggered $event): void
    {
        if (!isset($this->testTriggeredPhpunitWarningEvents[$event->test()->id()])) {
            $this->testTriggeredPhpunitWarningEvents[$event->test()->id()] = [];
        }

        $this->testTriggeredPhpunitWarningEvents[$event->test()->id()][] = $event;
    }

    public function testRunnerTriggeredDeprecation(TestRunnerDeprecationTriggered $event): void
    {
        $this->testRunnerTriggeredDeprecationEvents[] = $event;
    }

    public function testRunnerTriggeredWarning(TestRunnerWarningTriggered $event): void
    {
        $this->testRunnerTriggeredWarningEvents[] = $event;
    }

    public function hasErroredTests(): bool
    {
        return !empty($this->testErroredEvents);
    }

    public function hasFailedTests(): bool
    {
        return !empty($this->testFailedEvents);
    }

    public function hasRiskyTests(): bool
    {
        return !empty($this->testConsideredRiskyEvents);
    }

    public function hasSkippedTests(): bool
    {
        return !empty($this->testSkippedEvents);
    }

    public function hasIncompleteTests(): bool
    {
        return !empty($this->testMarkedIncompleteEvents);
    }

    public function hasDeprecations(): bool
    {
        return !empty($this->deprecations) ||
               !empty($this->phpDeprecations) ||
               !empty($this->testTriggeredPhpunitDeprecationEvents) ||
               !empty($this->testRunnerTriggeredDeprecationEvents);
    }

    public function hasNotices(): bool
    {
        return !empty($this->notices) ||
               !empty($this->phpNotices);
    }

    public function hasWarnings(): bool
    {
        return !empty($this->warnings) ||
               !empty($this->phpWarnings) ||
               !empty($this->testTriggeredPhpunitWarningEvents) ||
               !empty($this->testRunnerTriggeredWarningEvents);
    }

    /**
     * @return non-empty-string
     */
    private function issueId(DeprecationTriggered|ErrorTriggered|NoticeTriggered|PhpDeprecationTriggered|PhpNoticeTriggered|PhpWarningTriggered|WarningTriggered $event): string
    {
        return implode(':', [$event->file(), $event->line(), $event->message()]);
    }
}
