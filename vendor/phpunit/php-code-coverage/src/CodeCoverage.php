<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage;

use function array_diff;
use function array_diff_key;
use function array_flip;
use function array_keys;
use function array_merge;
use function array_merge_recursive;
use function array_unique;
use function count;
use function explode;
use function is_array;
use function is_file;
use function sort;
use ReflectionClass;
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;
use SebastianBergmann\CodeCoverage\Data\RawCodeCoverageData;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Node\Builder;
use SebastianBergmann\CodeCoverage\Node\Directory;
use SebastianBergmann\CodeCoverage\StaticAnalysis\CachingFileAnalyser;
use SebastianBergmann\CodeCoverage\StaticAnalysis\FileAnalyser;
use SebastianBergmann\CodeCoverage\StaticAnalysis\ParsingFileAnalyser;
use SebastianBergmann\CodeCoverage\Test\TestSize\TestSize;
use SebastianBergmann\CodeCoverage\Test\TestStatus\TestStatus;
use SebastianBergmann\CodeUnitReverseLookup\Wizard;

/**
 * Provides collection functionality for PHP code coverage information.
 *
 * @phpstan-type TestType = array{
 *     size: string,
 *     status: string,
 * }
 */
final class CodeCoverage
{
    private const UNCOVERED_FILES = 'UNCOVERED_FILES';
    private readonly Driver $driver;
    private readonly Filter $filter;
    private readonly Wizard $wizard;
    private bool $checkForUnintentionallyCoveredCode = false;
    private bool $includeUncoveredFiles              = true;
    private bool $ignoreDeprecatedCode               = false;
    private ?string $currentId                       = null;
    private ?TestSize $currentSize                   = null;
    private ProcessedCodeCoverageData $data;
    private bool $useAnnotationsForIgnoringCode = true;

    /**
     * @var array<string,list<int>>
     */
    private array $linesToBeIgnored = [];

    /**
     * @var array<string, TestType>
     */
    private array $tests = [];

    /**
     * @var list<class-string>
     */
    private array $parentClassesExcludedFromUnintentionallyCoveredCodeCheck = [];
    private ?FileAnalyser $analyser                                         = null;
    private ?string $cacheDirectory                                         = null;
    private ?Directory $cachedReport                                        = null;

    public function __construct(Driver $driver, Filter $filter)
    {
        $this->driver = $driver;
        $this->filter = $filter;
        $this->data   = new ProcessedCodeCoverageData;
        $this->wizard = new Wizard;
    }

    /**
     * Returns the code coverage information as a graph of node objects.
     */
    public function getReport(): Directory
    {
        if ($this->cachedReport === null) {
            $this->cachedReport = (new Builder($this->analyser()))->build($this);
        }

        return $this->cachedReport;
    }

    /**
     * Clears collected code coverage data.
     */
    public function clear(): void
    {
        $this->currentId    = null;
        $this->currentSize  = null;
        $this->data         = new ProcessedCodeCoverageData;
        $this->tests        = [];
        $this->cachedReport = null;
    }

    /**
     * @internal
     */
    public function clearCache(): void
    {
        $this->cachedReport = null;
    }

    /**
     * Returns the filter object used.
     */
    public function filter(): Filter
    {
        return $this->filter;
    }

    /**
     * Returns the collected code coverage data.
     */
    public function getData(bool $raw = false): ProcessedCodeCoverageData
    {
        if (!$raw) {
            if ($this->includeUncoveredFiles) {
                $this->addUncoveredFilesFromFilter();
            }
        }

        return $this->data;
    }

