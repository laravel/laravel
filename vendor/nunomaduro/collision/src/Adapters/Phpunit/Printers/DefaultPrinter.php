<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Printers;

use Closure;
use NunoMaduro\Collision\Adapters\Phpunit\ConfigureIO;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use NunoMaduro\Collision\Adapters\Phpunit\Style;
use NunoMaduro\Collision\Adapters\Phpunit\Support\ResultReflection;
use NunoMaduro\Collision\Adapters\Phpunit\TestResult;
use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use NunoMaduro\Collision\Exceptions\TestOutcome;
use Pest\Collision\Events;
use Pest\Result;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Code\ThrowableBuilder;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Event\Test\BeforeFirstTestMethodErrored;
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
use PHPUnit\Event\Test\PreparationStarted;
use PHPUnit\Event\Test\PrintedUnexpectedOutput;
use PHPUnit\Event\Test\Skipped;
use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\Event\TestRunner\DeprecationTriggered as TestRunnerDeprecationTriggered;
use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\WarningTriggered as TestRunnerWarningTriggered;
use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\SkippedWithMessageException;
use PHPUnit\TestRunner\TestResult\Facade;
use PHPUnit\TextUI\Configuration\Registry;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @internal
 */
final class DefaultPrinter
{
    /**
     * The output instance.
     */
    private ConsoleOutput $output;

    /**
     * The state instance.
     */
    private State $state;

    /**
     * The style instance.
     */
    private Style $style;

    /**
     * If the printer should be compact.
     */
    private static bool $compact = false;

    /**
     * If the printer should profile.
     */
    private static bool $profile = false;

    /**
     * When profiling, holds a list of slow tests.
     */
    private array $profileSlowTests = [];

    /**
     * The test started at in microseconds.
     */
    private float $testStartedAt = 0.0;

    /**
     * If the printer should be verbose.
     */
    private static bool $verbose = false;

    /**
     * Closures that contribute extra output to the recap line.
     *
     * @var array<int, Closure(State, Info, \PHPUnit\TestRunner\TestResult\TestResult): string>
     */
    private static array $recapCallbacks = [];

    /**
     * Creates a new Printer instance.
     */
    public function __construct(bool $colors)
    {
        $this->output = new ConsoleOutput(OutputInterface::VERBOSITY_NORMAL, $colors);

        ConfigureIO::of(new ArgvInput, $this->output);

        class_exists(Events::class) && Events::setOutput($this->output);

        self::$verbose = $this->output->isVerbose();

        $this->style = new Style($this->output);

        $this->state = new State;
    }

    /**
     * If the printer instances should be compact.
     */
    public static function compact(?bool $value = null): bool
    {
        if (! is_null($value)) {
            self::$compact = $value;
        }

        return ! self::$verbose && self::$compact;
    }

    /**
     * If the printer instances should profile.
     */
    public static function profile(?bool $value = null): bool
    {
        if (! is_null($value)) {
            self::$profile = $value;
        }

        return self::$profile;
    }

    /**
     * Registers a closure that appends output to the recap's assertions line.
     *
     * @param  Closure(State, Info, \PHPUnit\TestRunner\TestResult\TestResult): string  $callback
     */
    public static function addRecap(Closure $callback): void
    {
        self::$recapCallbacks[] = $callback;
    }

    /**
     * @return array<int, Closure(State, Info, \PHPUnit\TestRunner\TestResult\TestResult): string>
     */
    public static function recapCallbacks(): array
    {
        return self::$recapCallbacks;
    }

    /**
     * Removes all registered recap callbacks.
     */
    public static function flushRecapCallbacks(): void
    {
        self::$recapCallbacks = [];
    }

    /**
     * Defines if the output should be decorated or not.
     */
    public function setDecorated(bool $decorated): void
    {
        $this->output->setDecorated($decorated);
    }

