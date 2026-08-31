<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Data;

use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_unique;
use function count;
use function is_array;
use function ksort;
use SebastianBergmann\CodeCoverage\Driver\Driver;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 *
 * @phpstan-import-type XdebugFunctionCoverageType from \SebastianBergmann\CodeCoverage\Driver\XdebugDriver
 *
 * @phpstan-type TestIdType = string
 */
final class ProcessedCodeCoverageData
{
    /**
     * Line coverage data.
     * An array of filenames, each having an array of linenumbers, each executable line having an array of testcase ids.
     *
     * @var array<string, array<int, null|list<TestIdType>>>
     */
    private array $lineCoverage = [];

    /**
     * Function coverage data.
     * Maintains base format of raw data (@see https://xdebug.org/docs/code_coverage), but each 'hit' entry is an array
     * of testcase ids.
     *
     * @var array<string, array<string, array{
     *     branches: array<int, array{
     *         op_start: int,
     *         op_end: int,
     *         line_start: int,
     *         line_end: int,
     *         hit: list<TestIdType>,
     *         out: array<int, int>,
     *         out_hit: array<int, int>,
     *     }>,
     *     paths: array<int, array{
     *         path: array<int, int>,
     *         hit: list<TestIdType>,
     *     }>,
     *     hit: list<TestIdType>
     * }>>
     */
    private array $functionCoverage = [];

    public function initializeUnseenData(RawCodeCoverageData $rawData): void
    {
        foreach ($rawData->lineCoverage() as $file => $lines) {
            if (!isset($this->lineCoverage[$file])) {
                $this->lineCoverage[$file] = [];

                foreach ($lines as $k => $v) {
                    $this->lineCoverage[$file][$k] = $v === Driver::LINE_NOT_EXECUTABLE ? null : [];
                }
            }
        }

        foreach ($rawData->functionCoverage() as $file => $functions) {
            foreach ($functions as $functionName => $functionData) {
                if (isset($this->functionCoverage[$file][$functionName])) {
                    $this->initPreviouslySeenFunction($file, $functionName, $functionData);
                } else {
                    $this->initPreviouslyUnseenFunction($file, $functionName, $functionData);
                }
            }
        }
    }

    public function markCodeAsExecutedByTestCase(string $testCaseId, RawCodeCoverageData $executedCode): void
    {
        foreach ($executedCode->lineCoverage() as $file => $lines) {
            foreach ($lines as $k => $v) {
                if ($v === Driver::LINE_EXECUTED) {
                    $this->lineCoverage[$file][$k][] = $testCaseId;
                }
            }
        }

        foreach ($executedCode->functionCoverage() as $file => $functions) {
            foreach ($functions as $functionName => $functionData) {
                foreach ($functionData['branches'] as $branchId => $branchData) {
                    if ($branchData['hit'] === Driver::BRANCH_HIT) {
                        $this->functionCoverage[$file][$functionName]['branches'][$branchId]['hit'][] = $testCaseId;
                    }
                }

                foreach ($functionData['paths'] as $pathId => $pathData) {
                    if ($pathData['hit'] === Driver::BRANCH_HIT) {
                        $this->functionCoverage[$file][$functionName]['paths'][$pathId]['hit'][] = $testCaseId;
                    }
                }
            }
        }
    }

    public function setLineCoverage(array $lineCoverage): void
    {
        $this->lineCoverage = $lineCoverage;
    }

    public function lineCoverage(): array
    {
        ksort($this->lineCoverage);

        return $this->lineCoverage;
    }

    public function setFunctionCoverage(array $functionCoverage): void
    {
        $this->functionCoverage = $functionCoverage;
    }

    public function functionCoverage(): array
    {
        ksort($this->functionCoverage);

        return $this->functionCoverage;
    }

    public function coveredFiles(): array
    {
        ksort($this->lineCoverage);

        return array_keys($this->lineCoverage);
    }

    public function renameFile(string $oldFile, string $newFile): void
    {
        $this->lineCoverage[$newFile] = $this->lineCoverage[$oldFile];

        if (isset($this->functionCoverage[$oldFile])) {
            $this->functionCoverage[$newFile] = $this->functionCoverage[$oldFile];
        }

        unset($this->lineCoverage[$oldFile], $this->functionCoverage[$oldFile]);
    }

