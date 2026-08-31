<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit;

use Closure;
use NunoMaduro\Collision\Adapters\Phpunit\Printers\DefaultPrinter;
use NunoMaduro\Collision\Adapters\Phpunit\Support\ResultReflection;
use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use NunoMaduro\Collision\Exceptions\TestException;
use NunoMaduro\Collision\Exceptions\TestOutcome;
use NunoMaduro\Collision\Writer;
use Pest\Collision\Events;
use Pest\Expectation;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\SkippedWithMessageException;
use PHPUnit\Runner\TestSuiteSorter;
use PHPUnit\TestRunner\TestResult\TestResult as PHPUnitTestResult;
use PHPUnit\TextUI\Configuration\Registry;
use ReflectionClass;
use ReflectionFunction;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Termwind\Terminal;
use Whoops\Exception\Frame;
use Whoops\Exception\Inspector;

use function Termwind\render;
use function Termwind\renderUsing;
use function Termwind\terminal;

/**
 * @internal
 */
final class Style
{
    private int $compactProcessed = 0;

    private int $compactSymbolsPerLine = 0;

    private readonly Terminal $terminal;

    private readonly ConsoleOutput $output;

    /**
     * @var string[]
     */
    private const TYPES = [TestResult::DEPRECATED, TestResult::FAIL, TestResult::WARN, TestResult::RISKY, TestResult::INCOMPLETE, TestResult::NOTICE, TestResult::TODO, TestResult::SKIPPED, TestResult::PASS];

    /**
     * Style constructor.
     */
    public function __construct(ConsoleOutputInterface $output)
    {
        if (! $output instanceof ConsoleOutput) {
            throw new ShouldNotHappen;
        }

        $this->terminal = terminal();
        $this->output = $output;

        $this->compactSymbolsPerLine = $this->terminal->width() - 4;
    }

    /**
     * Prints the content similar too:.
     *
     * ```
     *    WARN  Your XML configuration validates against a deprecated schema...
     * ```
     */
    public function writeWarning(string $message): void
    {
        $this->output->writeln(['', '  <fg=black;bg=yellow;options=bold> WARN </> '.$message]);
    }

    /**
     * Prints the content similar too:.
     *
     * ```
     *    WARN  Your XML configuration validates against a deprecated schema...
     * ```
     */
    public function writeThrowable(\Throwable $throwable): void
    {
        $this->output->writeln(['', '  <fg=white;bg=red;options=bold> ERROR </> '.$throwable->getMessage()]);
    }

    /**
     * Prints the content similar too:.
     *
     * ```
     *    PASS  Unit\ExampleTest
     *    ✓ basic test
     * ```
     */
    public function writeCurrentTestCaseSummary(State $state): void
    {
        if ($state->testCaseTestsCount() === 0 || is_null($state->testCaseName)) {
            return;
        }

        if (! $state->headerPrinted && ! DefaultPrinter::compact()) {
            $this->output->writeln($this->titleLineFrom(
                $state->getTestCaseFontColor(),
                $state->getTestCaseTitleColor(),
                $state->getTestCaseTitle(),
                $state->testCaseName,
                $state->todosCount(),
            ));
            $state->headerPrinted = true;
        }

        $state->eachTestCaseTests(function (TestResult $testResult): void {
            if ($testResult->description !== '') {
                if (DefaultPrinter::compact()) {
                    $this->writeCompactDescriptionLine($testResult);
                } else {
                    $this->writeDescriptionLine($testResult);
                }
            }
        });
    }