    /**
     * Listen to the runner execution started event.
     */
    public function testPrintedUnexpectedOutput(PrintedUnexpectedOutput $printedUnexpectedOutput): void
    {
        $this->output->write($printedUnexpectedOutput->output());
    }

    /**
     * Listen to the runner execution started event.
     */
    public function testRunnerExecutionStarted(ExecutionStarted $executionStarted): void
    {
        // ..
    }

    /**
     * Listen to the test finished event.
     */
    public function testFinished(Finished $event): void
    {
        $duration = (hrtime(true) - $this->testStartedAt) / 1_000_000;

        $test = $event->test();

        if (! $test instanceof TestMethod) {
            throw new ShouldNotHappen;
        }

        if (! $this->state->existsInTestCase($event->test())) {
            $this->state->add(TestResult::fromTestCase($event->test(), TestResult::PASS));
        }

        $result = $this->state->setDuration($test, $duration);

        if (self::$profile) {
            $this->profileSlowTests[$event->test()->id()] = $result;

            // Sort the slow tests by time, and keep only 10 of them.
            uasort($this->profileSlowTests, static function (TestResult $a, TestResult $b) {
                return $b->duration <=> $a->duration;
            });

            $this->profileSlowTests = array_slice($this->profileSlowTests, 0, 10);
        }
    }

    /**
     * Listen to the test prepared event.
     */
    public function testPreparationStarted(PreparationStarted $event): void
    {
        $this->testStartedAt = hrtime(true);

        $test = $event->test();

        if (! $test instanceof TestMethod) {
            throw new ShouldNotHappen;
        }

        $this->ensureCaseBoundary($test);
    }

    /**
     * Flush the previous test case summary if the given test belongs to a new case.
     *
     * Some PHPUnit events (e.g. deprecation triggers from class autoloading) fire
     * for a test before its `testPreparationStarted`. Without flushing here, the
     * previous case's tests would be silently merged under the new case's header.
     */
    private function ensureCaseBoundary(Test $test): void
    {
        if (! $test instanceof TestMethod) {
            return;
        }

        if ($this->state->testCaseHasChanged($test)) {
            $this->style->writeCurrentTestCaseSummary($this->state);

            $this->state->moveTo($test);
        }
    }

    /**
     * Listen to the test errored event.
     */
    public function testBeforeFirstTestMethodErrored(BeforeFirstTestMethodErrored $event): void
    {
        $this->state->add(TestResult::fromBeforeFirstTestMethodErrored($event));
    }

