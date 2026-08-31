<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Output;

use const PHP_EOL;
use function sprintf;
use PHPUnit\TestRunner\TestResult\TestResult;
use PHPUnit\Util\Color;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class SummaryPrinter
{
    private readonly Printer $printer;
    private readonly bool $colors;
    private bool $countPrinted = false;

    public function __construct(Printer $printer, bool $colors)
    {
        $this->printer = $printer;
        $this->colors  = $colors;
    }

    public function print(TestResult $result): void
    {
        if ($result->numberOfTestsRun() === 0) {
            $this->printWithColor(
                'fg-black, bg-yellow',
                'No tests executed!',
            );

            return;
        }

        if ($result->wasSuccessful() &&
            !$result->hasIssues() &&
            !$result->hasTestSuiteSkippedEvents() &&
            !$result->hasTestSkippedEvents()) {
            $this->printWithColor(
                'fg-black, bg-green',
                sprintf(
                    'OK (%d test%s, %d assertion%s)',
                    $result->numberOfTestsRun(),
                    $result->numberOfTestsRun() === 1 ? '' : 's',
                    $result->numberOfAssertions(),
                    $result->numberOfAssertions() === 1 ? '' : 's',
                ),
            );

            $this->printNumberOfIssuesIgnoredByBaseline($result);

            return;
        }

        if ($result->wasSuccessful()) {
            if ($result->hasIssues()) {
                $color = 'fg-black, bg-yellow';

                $this->printWithColor(
                    $color,
                    'OK, but there were issues!',
                );
            } else {
                $color = 'fg-black, bg-green';

                $this->printWithColor(
                    $color,
                    'OK, but some tests were skipped!',
                );
            }
        } else {
            $color = 'fg-white, bg-red';

            if ($result->hasTestErroredEvents() || $result->hasTestTriggeredPhpunitErrorEvents()) {
                $this->printWithColor(
                    'fg-white, bg-red',
                    'ERRORS!',
                );
            } else {
                $this->printWithColor(
                    'fg-white, bg-red',
                    'FAILURES!',
                );
            }
        }

        $this->printCountString($result->numberOfTestsRun(), 'Tests', $color, true);
        $this->printCountString($result->numberOfAssertions(), 'Assertions', $color, true);
        $this->printCountString($result->numberOfErrors(), 'Errors', $color);
        $this->printCountString($result->numberOfTestFailedEvents(), 'Failures', $color);
        $this->printCountString($result->numberOfPhpunitWarnings(), 'PHPUnit Warnings', $color);
        $this->printCountString($result->numberOfWarnings(), 'Warnings', $color);
        $this->printCountString($result->numberOfPhpOrUserDeprecations(), 'Deprecations', $color);
        $this->printCountString($result->numberOfPhpunitDeprecations(), 'PHPUnit Deprecations', $color);
        $this->printCountString($result->numberOfNotices(), 'Notices', $color);
        $this->printCountString($result->numberOfTestSkippedByTestSuiteSkippedEvents() + $result->numberOfTestSkippedEvents(), 'Skipped', $color);
        $this->printCountString($result->numberOfTestMarkedIncompleteEvents(), 'Incomplete', $color);
        $this->printCountString($result->numberOfTestsWithTestConsideredRiskyEvents(), 'Risky', $color);
        $this->printWithColor($color, '.');

        $this->printNumberOfIssuesIgnoredByBaseline($result);
    }

    private function printCountString(int $count, string $name, string $color, bool $always = false): void
    {
        if ($always || $count > 0) {
            $this->printWithColor(
                $color,
                sprintf(
                    '%s%s: %d',
                    $this->countPrinted ? ', ' : '',
                    $name,
                    $count,
                ),
                false,
            );

            $this->countPrinted = true;
        }
    }

    private function printWithColor(string $color, string $buffer, bool $lf = true): void
    {
        if ($this->colors) {
            $buffer = Color::colorizeTextBox($color, $buffer);
        }

        $this->printer->print($buffer);

        if ($lf) {
            $this->printer->print(PHP_EOL);
        }
    }

    private function printNumberOfIssuesIgnoredByBaseline(TestResult $result): void
    {
        if ($result->hasIssuesIgnoredByBaseline()) {
            $this->printer->print(
                sprintf(
                    '%s%d issue%s %s ignored by baseline.%s',
                    PHP_EOL,
                    $result->numberOfIssuesIgnoredByBaseline(),
                    $result->numberOfIssuesIgnoredByBaseline() > 1 ? 's' : '',
                    $result->numberOfIssuesIgnoredByBaseline() > 1 ? 'were' : 'was',
                    PHP_EOL,
                ),
            );
        }
    }
}