    /**
     * Prints the content similar too:.
     *
     * ```
     *    PASS  Unit\ExampleTest
     *    ✓ basic test
     * ```
     */
    public function writeErrorsSummary(State $state): void
    {
        $configuration = Registry::get();
        $failTypes = [
            TestResult::FAIL,
        ];

        if ($configuration->displayDetailsOnTestsThatTriggerNotices()) {
            $failTypes[] = TestResult::NOTICE;
        }

        if ($configuration->displayDetailsOnTestsThatTriggerDeprecations()) {
            $failTypes[] = TestResult::DEPRECATED;
        }

        if ($configuration->failOnWarning() || $configuration->displayDetailsOnTestsThatTriggerWarnings()) {
            $failTypes[] = TestResult::WARN;
        }

        if ($configuration->failOnRisky()) {
            $failTypes[] = TestResult::RISKY;
        }

        if ($configuration->failOnIncomplete() || $configuration->displayDetailsOnIncompleteTests()) {
            $failTypes[] = TestResult::INCOMPLETE;
        }

        if ($configuration->failOnSkipped() || $configuration->displayDetailsOnSkippedTests()) {
            $failTypes[] = TestResult::SKIPPED;
        }

        $failTypes = array_unique($failTypes);

        $errors = array_values(array_filter($state->suiteTests, fn (TestResult $testResult) => in_array(
            $testResult->type,
            $failTypes,
            true
        )));

        array_map(function (TestResult $testResult): void {
            if (! $testResult->throwable instanceof Throwable) {
                throw new ShouldNotHappen;
            }

            renderUsing($this->output);
            render(<<<'HTML'
                <div class="mx-2 text-red">
                    <hr/>
                </div>
            HTML
            );

            $testCaseName = $testResult->testCaseName;
            $description = $testResult->description;

            /** @var class-string $throwableClassName */
            $throwableClassName = $testResult->throwable->className();

            $throwableClassName = ! in_array($throwableClassName, [
                ExpectationFailedException::class,
                IncompleteTestError::class,
                SkippedWithMessageException::class,
                TestOutcome::class,
            ], true) ? sprintf('<span class="px-1 bg-red font-bold">%s</span>', (new ReflectionClass($throwableClassName))->getShortName())
                : '';

            $truncateClasses = $this->output->isVerbose() ? '' : 'flex-1 truncate';

            renderUsing($this->output);
            render(sprintf(<<<'HTML'
                <div class="flex justify-between mx-2">
                    <span class="%s">
                        <span class="px-1 bg-%s %s font-bold uppercase">%s</span> <span class="font-bold">%s</span><span class="text-gray mx-1">></span><span>%s</span>
                    </span>
                    <span class="ml-1">
                        %s
                    </span>
                </div>
            HTML, $truncateClasses, $testResult->color === 'yellow' ? 'yellow-400' : $testResult->color, $testResult->color === 'yellow' ? 'text-black' : '', $testResult->type, $testCaseName, $description, $throwableClassName));

            $this->writeError($testResult->throwable);
        }, $errors);
    }

    /**
     * Writes the final recap.
     */
    public function writeRecap(State $state, Info $telemetry, PHPUnitTestResult $result): void
    {
        $tests = [];
        foreach (self::TYPES as $type) {
            if (($countTests = $state->countTestsInTestSuiteBy($type)) !== 0) {
                $color = TestResult::makeColor($type);

                if ($type === TestResult::WARN && $countTests < 2) {
                    $type = 'warning';
                }

                if ($type === TestResult::NOTICE && $countTests > 1) {
                    $type = 'notices';
                }

                if ($type === TestResult::TODO && $countTests > 1) {
                    $type = 'todos';
                }

                $tests[] = "<fg=$color;options=bold>$countTests $type</>";
            }
        }

        $pending = ResultReflection::numberOfTests($result) - $result->numberOfTestsRun();
        if ($pending > 0) {
            $tests[] = "\e[2m$pending pending\e[22m";
        }

        $timeElapsed = number_format($telemetry->durationSinceStart()->asFloat(), 2, '.', '');

        $this->output->writeln(['']);

        if (! empty($tests)) {
            $extra = '';
            foreach (DefaultPrinter::recapCallbacks() as $callback) {
                $output = $callback($state, $telemetry, $result);
                if ($output !== '') {
                    $extra .= '<fg=gray>,</> '.$output;
                }
            }

            $this->output->writeln([
                sprintf(
                    '  <fg=gray>Tests:</>    <fg=default>%s</><fg=gray> (%s assertions%s)</>',
                    implode('<fg=gray>,</> ', $tests),
                    $result->numberOfAssertions(),
                    $extra,
                ),
            ]);
        }

        $this->output->writeln([
            sprintf(
                '  <fg=gray>Duration:</> <fg=default>%ss</>',
                $timeElapsed
            ),
        ]);

        $configuration = Registry::get();
        if ($configuration->executionOrder() === TestSuiteSorter::ORDER_RANDOMIZED) {
            $this->output->writeln([
                sprintf(
                    '  <fg=gray>Random Order Seed:</> <fg=default>%s</>',
                    $configuration->randomOrderSeed(),
                ),
            ]);
        }

        $this->output->writeln('');
    }

