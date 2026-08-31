<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report;

use const PHP_EOL;
use function array_map;
use function date;
use function ksort;
use function max;
use function sprintf;
use function str_pad;
use function strlen;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Node\File;
use SebastianBergmann\CodeCoverage\Util\Percentage;

final class Text
{
    /**
     * @var string
     */
    private const COLOR_GREEN = "\x1b[30;42m";

    /**
     * @var string
     */
    private const COLOR_YELLOW = "\x1b[30;43m";

    /**
     * @var string
     */
    private const COLOR_RED = "\x1b[37;41m";

    /**
     * @var string
     */
    private const COLOR_HEADER = "\x1b[1;37;40m";

    /**
     * @var string
     */
    private const COLOR_RESET = "\x1b[0m";
    private readonly Thresholds $thresholds;
    private readonly bool $showUncoveredFiles;
    private readonly bool $showOnlySummary;

    public function __construct(Thresholds $thresholds, bool $showUncoveredFiles = false, bool $showOnlySummary = false)
    {
        $this->thresholds         = $thresholds;
        $this->showUncoveredFiles = $showUncoveredFiles;
        $this->showOnlySummary    = $showOnlySummary;
    }

    public function process(CodeCoverage $coverage, bool $showColors = false): string
    {
        $hasBranchCoverage = !empty($coverage->getData(true)->functionCoverage());

        $output = PHP_EOL . PHP_EOL;
        $report = $coverage->getReport();

        $colors = [
            'header'   => '',
            'classes'  => '',
            'methods'  => '',
            'lines'    => '',
            'branches' => '',
            'paths'    => '',
            'reset'    => '',
        ];

        if ($showColors) {
            $colors['classes'] = $this->coverageColor(
                $report->numberOfTestedClassesAndTraits(),
                $report->numberOfClassesAndTraits(),
            );

            $colors['methods'] = $this->coverageColor(
                $report->numberOfTestedMethods(),
                $report->numberOfMethods(),
            );

            $colors['lines'] = $this->coverageColor(
                $report->numberOfExecutedLines(),
                $report->numberOfExecutableLines(),
            );

            $colors['branches'] = $this->coverageColor(
                $report->numberOfExecutedBranches(),
                $report->numberOfExecutableBranches(),
            );

            $colors['paths'] = $this->coverageColor(
                $report->numberOfExecutedPaths(),
                $report->numberOfExecutablePaths(),
            );

            $colors['reset']  = self::COLOR_RESET;
            $colors['header'] = self::COLOR_HEADER;
        }

        $classes = sprintf(
            '  Classes: %6s (%d/%d)',
            Percentage::fromFractionAndTotal(
                $report->numberOfTestedClassesAndTraits(),
                $report->numberOfClassesAndTraits(),
            )->asString(),
            $report->numberOfTestedClassesAndTraits(),
            $report->numberOfClassesAndTraits(),
        );

        $methods = sprintf(
            '  Methods: %6s (%d/%d)',
            Percentage::fromFractionAndTotal(
                $report->numberOfTestedMethods(),
                $report->numberOfMethods(),
            )->asString(),
            $report->numberOfTestedMethods(),
            $report->numberOfMethods(),
        );

        $paths    = '';
        $branches = '';

        if ($hasBranchCoverage) {
            $paths = sprintf(
                '  Paths:   %6s (%d/%d)',
                Percentage::fromFractionAndTotal(
                    $report->numberOfExecutedPaths(),
                    $report->numberOfExecutablePaths(),
                )->asString(),
                $report->numberOfExecutedPaths(),
                $report->numberOfExecutablePaths(),
            );

            $branches = sprintf(
                '  Branches:   %6s (%d/%d)',
                Percentage::fromFractionAndTotal(
                    $report->numberOfExecutedBranches(),
                    $report->numberOfExecutableBranches(),
                )->asString(),
                $report->numberOfExecutedBranches(),
                $report->numberOfExecutableBranches(),
            );
        }

        $lines = sprintf(
            '  Lines:   %6s (%d/%d)',
            Percentage::fromFractionAndTotal(
                $report->numberOfExecutedLines(),
                $report->numberOfExecutableLines(),
            )->asString(),
            $report->numberOfExecutedLines(),
            $report->numberOfExecutableLines(),
        );

        $padding = max(array_map('strlen', [$classes, $methods, $lines]));

        if ($this->showOnlySummary) {
            $title   = 'Code Coverage Report Summary:';
            $padding = max($padding, strlen($title));

            $output .= $this->format($colors['header'], $padding, $title);
        } else {
            $date  = date('  Y-m-d H:i:s');
            $title = 'Code Coverage Report:';

            $output .= $this->format($colors['header'], $padding, $title);
            $output .= $this->format($colors['header'], $padding, $date);
            $output .= $this->format($colors['header'], $padding, '');
            $output .= $this->format($colors['header'], $padding, ' Summary:');
        }

        $output .= $this->format($colors['classes'], $padding, $classes);
        $output .= $this->format($colors['methods'], $padding, $methods);

        if ($hasBranchCoverage) {
            $output .= $this->format($colors['paths'], $padding, $paths);
            $output .= $this->format($colors['branches'], $padding, $branches);
        }
        $output .= $this->format($colors['lines'], $padding, $lines);

        if ($this->showOnlySummary) {
            return $output . PHP_EOL;
        }

        $classCoverage = [];

        foreach ($report as $item) {
            if (!$item instanceof File) {
                continue;
            }

            $classes = $item->classesAndTraits();

            foreach ($classes as $className => $class) {
                $classExecutableLines    = 0;
                $classExecutedLines      = 0;
                $classExecutableBranches = 0;
                $classExecutedBranches   = 0;
                $classExecutablePaths    = 0;
                $classExecutedPaths      = 0;
                $coveredMethods          = 0;
                $classMethods            = 0;

                foreach ($class['methods'] as $method) {
                    if ($method['executableLines'] == 0) {
                        continue;
                    }

                    $classMethods++;
                    $classExecutableLines    += $method['executableLines'];
                    $classExecutedLines      += $method['executedLines'];
                    $classExecutableBranches += $method['executableBranches'];
                    $classExecutedBranches   += $method['executedBranches'];
                    $classExecutablePaths    += $method['executablePaths'];
                    $classExecutedPaths      += $method['executedPaths'];

                    if ($method['coverage'] == 100) {
                        $coveredMethods++;
                    }
                }

                $classCoverage[$className] = [
                    'namespace'         => $class['namespace'],
                    'className'         => $className,
                    'methodsCovered'    => $coveredMethods,
                    'methodCount'       => $classMethods,
                    'statementsCovered' => $classExecutedLines,
                    'statementCount'    => $classExecutableLines,
                    'branchesCovered'   => $classExecutedBranches,
                    'branchesCount'     => $classExecutableBranches,
                    'pathsCovered'      => $classExecutedPaths,
                    'pathsCount'        => $classExecutablePaths,
                ];
            }
        }

        ksort($classCoverage);

        $methodColor   = '';
        $pathsColor    = '';
        $branchesColor = '';
        $linesColor    = '';
        $resetColor    = '';

        foreach ($classCoverage as $fullQualifiedPath => $classInfo) {
            if ($this->showUncoveredFiles || $classInfo['statementsCovered'] != 0) {
                if ($showColors) {
                    $methodColor   = $this->coverageColor($classInfo['methodsCovered'], $classInfo['methodCount']);
                    $pathsColor    = $this->coverageColor($classInfo['pathsCovered'], $classInfo['pathsCount']);
                    $branchesColor = $this->coverageColor($classInfo['branchesCovered'], $classInfo['branchesCount']);
                    $linesColor    = $this->coverageColor($classInfo['statementsCovered'], $classInfo['statementCount']);
                    $resetColor    = $colors['reset'];
                }

                $output .= PHP_EOL . $fullQualifiedPath . PHP_EOL
                    . '  ' . $methodColor . 'Methods: ' . $this->printCoverageCounts($classInfo['methodsCovered'], $classInfo['methodCount'], 2) . $resetColor . ' ';

                if ($hasBranchCoverage) {
                    $output .= '  ' . $pathsColor . 'Paths: ' . $this->printCoverageCounts($classInfo['pathsCovered'], $classInfo['pathsCount'], 3) . $resetColor . ' '
                    . '  ' . $branchesColor . 'Branches: ' . $this->printCoverageCounts($classInfo['branchesCovered'], $classInfo['branchesCount'], 3) . $resetColor . ' ';
                }
                $output .= '  ' . $linesColor . 'Lines: ' . $this->printCoverageCounts($classInfo['statementsCovered'], $classInfo['statementCount'], 3) . $resetColor;
            }
        }

        return $output . PHP_EOL;
    }

    private function coverageColor(int $numberOfCoveredElements, int $totalNumberOfElements): string
    {
        $coverage = Percentage::fromFractionAndTotal(
            $numberOfCoveredElements,
            $totalNumberOfElements,
        );

        if ($coverage->asFloat() >= $this->thresholds->highLowerBound()) {
            return self::COLOR_GREEN;
        }

        if ($coverage->asFloat() > $this->thresholds->lowUpperBound()) {
            return self::COLOR_YELLOW;
        }

        return self::COLOR_RED;
    }

    private function printCoverageCounts(int $numberOfCoveredElements, int $totalNumberOfElements, int $precision): string
    {
        $format = '%' . $precision . 's';

        return Percentage::fromFractionAndTotal(
            $numberOfCoveredElements,
            $totalNumberOfElements,
        )->asFixedWidthString() .
            ' (' . sprintf($format, $numberOfCoveredElements) . '/' .
        sprintf($format, $totalNumberOfElements) . ')';
    }

    private function format(string $color, int $padding, false|string $string): string
    {
        if ($color === '') {
            return (string) $string . PHP_EOL;
        }

        return $color . str_pad((string) $string, $padding) . self::COLOR_RESET . PHP_EOL;
    }
}