    public function merge(self $newData): void
    {
        foreach ($newData->lineCoverage as $file => $lines) {
            if (!isset($this->lineCoverage[$file])) {
                $this->lineCoverage[$file] = $lines;

                continue;
            }

            // we should compare the lines if any of two contains data
            $compareLineNumbers = array_unique(
                array_merge(
                    array_keys($this->lineCoverage[$file]),
                    array_keys($newData->lineCoverage[$file]),
                ),
            );

            foreach ($compareLineNumbers as $line) {
                $thatPriority = $this->priorityForLine($newData->lineCoverage[$file], $line);
                $thisPriority = $this->priorityForLine($this->lineCoverage[$file], $line);

                if ($thatPriority > $thisPriority) {
                    $this->lineCoverage[$file][$line] = $newData->lineCoverage[$file][$line];
                } elseif ($thatPriority === $thisPriority && is_array($this->lineCoverage[$file][$line])) {
                    $this->lineCoverage[$file][$line] = array_unique(
                        array_merge($this->lineCoverage[$file][$line], $newData->lineCoverage[$file][$line]),
                    );
                }
            }
        }

        foreach ($newData->functionCoverage as $file => $functions) {
            if (!isset($this->functionCoverage[$file])) {
                $this->functionCoverage[$file] = $functions;

                continue;
            }

            foreach ($functions as $functionName => $functionData) {
                if (isset($this->functionCoverage[$file][$functionName])) {
                    $this->initPreviouslySeenFunction($file, $functionName, $functionData);
                } else {
                    $this->initPreviouslyUnseenFunction($file, $functionName, $functionData);
                }

                foreach ($functionData['branches'] as $branchId => $branchData) {
                    $this->functionCoverage[$file][$functionName]['branches'][$branchId]['hit'] = array_unique(array_merge($this->functionCoverage[$file][$functionName]['branches'][$branchId]['hit'], $branchData['hit']));
                }

                foreach ($functionData['paths'] as $pathId => $pathData) {
                    $this->functionCoverage[$file][$functionName]['paths'][$pathId]['hit'] = array_unique(array_merge($this->functionCoverage[$file][$functionName]['paths'][$pathId]['hit'], $pathData['hit']));
                }
            }
        }
    }

    /**
     * Determine the priority for a line.
     *
     * 1 = the line is not set
     * 2 = the line has not been tested
     * 3 = the line is dead code
     * 4 = the line has been tested
     *
     * During a merge, a higher number is better.
     */
    private function priorityForLine(array $data, int $line): int
    {
        if (!array_key_exists($line, $data)) {
            return 1;
        }

        if (is_array($data[$line]) && count($data[$line]) === 0) {
            return 2;
        }

        if ($data[$line] === null) {
            return 3;
        }

        return 4;
    }

    /**
     * For a function we have never seen before, copy all data over and simply init the 'hit' array.
     *
     * @param XdebugFunctionCoverageType $functionData
     */
    private function initPreviouslyUnseenFunction(string $file, string $functionName, array $functionData): void
    {
        $this->functionCoverage[$file][$functionName] = $functionData;

        foreach (array_keys($functionData['branches']) as $branchId) {
            $this->functionCoverage[$file][$functionName]['branches'][$branchId]['hit'] = [];
        }

        foreach (array_keys($functionData['paths']) as $pathId) {
            $this->functionCoverage[$file][$functionName]['paths'][$pathId]['hit'] = [];
        }
    }

    /**
     * For a function we have seen before, only copy over and init the 'hit' array for any unseen branches and paths.
     * Techniques such as mocking and where the contents of a file are different vary during tests (e.g. compiling
     * containers) mean that the functions inside a file cannot be relied upon to be static.
     *
     * @param XdebugFunctionCoverageType $functionData
     */
    private function initPreviouslySeenFunction(string $file, string $functionName, array $functionData): void
    {
        foreach ($functionData['branches'] as $branchId => $branchData) {
            if (!isset($this->functionCoverage[$file][$functionName]['branches'][$branchId])) {
                $this->functionCoverage[$file][$functionName]['branches'][$branchId]        = $branchData;
                $this->functionCoverage[$file][$functionName]['branches'][$branchId]['hit'] = [];
            }
        }

        foreach ($functionData['paths'] as $pathId => $pathData) {
            if (!isset($this->functionCoverage[$file][$functionName]['paths'][$pathId])) {
                $this->functionCoverage[$file][$functionName]['paths'][$pathId]        = $pathData;
                $this->functionCoverage[$file][$functionName]['paths'][$pathId]['hit'] = [];
            }
        }
    }
}