    /**
     * @param  array<int, TestResult>  $slowTests
     */
    public function writeSlowTests(array $slowTests, Info $telemetry): void
    {
        $this->output->writeln('  <fg=gray>Top 10 slowest tests:</>');

        $timeElapsed = $telemetry->durationSinceStart()->asFloat();

        foreach ($slowTests as $testResult) {
            $seconds = number_format($testResult->duration / 1000, 2, '.', '');

            $color = ($testResult->duration / 1000) > $timeElapsed * 0.25 ? 'red' : ($testResult->duration > $timeElapsed * 0.1 ? 'yellow' : 'gray');

            renderUsing($this->output);
            render(sprintf(<<<'HTML'
                <div class="flex justify-between space-x-1 mx-2">
                    <span class="flex-1">
                        <span class="font-bold">%s</span><span class="text-gray mx-1">></span><span class="text-gray">%s</span>
                    </span>
                    <span class="ml-1 font-bold text-%s">
                        %ss
                    </span>
                </div>
            HTML, $testResult->testCaseName, $testResult->description, $color, $seconds));
        }

        $timeElapsedInSlowTests = array_sum(array_map(fn (TestResult $testResult) => $testResult->duration / 1000, $slowTests));

        $timeElapsedAsString = number_format($timeElapsed, 2, '.', '');
        $percentageInSlowTestsAsString = number_format($timeElapsedInSlowTests * 100 / $timeElapsed, 2, '.', '');
        $timeElapsedInSlowTestsAsString = number_format($timeElapsedInSlowTests, 2, '.', '');

        renderUsing($this->output);
        render(sprintf(<<<'HTML'
            <div class="mx-2 mb-1 flex">
                <div class="text-gray">
                    <hr/>
                </div>
                <div class="flex space-x-1 justify-between">
                    <span>
                    </span>
                    <span>
                        <span class="text-gray">(%s%% of %ss)</span>
                        <span class="ml-1 font-bold">%ss</span>
                    </span>
                </div>
            </div>
        HTML, $percentageInSlowTestsAsString, $timeElapsedAsString, $timeElapsedInSlowTestsAsString));
    }

    /**
     * Displays the error using Collision's writer and terminates with exit code === 1.
     */
    public function writeError(Throwable $throwable): void
    {
        $writer = (new Writer)->setOutput($this->output);

        $throwable = new TestException($throwable, $this->output->isVerbose());

        $writer->showTitle(false);

        $writer->ignoreFilesIn([
            '/vendor\/nunomaduro\/collision/',
            '/vendor\/bin\/pest/',
            '/bin\/pest/',
            '/vendor\/brianium\/paratest/',
            '/vendor\/pestphp\/pest/',
            '/vendor\/pestphp\/pest-plugin-arch/',
            '/vendor\/pestphp\/pest-plugin-evals/',
            '/vendor\/pestphp\/pest-plugin-browser/',
            '/vendor\/phpspec\/prophecy-phpunit/',
            '/vendor\/phpspec\/prophecy/',
            '/vendor\/phpunit\/phpunit\/src/',
            '/vendor\/mockery\/mockery/',
            '/vendor\/laravel\/dusk/',
            '/Illuminate\/Testing/',
            '/Illuminate\/Foundation\/Testing/',
            '/Illuminate\/Foundation\/Bootstrap\/HandleExceptions/',
            '/vendor\/symfony\/framework-bundle\/Test/',
            '/vendor\/symfony\/phpunit-bridge/',
            '/vendor\/symfony\/dom-crawler/',
            '/vendor\/symfony\/browser-kit/',
            '/vendor\/symfony\/css-selector/',
            '/vendor\/bin\/.phpunit/',
            '/bin\/.phpunit/',
            '/vendor\/bin\/simple-phpunit/',
            '/bin\/phpunit/',
            '/vendor\/coduo\/php-matcher\/src\/PHPUnit/',
            '/vendor\/sulu\/sulu\/src\/Sulu\/Bundle\/TestBundle\/Testing/',
            '/vendor\/webmozart\/assert/',

            $this->ignorePestPipes(...),
            $this->ignorePestExtends(...),
            $this->ignorePestInterceptors(...),

        ]);

        /** @var \Throwable $throwable */
        $inspector = new Inspector($throwable);

        $writer->write($inspector);
    }

    /**
     * Returns the title contents.
     */
    private function titleLineFrom(string $fg, string $bg, string $title, string $testCaseName, int $todos): string
    {
        return sprintf(
            "\n  <fg=%s;bg=%s;options=bold> %s </><fg=default> %s</>%s",
            $fg,
            $bg,
            $title,
            $testCaseName,
            $todos > 0 ? sprintf('<fg=gray> - %s todo%s</>', $todos, $todos > 1 ? 's' : '') : '',
        );
    }

    /**
     * Writes a description line.
     */
    private function writeCompactDescriptionLine(TestResult $result): void
    {
        $symbolsOnCurrentLine = $this->compactProcessed % $this->compactSymbolsPerLine;

        if ($symbolsOnCurrentLine >= $this->terminal->width() - 4) {
            $symbolsOnCurrentLine = 0;
        }

        if ($symbolsOnCurrentLine === 0) {
            $this->output->writeln('');
            $this->output->write('  ');
        }

        $this->output->write(sprintf('<fg=%s;options=bold>%s</>', $result->compactColor, $result->compactIcon));

        $this->compactProcessed++;
    }