    /**
     * Sets the coverage data.
     */
    public function setData(ProcessedCodeCoverageData $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array<string, TestType>
     */
    public function getTests(): array
    {
        return $this->tests;
    }

    /**
     * @param array<string, TestType> $tests
     */
    public function setTests(array $tests): void
    {
        $this->tests = $tests;
    }

    public function start(string $id, ?TestSize $size = null, bool $clear = false): void
    {
        if ($clear) {
            $this->clear();
        }

        $this->currentId   = $id;
        $this->currentSize = $size;

        $this->driver->start();

        $this->cachedReport = null;
    }

    /**
     * @param array<string,list<int>> $linesToBeIgnored
     */
    public function stop(bool $append = true, ?TestStatus $status = null, array|false $linesToBeCovered = [], array $linesToBeUsed = [], array $linesToBeIgnored = []): RawCodeCoverageData
    {
        $data = $this->driver->stop();

        $this->linesToBeIgnored = array_merge_recursive(
            $this->linesToBeIgnored,
            $linesToBeIgnored,
        );

        $this->append($data, null, $append, $status, $linesToBeCovered, $linesToBeUsed, $linesToBeIgnored);

        $this->currentId    = null;
        $this->currentSize  = null;
        $this->cachedReport = null;

        return $data;
    }

    /**
     * @param array<string,list<int>> $linesToBeIgnored
     *
     * @throws ReflectionException
     * @throws TestIdMissingException
     * @throws UnintentionallyCoveredCodeException
     */
    public function append(RawCodeCoverageData $rawData, ?string $id = null, bool $append = true, ?TestStatus $status = null, array|false $linesToBeCovered = [], array $linesToBeUsed = [], array $linesToBeIgnored = []): void
    {
        if ($id === null) {
            $id = $this->currentId;
        }

        if ($id === null) {
            throw new TestIdMissingException;
        }

        $this->cachedReport = null;

        if ($status === null) {
            $status = TestStatus::unknown();
        }

        $size = $this->currentSize;

        if ($size === null) {
            $size = TestSize::unknown();
        }

        $this->applyFilter($rawData);

        $this->applyExecutableLinesFilter($rawData);

        if ($this->useAnnotationsForIgnoringCode) {
            $this->applyIgnoredLinesFilter($rawData, $linesToBeIgnored);
        }

        $this->data->initializeUnseenData($rawData);

        if (!$append) {
            return;
        }

        if ($id === self::UNCOVERED_FILES) {
            return;
        }

        $this->applyCoversAndUsesFilter(
            $rawData,
            $linesToBeCovered,
            $linesToBeUsed,
            $size,
        );

        if (empty($rawData->lineCoverage())) {
            return;
        }

        $this->tests[$id] = [
            'size'   => $size->asString(),
            'status' => $status->asString(),
        ];

        $this->data->markCodeAsExecutedByTestCase($id, $rawData);
    }

    /**
     * Merges the data from another instance.
     */
    public function merge(self $that): void
    {
        $this->filter->includeFiles(
            $that->filter()->files(),
        );

        $this->data->merge($that->data);

        $this->tests = array_merge($this->tests, $that->getTests());

        $this->cachedReport = null;
    }

    public function enableCheckForUnintentionallyCoveredCode(): void
    {
        $this->checkForUnintentionallyCoveredCode = true;
    }

    public function disableCheckForUnintentionallyCoveredCode(): void
    {
        $this->checkForUnintentionallyCoveredCode = false;
    }

    public function includeUncoveredFiles(): void
    {
        $this->includeUncoveredFiles = true;
    }

    public function excludeUncoveredFiles(): void
    {
        $this->includeUncoveredFiles = false;
    }

    public function enableAnnotationsForIgnoringCode(): void
    {
        $this->useAnnotationsForIgnoringCode = true;
    }

    public function disableAnnotationsForIgnoringCode(): void
    {
        $this->useAnnotationsForIgnoringCode = false;
    }

    public function ignoreDeprecatedCode(): void
    {
        $this->ignoreDeprecatedCode = true;
    }

    public function doNotIgnoreDeprecatedCode(): void
    {
        $this->ignoreDeprecatedCode = false;
    }

    /**
     * @phpstan-assert-if-true !null $this->cacheDirectory
     */
    public function cachesStaticAnalysis(): bool
    {
        return $this->cacheDirectory !== null;
    }

    public function cacheStaticAnalysis(string $directory): void
    {
        $this->cacheDirectory = $directory;
    }

    public function doNotCacheStaticAnalysis(): void
    {
        $this->cacheDirectory = null;
    }

    /**
     * @throws StaticAnalysisCacheNotConfiguredException
     */
    public function cacheDirectory(): string
    {
        if (!$this->cachesStaticAnalysis()) {
            throw new StaticAnalysisCacheNotConfiguredException(
                'The static analysis cache is not configured',
            );
        }

        return $this->cacheDirectory;
    }

    /**
     * @param class-string $className
     */
    public function excludeSubclassesOfThisClassFromUnintentionallyCoveredCodeCheck(string $className): void
    {
        $this->parentClassesExcludedFromUnintentionallyCoveredCodeCheck[] = $className;
    }

    public function enableBranchAndPathCoverage(): void
    {
        $this->driver->enableBranchAndPathCoverage();
    }

    public function disableBranchAndPathCoverage(): void
    {
        $this->driver->disableBranchAndPathCoverage();
    }

    public function collectsBranchAndPathCoverage(): bool
    {
        return $this->driver->collectsBranchAndPathCoverage();
    }

    public function detectsDeadCode(): bool
    {
        return $this->driver->detectsDeadCode();
    }

    /**
     * @internal
     */
    public function driverIsPcov(): bool
    {
        return $this->driver->isPcov();
    }

    /**
     * @internal
     */
    public function driverIsXdebug(): bool
    {
        return $this->driver->isXdebug();
    }

    /**
     * @throws ReflectionException
     * @throws UnintentionallyCoveredCodeException
     */
    private function applyCoversAndUsesFilter(RawCodeCoverageData $rawData, array|false $linesToBeCovered, array $linesToBeUsed, TestSize $size): void
    {
        if ($linesToBeCovered === false) {
            $rawData->clear();

            return;
        }

        if (empty($linesToBeCovered)) {
            return;
        }

        if ($this->checkForUnintentionallyCoveredCode && !$size->isMedium() && !$size->isLarge()) {
            $this->performUnintentionallyCoveredCodeCheck($rawData, $linesToBeCovered, $linesToBeUsed);
        }

        $rawLineData         = $rawData->lineCoverage();
        $filesWithNoCoverage = array_diff_key($rawLineData, $linesToBeCovered);

        foreach (array_keys($filesWithNoCoverage) as $fileWithNoCoverage) {
            $rawData->removeCoverageDataForFile($fileWithNoCoverage);
        }

        if (is_array($linesToBeCovered)) {
            foreach ($linesToBeCovered as $fileToBeCovered => $includedLines) {
                $rawData->keepLineCoverageDataOnlyForLines($fileToBeCovered, $includedLines);
                $rawData->keepFunctionCoverageDataOnlyForLines($fileToBeCovered, $includedLines);
            }
        }
    }

    private function applyFilter(RawCodeCoverageData $data): void
    {
        if (!$this->filter->isEmpty()) {
            foreach (array_keys($data->lineCoverage()) as $filename) {
                if ($this->filter->isExcluded($filename)) {
                    $data->removeCoverageDataForFile($filename);
                }
            }
        }

        $data->skipEmptyLines();
    }

    private function applyExecutableLinesFilter(RawCodeCoverageData $data): void
    {
        foreach (array_keys($data->lineCoverage()) as $filename) {
            if (!$this->filter->isFile($filename)) {
                continue;
            }

            $linesToBranchMap = $this->analyser()->executableLinesIn($filename);

            $data->keepLineCoverageDataOnlyForLines(
                $filename,
                array_keys($linesToBranchMap),
            );

            $data->markExecutableLineByBranch(
                $filename,
                $linesToBranchMap,
            );
        }
    }

    /**
     * @param array<string,list<int>> $linesToBeIgnored
     */
    private function applyIgnoredLinesFilter(RawCodeCoverageData $data, array $linesToBeIgnored): void
    {
        foreach (array_keys($data->lineCoverage()) as $filename) {
            if (!$this->filter->isFile($filename)) {
                continue;
            }

            if (isset($linesToBeIgnored[$filename])) {
                $data->removeCoverageDataForLines(
                    $filename,
                    $linesToBeIgnored[$filename],
                );
            }

            $data->removeCoverageDataForLines(
                $filename,
                $this->analyser()->ignoredLinesFor($filename),
            );
        }
    }

    /**
     * @throws UnintentionallyCoveredCodeException
     */
    private function addUncoveredFilesFromFilter(): void
    {
        $uncoveredFiles = array_diff(
            $this->filter->files(),
            $this->data->coveredFiles(),
        );

        foreach ($uncoveredFiles as $uncoveredFile) {
            if (is_file($uncoveredFile)) {
                $this->append(
                    RawCodeCoverageData::fromUncoveredFile(
                        $uncoveredFile,
                        $this->analyser(),
                    ),
                    self::UNCOVERED_FILES,
                    linesToBeIgnored: $this->linesToBeIgnored,
                );
            }
        }
    }

    /**
     * @throws ReflectionException
     * @throws UnintentionallyCoveredCodeException
     */
    private function performUnintentionallyCoveredCodeCheck(RawCodeCoverageData $data, array $linesToBeCovered, array $linesToBeUsed): void
    {
        $allowedLines = $this->getAllowedLines(
            $linesToBeCovered,
            $linesToBeUsed,
        );

        $unintentionallyCoveredUnits = [];

        foreach ($data->lineCoverage() as $file => $_data) {
            foreach ($_data as $line => $flag) {
                if ($flag === 1 && !isset($allowedLines[$file][$line])) {
                    $unintentionallyCoveredUnits[] = $this->wizard->lookup($file, $line);
                }
            }
        }

        $unintentionallyCoveredUnits = $this->processUnintentionallyCoveredUnits($unintentionallyCoveredUnits);

        if (!empty($unintentionallyCoveredUnits)) {
            throw new UnintentionallyCoveredCodeException(
                $unintentionallyCoveredUnits,
            );
        }
    }

    private function getAllowedLines(array $linesToBeCovered, array $linesToBeUsed): array
    {
        $allowedLines = [];

        foreach (array_keys($linesToBeCovered) as $file) {
            if (!isset($allowedLines[$file])) {
                $allowedLines[$file] = [];
            }

            $allowedLines[$file] = array_merge(
                $allowedLines[$file],
                $linesToBeCovered[$file],
            );
        }

        foreach (array_keys($linesToBeUsed) as $file) {
            if (!isset($allowedLines[$file])) {
                $allowedLines[$file] = [];
            }

            $allowedLines[$file] = array_merge(
                $allowedLines[$file],
                $linesToBeUsed[$file],
            );
        }

        foreach (array_keys($allowedLines) as $file) {
            $allowedLines[$file] = array_flip(
                array_unique($allowedLines[$file]),
            );
        }

        return $allowedLines;
    }

    /**
     * @param list<string> $unintentionallyCoveredUnits
     *
     * @throws ReflectionException
     *
     * @return list<string>
     */
    private function processUnintentionallyCoveredUnits(array $unintentionallyCoveredUnits): array
    {
        $unintentionallyCoveredUnits = array_unique($unintentionallyCoveredUnits);
        $processed                   = [];

        foreach ($unintentionallyCoveredUnits as $unintentionallyCoveredUnit) {
            $tmp = explode('::', $unintentionallyCoveredUnit);

            if (count($tmp) !== 2) {
                $processed[] = $unintentionallyCoveredUnit;

                continue;
            }

            try {
                $class = new ReflectionClass($tmp[0]);

                foreach ($this->parentClassesExcludedFromUnintentionallyCoveredCodeCheck as $parentClass) {
                    if ($class->isSubclassOf($parentClass)) {
                        continue 2;
                    }
                }
            } catch (\ReflectionException $e) {
                throw new ReflectionException(
                    $e->getMessage(),
                    $e->getCode(),
                    $e,
                );
            }

            $processed[] = $tmp[0];
        }

        $processed = array_unique($processed);

        sort($processed);

        return $processed;
    }

    private function analyser(): FileAnalyser
    {
        if ($this->analyser !== null) {
            return $this->analyser;
        }

        $this->analyser = new ParsingFileAnalyser(
            $this->useAnnotationsForIgnoringCode,
            $this->ignoreDeprecatedCode,
        );

        if ($this->cachesStaticAnalysis()) {
            $this->analyser = new CachingFileAnalyser(
                $this->cacheDirectory,
                $this->analyser,
                $this->useAnnotationsForIgnoringCode,
                $this->ignoreDeprecatedCode,
            );
        }

        return $this->analyser;
    }
}