    /**
     * Listen to the test errored event.
     */
    public function testErrored(Errored $event): void
    {
        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::FAIL, $event->throwable()));
    }

    /**
     * Listen to the test failed event.
     */
    public function testFailed(Failed $event): void
    {
        $throwable = $event->throwable();

        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::FAIL, $throwable));
    }

    /**
     * Listen to the test marked incomplete event.
     */
    public function testMarkedIncomplete(MarkedIncomplete $event): void
    {
        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::INCOMPLETE, $event->throwable()));
    }

    /**
     * Listen to the test considered risky event.
     */
    public function testConsideredRisky(ConsideredRisky $event): void
    {
        $throwable = ThrowableBuilder::from(new IncompleteTestError($event->message()));

        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::RISKY, $throwable));
    }

    /**
     * Listen to the test runner deprecation triggered.
     */
    public function testRunnerDeprecationTriggered(TestRunnerDeprecationTriggered $event): void
    {
        $this->style->writeWarning($event->message());
    }

    /**
     * Listen to the test runner warning triggered.
     */
    public function testRunnerWarningTriggered(TestRunnerWarningTriggered $event): void
    {
        if (! str_starts_with($event->message(), 'No tests found in class')) {
            $this->style->writeWarning($event->message());
        }
    }

    /**
     * Listen to the test runner warning triggered.
     */
    public function testPhpDeprecationTriggered(PhpDeprecationTriggered $event): void
    {
        $throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

        $this->ensureCaseBoundary($event->test());
        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::DEPRECATED, $throwable));
    }

    /**
     * Listen to the test runner notice triggered.
     */
    public function testPhpNoticeTriggered(PhpNoticeTriggered $event): void
    {
        $throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

        $this->ensureCaseBoundary($event->test());
        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::NOTICE, $throwable));
    }

    /**
     * Listen to the test php warning triggered event.
     */
    public function testPhpWarningTriggered(PhpWarningTriggered $event): void
    {
        $throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

        $this->ensureCaseBoundary($event->test());
        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::WARN, $throwable));
    }

    /**
     * Listen to the test runner warning triggered.
     */
    public function testPhpunitWarningTriggered(PhpunitWarningTriggered $event): void
    {
        $throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

        $this->ensureCaseBoundary($event->test());
        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::WARN, $throwable));
    }

    /**
     * Listen to the test deprecation triggered event.
     */
    public function testDeprecationTriggered(DeprecationTriggered $event): void
    {
        $throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

        $this->ensureCaseBoundary($event->test());
        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::DEPRECATED, $throwable));
    }

    /**
     * Listen to the test phpunit deprecation triggered event.
     */
    public function testPhpunitDeprecationTriggered(PhpunitDeprecationTriggered $event): void
    {
        $throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

        $this->ensureCaseBoundary($event->test());
        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::DEPRECATED, $throwable));
    }

    /**
     * Listen to the test phpunit error triggered event.
     */
    public function testPhpunitErrorTriggered(PhpunitErrorTriggered $event): void
    {
        $throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::FAIL, $throwable));
    }

    /**
     * Listen to the test warning triggered event.
     */
    public function testNoticeTriggered(NoticeTriggered $event): void
    {
        $throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

        $this->ensureCaseBoundary($event->test());
        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::NOTICE, $throwable));
    }

    /**
     * Listen to the test warning triggered event.
     */
    public function testWarningTriggered(WarningTriggered $event): void
    {
        $throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

        $this->ensureCaseBoundary($event->test());
        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::WARN, $throwable));
    }

    /**
     * Listen to the test skipped event.
     */
    public function testSkipped(Skipped $event): void
    {
        if ($event->message() === '__TODO__') {
            $this->state->add(TestResult::fromTestCase($event->test(), TestResult::TODO));

            return;
        }

        $throwable = ThrowableBuilder::from(new SkippedWithMessageException($event->message()));

        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::SKIPPED, $throwable));
    }

    /**
     * Listen to the test finished event.
     */
    public function testPassed(Passed $event): void
    {
        if (! $this->state->existsInTestCase($event->test())) {
            $this->state->add(TestResult::fromTestCase($event->test(), TestResult::PASS));
        }
    }

    /**
     * Listen to the runner execution finished event.
     */
    public function testRunnerExecutionFinished(ExecutionFinished $event): void
    {
        $result = Facade::result();

        if (ResultReflection::numberOfTests(Facade::result()) === 0) {
            $this->output->writeln([
                '',
                '  <fg=white;options=bold;bg=blue> INFO </> No tests found.',
                '',
            ]);

            return;
        }

        $this->style->writeCurrentTestCaseSummary($this->state);

        if (self::$compact) {
            $this->output->writeln(['']);
        }

        if (class_exists(Result::class)) {
            $failed = Result::failed(Registry::get(), Facade::result());
        } else {
            $failed = ! Facade::result()->wasSuccessful();
        }

        $this->style->writeErrorsSummary($this->state);

        $this->style->writeRecap($this->state, $event->telemetryInfo(), $result);

        if (! $failed && count($this->profileSlowTests) > 0) {
            $this->style->writeSlowTests($this->profileSlowTests, $event->telemetryInfo());
        }
    }

    /**
     * Reports the given throwable.
     */
    public function report(Throwable $throwable): void
    {
        $this->style->writeError(ThrowableBuilder::from($throwable));
    }
}