    /**
     * Writes a description line.
     */
    private function writeDescriptionLine(TestResult $result): void
    {
        if (! empty($warning = $result->warning)) {
            if (! str_contains($warning, "\n")) {
                $warning = sprintf(
                    ' → %s',
                    $warning
                );
            } else {
                $warningLines = explode("\n", $warning);
                $warning = '';

                foreach ($warningLines as $w) {
                    $warning .= sprintf(
                        "\n    <fg=yellow;options=bold>⇂ %s</>",
                        trim($w)
                    );
                }
            }
        }

        $seconds = '';

        if (($result->duration / 1000) > 0.0) {
            $seconds = number_format($result->duration / 1000, 2, '.', '');
            $seconds = $seconds !== '0.00' ? sprintf('<span class="text-gray mr-2">%ss</span>', $seconds) : '';
        }

        if (isset($_SERVER['REBUILD_SNAPSHOTS']) || (isset($_SERVER['COLLISION_IGNORE_DURATION']) && $_SERVER['COLLISION_IGNORE_DURATION'] === 'true')) {
            $seconds = '';
        }

        $truncateClasses = $this->output->isVerbose() ? '' : 'flex-1 truncate';

        if ($warning !== '') {
            $warning = sprintf('<span class="ml-1 text-yellow">%s</span>', $warning);

            if (! empty($result->warningSource)) {
                $warning .= ' // '.$result->warningSource;
            }
        }

        $description = $result->description;

        /** @var string $description */
        $description = preg_replace('/`([^`]+)`/', '<span class="text-white">$1</span>', $description);

        if (class_exists(Events::class)) {
            $description = Events::beforeTestMethodDescription($result, $description);
        }

        renderUsing($this->output);
        render(sprintf(<<<'HTML'
            <div class="%s ml-2">
                <span class="%s text-gray">
                    <span class="text-%s font-bold">%s</span><span class="ml-1 text-gray">%s</span>%s
                </span>%s
            </div>
        HTML, $seconds === '' ? '' : 'flex space-x-1 justify-between', $truncateClasses, $result->color, $result->icon, $description, $warning, $seconds));

        class_exists(Events::class) && Events::afterTestMethodDescription($result);
    }

    /**
     * @param  Frame  $frame
     */
    private function ignorePestPipes($frame): bool
    {
        if (class_exists(Expectation::class)) {
            $reflection = new ReflectionClass(Expectation::class);

            /** @var array<string, array<Closure(Closure, mixed ...$arguments): void>> $expectationPipes */
            $expectationPipes = $reflection->getStaticPropertyValue('pipes', []);

            foreach ($expectationPipes as $pipes) {
                foreach ($pipes as $pipeClosure) {
                    if ($this->isFrameInClosure($frame, $pipeClosure)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param  Frame  $frame
     */
    private function ignorePestExtends($frame): bool
    {
        if (class_exists(Expectation::class)) {
            $reflection = new ReflectionClass(Expectation::class);

            /** @var array<string, Closure> $extends */
            $extends = $reflection->getStaticPropertyValue('extends', []);

            foreach ($extends as $extendClosure) {
                if ($this->isFrameInClosure($frame, $extendClosure)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  Frame  $frame
     */
    private function ignorePestInterceptors($frame): bool
    {
        if (class_exists(Expectation::class)) {
            $reflection = new ReflectionClass(Expectation::class);

            /** @var array<string, array<Closure(Closure, mixed ...$arguments): void>> $expectationInterceptors */
            $expectationInterceptors = $reflection->getStaticPropertyValue('interceptors', []);

            foreach ($expectationInterceptors as $pipes) {
                foreach ($pipes as $pipeClosure) {
                    if ($this->isFrameInClosure($frame, $pipeClosure)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param  Frame  $frame
     */
    private function isFrameInClosure($frame, Closure $closure): bool
    {
        $reflection = new ReflectionFunction($closure);

        $sanitizedPath = (string) str_replace('\\', '/', (string) $frame->getFile());

        /** @phpstan-ignore-next-line */
        $sanitizedClosurePath = (string) str_replace('\\', '/', $reflection->getFileName());

        if ($sanitizedPath === $sanitizedClosurePath) {
            if ($reflection->getStartLine() <= $frame->getLine() && $frame->getLine() <= $reflection->getEndLine()) {
                return true;
            }
        }

        return false;
    }
}
