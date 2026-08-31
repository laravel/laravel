<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Output\TestDox;

use const PHP_EOL;
use function array_map;
use function explode;
use function implode;
use function preg_match;
use function preg_split;
use function rtrim;
use function sprintf;
use function str_starts_with;
use function trim;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Test\AfterLastTestMethodErrored;
use PHPUnit\Event\Test\BeforeFirstTestMethodErrored;
use PHPUnit\Framework\TestStatus\TestStatus;
use PHPUnit\Logging\TestDox\TestResult as TestDoxTestResult;
use PHPUnit\Logging\TestDox\TestResultCollection;
use PHPUnit\TestRunner\TestResult\TestResult;
use PHPUnit\TextUI\Output\Printer;
use PHPUnit\Util\Color;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ResultPrinter
{
    private Printer $printer;
    private bool $colors;
    private int $columns;
    private bool $printSummary;

    public function __construct(Printer $printer, bool $colors, int $columns, bool $printSummary)
    {
        $this->printer      = $printer;
        $this->colors       = $colors;
        $this->columns      = $columns;
        $this->printSummary = $printSummary;
    }

    /**
     * @param array<string, TestResultCollection> $tests
     */
    public function print(TestResult $result, array $tests): void
    {
        $this->doPrint($tests, false);

        if ($this->printSummary) {
            $this->printer->print('Summary of tests with errors, failures, or issues:' . PHP_EOL . PHP_EOL);

            $this->doPrint($tests, true);
        }

        $beforeFirstTestMethodErrored = [];
        $afterLastTestMethodErrored   = [];

        foreach ($result->testErroredEvents() as $error) {
            if ($error instanceof BeforeFirstTestMethodErrored) {
                $beforeFirstTestMethodErrored[$error->calledMethod()->className() . '::' . $error->calledMethod()->methodName()] = $error;
            }

            if ($error instanceof AfterLastTestMethodErrored) {
                $afterLastTestMethodErrored[$error->calledMethod()->className() . '::' . $error->calledMethod()->methodName()] = $error;
            }
        }

        $this->printBeforeClassOrAfterClassErrors(
            'before-first-test',
            $beforeFirstTestMethodErrored,
        );

        $this->printBeforeClassOrAfterClassErrors(
            'after-last-test',
            $afterLastTestMethodErrored,
        );
    }

    /**
     * @param array<string, TestResultCollection> $tests
     */
    private function doPrint(array $tests, bool $onlySummary): void
    {
        foreach ($tests as $prettifiedClassName => $_tests) {
            $print = true;

            if ($onlySummary) {
                $found = false;

                foreach ($_tests as $test) {
                    if ($test->status()->isSuccess()) {
                        continue;
                    }

                    $found = true;

                    break;
                }

                if (!$found) {
                    $print = false;
                }
            }

            if (!$print) {
                continue;
            }

            $this->printPrettifiedClassName($prettifiedClassName);

            foreach ($_tests as $test) {
                if ($onlySummary && $test->status()->isSuccess()) {
                    continue;
                }

                $this->printTestResult($test);
            }

            $this->printer->print(PHP_EOL);
        }
    }

    private function printPrettifiedClassName(string $prettifiedClassName): void
    {
        $buffer = $prettifiedClassName;

        if ($this->colors) {
            $buffer = Color::colorizeTextBox('underlined', $buffer);
        }

        $this->printer->print($buffer . PHP_EOL);
    }

    private function printTestResult(TestDoxTestResult $test): void
    {
        $this->printTestResultHeader($test);
        $this->printTestResultBody($test);
    }

    private function printTestResultHeader(TestDoxTestResult $test): void
    {
        $buffer = ' ' . $this->symbolFor($test->status()) . ' ';

        if ($this->colors) {
            $this->printer->print(
                Color::colorizeTextBox(
                    $this->colorFor($test->status()),
                    $buffer,
                ),
            );
        } else {
            $this->printer->print($buffer);
        }

        $this->printer->print($test->test()->testDox()->prettifiedMethodName($this->colors) . PHP_EOL);
    }

    private function printTestResultBody(TestDoxTestResult $test): void
    {
        if ($test->status()->isSuccess()) {
            return;
        }

        if (!$test->hasThrowable()) {
            return;
        }

        $this->printTestResultBodyStart($test);
        $this->printThrowable($test->status(), $test->throwable());
        $this->printTestResultBodyEnd($test);
    }

    private function printTestResultBodyStart(TestDoxTestResult $test): void
    {
        $this->printer->print(
            $this->prefixLines(
                $this->prefixFor('start', $test->status()),
                '',
            ),
        );

        $this->printer->print(PHP_EOL);
    }

    private function printTestResultBodyEnd(TestDoxTestResult $test): void
    {
        $this->printer->print(PHP_EOL);

        $this->printer->print(
            $this->prefixLines(
                $this->prefixFor('last', $test->status()),
                '',
            ),
        );

        $this->printer->print(PHP_EOL);
    }

    private function printThrowable(TestStatus $status, Throwable $throwable): void
    {
        $message    = trim($throwable->description());
        $stackTrace = $this->formatStackTrace($throwable->stackTrace());
        $diff       = '';

        if (!empty($message) && $this->colors) {
            ['message' => $message, 'diff' => $diff] = $this->colorizeMessageAndDiff(
                $message,
                $this->messageColorFor($status),
            );
        }

        if (!empty($message)) {
            $this->printer->print(
                $this->prefixLines(
                    $this->prefixFor('message', $status),
                    $message,
                ),
            );

            $this->printer->print(PHP_EOL);
        }

        if (!empty($diff)) {
            $this->printer->print(
                $this->prefixLines(
                    $this->prefixFor('diff', $status),
                    $diff,
                ),
            );

            $this->printer->print(PHP_EOL);
        }

        if (!empty($stackTrace)) {
            if (!empty($message) || !empty($diff)) {
                $tracePrefix = $this->prefixFor('default', $status);
            } else {
                $tracePrefix = $this->prefixFor('trace', $status);
            }

            $this->printer->print(
                $this->prefixLines($tracePrefix, PHP_EOL . $stackTrace),
            );
        }

        if ($throwable->hasPrevious()) {
            $this->printer->print(PHP_EOL);

            $this->printer->print(
                $this->prefixLines(
                    $this->prefixFor('default', $status),
                    ' ',
                ),
            );

            $this->printer->print(PHP_EOL);

            $this->printer->print(
                $this->prefixLines(
                    $this->prefixFor('default', $status),
                    'Caused by:',
                ),
            );

            $this->printer->print(PHP_EOL);

            $this->printThrowable($status, $throwable->previous());
        }
    }

    /**
     * @return array{message: string, diff: string}
     */
    private function colorizeMessageAndDiff(string $buffer, string $style): array
    {
        $lines      = $buffer ? array_map('\rtrim', explode(PHP_EOL, $buffer)) : [];
        $message    = [];
        $diff       = [];
        $insideDiff = false;

        foreach ($lines as $line) {
            if ($line === '--- Expected') {
                $insideDiff = true;
            }

            if (!$insideDiff) {
                $message[] = $line;
            } else {
                if (str_starts_with($line, '-')) {
                    $line = Color::colorize('fg-red', Color::visualizeWhitespace($line, true));
                } elseif (str_starts_with($line, '+')) {
                    $line = Color::colorize('fg-green', Color::visualizeWhitespace($line, true));
                } elseif ($line === '@@ @@') {
                    $line = Color::colorize('fg-cyan', $line);
                }

                $diff[] = $line;
            }
        }

        $message = implode(PHP_EOL, $message);
        $diff    = implode(PHP_EOL, $diff);

        if (!empty($message)) {
            // Testdox output has a left-margin of 5; keep right-margin to prevent terminal scrolling
            $message = Color::colorizeTextBox($style, $message, $this->columns - 7);
        }

        return [
            'message' => $message,
            'diff'    => $diff,
        ];
    }

    private function formatStackTrace(string $stackTrace): string
    {
        if (!$this->colors) {
            return rtrim($stackTrace);
        }

        $lines        = [];
        $previousPath = '';

        foreach (explode(PHP_EOL, $stackTrace) as $line) {
            if (preg_match('/^(.*):(\d+)$/', $line, $matches)) {
                $lines[]      = Color::colorizePath($matches[1], $previousPath) . Color::dim(':') . Color::colorize('fg-blue', $matches[2]) . "\n";
                $previousPath = $matches[1];

                continue;
            }

            $lines[]      = $line;
            $previousPath = '';
        }

        return rtrim(implode('', $lines));
    }

    private function prefixLines(string $prefix, string $message): string
    {
        return implode(
            PHP_EOL,
            array_map(
                static fn (string $line) => '   ' . $prefix . ($line ? ' ' . $line : ''),
                preg_split('/\r\n|\r|\n/', $message),
            ),
        );
    }

    /**
     * @param 'default'|'diff'|'last'|'message'|'start'|'trace' $type
     */
    private function prefixFor(string $type, TestStatus $status): string
    {
        if (!$this->colors) {
            return '│';
        }

        return Color::colorize(
            $this->colorFor($status),
            match ($type) {
                'default' => '│',
                'start'   => '┐',
                'message' => '├',
                'diff'    => '┊',
                'trace'   => '╵',
                'last'    => '┴',
            },
        );
    }

    private function colorFor(TestStatus $status): string
    {
        if ($status->isSuccess()) {
            return 'fg-green';
        }

        if ($status->isError()) {
            return 'fg-yellow';
        }

        if ($status->isFailure()) {
            return 'fg-red';
        }

        if ($status->isSkipped()) {
            return 'fg-cyan';
        }

        if ($status->isIncomplete() || $status->isDeprecation() || $status->isNotice() || $status->isRisky() || $status->isWarning()) {
            return 'fg-yellow';
        }

        return 'fg-blue';
    }

    private function messageColorFor(TestStatus $status): string
    {
        if ($status->isSuccess()) {
            return '';
        }

        if ($status->isError()) {
            return 'bg-yellow,fg-black';
        }

        if ($status->isFailure()) {
            return 'bg-red,fg-white';
        }

        if ($status->isSkipped()) {
            return 'fg-cyan';
        }

        if ($status->isIncomplete() || $status->isDeprecation() || $status->isNotice() || $status->isRisky() || $status->isWarning()) {
            return 'fg-yellow';
        }

        return 'fg-white,bg-blue';
    }

    private function symbolFor(TestStatus $status): string
    {
        if ($status->isSuccess()) {
            return '✔';
        }

        if ($status->isError() || $status->isFailure()) {
            return '✘';
        }

        if ($status->isSkipped()) {
            return '↩';
        }

        if ($status->isDeprecation() || $status->isNotice() || $status->isRisky() || $status->isWarning()) {
            return '⚠';
        }

        if ($status->isIncomplete()) {
            return '∅';
        }

        return '?';
    }

    /**
     * @param 'after-last-test'|'before-first-test'                                            $type
     * @param array<non-empty-string, AfterLastTestMethodErrored|BeforeFirstTestMethodErrored> $errors
     */
    private function printBeforeClassOrAfterClassErrors(string $type, array $errors): void
    {
        if (empty($errors)) {
            return;
        }

        $this->printer->print(
            sprintf(
                'These %s methods errored:' . PHP_EOL . PHP_EOL,
                $type,
            ),
        );

        $index = 0;

        foreach ($errors as $method => $error) {
            $this->printer->print(
                sprintf(
                    '%d) %s' . PHP_EOL,
                    ++$index,
                    $method,
                ),
            );

            $this->printer->print(trim($error->throwable()->description()) . PHP_EOL . PHP_EOL);
            $this->printer->print($this->formatStackTrace($error->throwable()->stackTrace()) . PHP_EOL);
        }

        $this->printer->print(PHP_EOL);
    }
}
