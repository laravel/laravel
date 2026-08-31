<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\CliArguments;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
final readonly class Configuration
{
    /**
     * @var list<non-empty-string>
     */
    private array $arguments;
    private ?string $atLeastVersion;
    private ?bool $backupGlobals;
    private ?bool $backupStaticProperties;
    private ?bool $beStrictAboutChangesToGlobalState;
    private ?string $bootstrap;
    private ?string $cacheDirectory;
    private ?bool $cacheResult;
    private bool $checkPhpConfiguration;
    private bool $checkVersion;
    private ?string $colors;
    private null|int|string $columns;
    private ?string $configurationFile;

    /**
     * @var ?non-empty-list<non-empty-string>
     */
    private ?array $coverageFilter;
    private ?string $coverageClover;
    private ?string $coverageCobertura;
    private ?string $coverageCrap4J;
    private ?string $coverageHtml;
    private ?string $coveragePhp;
    private ?string $coverageText;
    private ?bool $coverageTextShowUncoveredFiles;
    private ?bool $coverageTextShowOnlySummary;
    private ?string $coverageXml;
    private ?bool $pathCoverage;
    private bool $warmCoverageCache;
    private ?int $defaultTimeLimit;
    private ?bool $disableCodeCoverageIgnore;
    private ?bool $disallowTestOutput;
    private ?bool $enforceTimeLimit;

    /**
     * @var ?non-empty-list<non-empty-string>
     */
    private ?array $excludeGroups;
    private ?int $executionOrder;
    private ?int $executionOrderDefects;
    private ?bool $failOnAllIssues;
    private ?bool $failOnDeprecation;
    private ?bool $failOnPhpunitDeprecation;
    private ?bool $failOnPhpunitWarning;
    private ?bool $failOnEmptyTestSuite;
    private ?bool $failOnIncomplete;
    private ?bool $failOnNotice;
    private ?bool $failOnRisky;
    private ?bool $failOnSkipped;
    private ?bool $failOnWarning;
    private ?bool $doNotFailOnDeprecation;
    private ?bool $doNotFailOnPhpunitDeprecation;
    private ?bool $doNotFailOnPhpunitWarning;
    private ?bool $doNotFailOnEmptyTestSuite;
    private ?bool $doNotFailOnIncomplete;
    private ?bool $doNotFailOnNotice;
    private ?bool $doNotFailOnRisky;
    private ?bool $doNotFailOnSkipped;
    private ?bool $doNotFailOnWarning;
    private ?bool $stopOnDefect;
    private ?bool $stopOnDeprecation;
    private ?string $specificDeprecationToStopOn;
    private ?bool $stopOnError;
    private ?bool $stopOnFailure;
    private ?bool $stopOnIncomplete;
    private ?bool $stopOnNotice;
    private ?bool $stopOnRisky;
    private ?bool $stopOnSkipped;
    private ?bool $stopOnWarning;
    private ?string $filter;
    private ?string $excludeFilter;
    private ?string $generateBaseline;
    private ?string $useBaseline;
    private bool $ignoreBaseline;
    private bool $generateConfiguration;
    private bool $migrateConfiguration;

    /**
     * @var ?non-empty-list<non-empty-string>
     */
    private ?array $groups;

    /**
     * @var ?non-empty-list<non-empty-string>
     */
    private ?array $testsCovering;

    /**
     * @var ?non-empty-list<non-empty-string>
     */
    private ?array $testsUsing;

    /**
     * @var ?non-empty-list<non-empty-string>
     */
    private ?array $testsRequiringPhpExtension;
    private bool $help;
    private ?string $includePath;

    /**
     * @var ?non-empty-array<non-empty-string, non-empty-string>
     */
    private ?array $iniSettings;
    private ?string $junitLogfile;
    private bool $listGroups;
    private bool $listSuites;
    private bool $listTestFiles;
    private bool $listTests;
    private ?string $listTestsXml;
    private ?bool $noCoverage;
    private ?bool $noExtensions;
    private ?bool $noOutput;
    private ?bool $noProgress;
    private ?bool $noResults;
    private ?bool $noLogging;
    private ?bool $processIsolation;
    private ?int $randomOrderSeed;
    private ?bool $reportUselessTests;
    private ?bool $resolveDependencies;
    private ?bool $reverseList;
    private ?bool $stderr;
    private ?bool $strictCoverage;
    private ?string $teamcityLogfile;
    private ?bool $teamCityPrinter;
    private ?string $testdoxHtmlFile;
    private ?string $testdoxTextFile;
    private ?bool $testdoxPrinter;
    private ?bool $testdoxPrinterSummary;

    /**
     * @var ?non-empty-list<non-empty-string>
     */
    private ?array $testSuffixes;
    private ?string $testSuite;
    private ?string $excludeTestSuite;
    private bool $useDefaultConfiguration;
    private ?bool $displayDetailsOnAllIssues;
    private ?bool $displayDetailsOnIncompleteTests;
    private ?bool $displayDetailsOnSkippedTests;
    private ?bool $displayDetailsOnTestsThatTriggerDeprecations;
    private ?bool $displayDetailsOnPhpunitDeprecations;
    private ?bool $displayDetailsOnTestsThatTriggerErrors;
    private ?bool $displayDetailsOnTestsThatTriggerNotices;
    private ?bool $displayDetailsOnTestsThatTriggerWarnings;
    private bool $version;
    private ?string $logEventsText;
    private ?string $logEventsVerboseText;
    private bool $debug;

    /**
     * @var ?non-empty-list<non-empty-string>
     */
    private ?array $extensions;

    /**
     * @param list<non-empty-string>                               $arguments
     * @param ?non-empty-list<non-empty-string>                    $excludeGroups
     * @param ?non-empty-list<non-empty-string>                    $groups
     * @param ?non-empty-list<non-empty-string>                    $testsCovering
     * @param ?non-empty-list<non-empty-string>                    $testsUsing
     * @param ?non-empty-list<non-empty-string>                    $testsRequiringPhpExtension
     * @param ?non-empty-array<non-empty-string, non-empty-string> $iniSettings
     * @param ?non-empty-list<non-empty-string>                    $testSuffixes
     * @param ?non-empty-list<non-empty-string>                    $coverageFilter
     * @param ?non-empty-list<non-empty-string>                    $extensions
     */
    public function __construct(array $arguments, ?string $atLeastVersion, ?bool $backupGlobals, ?bool $backupStaticProperties, ?bool $beStrictAboutChangesToGlobalState, ?string $bootstrap, ?string $cacheDirectory, ?bool $cacheResult, bool $checkPhpConfiguration, bool $checkVersion, ?string $colors, null|int|string $columns, ?string $configurationFile, ?string $coverageClover, ?string $coverageCobertura, ?string $coverageCrap4J, ?string $coverageHtml, ?string $coveragePhp, ?string $coverageText, ?bool $coverageTextShowUncoveredFiles, ?bool $coverageTextShowOnlySummary, ?string $coverageXml, ?bool $pathCoverage, bool $warmCoverageCache, ?int $defaultTimeLimit, ?bool $disableCodeCoverageIgnore, ?bool $disallowTestOutput, ?bool $enforceTimeLimit, ?array $excludeGroups, ?int $executionOrder, ?int $executionOrderDefects, ?bool $failOnAllIssues, ?bool $failOnDeprecation, ?bool $failOnPhpunitDeprecation, ?bool $failOnPhpunitWarning, ?bool $failOnEmptyTestSuite, ?bool $failOnIncomplete, ?bool $failOnNotice, ?bool $failOnRisky, ?bool $failOnSkipped, ?bool $failOnWarning, ?bool $doNotFailOnDeprecation, ?bool $doNotFailOnPhpunitDeprecation, ?bool $doNotFailOnPhpunitWarning, ?bool $doNotFailOnEmptyTestSuite, ?bool $doNotFailOnIncomplete, ?bool $doNotFailOnNotice, ?bool $doNotFailOnRisky, ?bool $doNotFailOnSkipped, ?bool $doNotFailOnWarning, ?bool $stopOnDefect, ?bool $stopOnDeprecation, ?string $specificDeprecationToStopOn, ?bool $stopOnError, ?bool $stopOnFailure, ?bool $stopOnIncomplete, ?bool $stopOnNotice, ?bool $stopOnRisky, ?bool $stopOnSkipped, ?bool $stopOnWarning, ?string $filter, ?string $excludeFilter, ?string $generateBaseline, ?string $useBaseline, bool $ignoreBaseline, bool $generateConfiguration, bool $migrateConfiguration, ?array $groups, ?array $testsCovering, ?array $testsUsing, ?array $testsRequiringPhpExtension, bool $help, ?string $includePath, ?array $iniSettings, ?string $junitLogfile, bool $listGroups, bool $listSuites, bool $listTestFiles, bool $listTests, ?string $listTestsXml, ?bool $noCoverage, ?bool $noExtensions, ?bool $noOutput, ?bool $noProgress, ?bool $noResults, ?bool $noLogging, ?bool $processIsolation, ?int $randomOrderSeed, ?bool $reportUselessTests, ?bool $resolveDependencies, ?bool $reverseList, ?bool $stderr, ?bool $strictCoverage, ?string $teamcityLogfile, ?string $testdoxHtmlFile, ?string $testdoxTextFile, ?array $testSuffixes, ?string $testSuite, ?string $excludeTestSuite, bool $useDefaultConfiguration, ?bool $displayDetailsOnAllIssues, ?bool $displayDetailsOnIncompleteTests, ?bool $displayDetailsOnSkippedTests, ?bool $displayDetailsOnTestsThatTriggerDeprecations, ?bool $displayDetailsOnPhpunitDeprecations, ?bool $displayDetailsOnTestsThatTriggerErrors, ?bool $displayDetailsOnTestsThatTriggerNotices, ?bool $displayDetailsOnTestsThatTriggerWarnings, bool $version, ?array $coverageFilter, ?string $logEventsText, ?string $logEventsVerboseText, ?bool $printerTeamCity, ?bool $testdoxPrinter, ?bool $testdoxPrinterSummary, bool $debug, ?array $extensions)
    {
        $this->arguments                                    = $arguments;
        $this->atLeastVersion                               = $atLeastVersion;
        $this->backupGlobals                                = $backupGlobals;
        $this->backupStaticProperties                       = $backupStaticProperties;
        $this->beStrictAboutChangesToGlobalState            = $beStrictAboutChangesToGlobalState;
        $this->bootstrap                                    = $bootstrap;
        $this->cacheDirectory                               = $cacheDirectory;
        $this->cacheResult                                  = $cacheResult;
        $this->checkPhpConfiguration                        = $checkPhpConfiguration;
        $this->checkVersion                                 = $checkVersion;
        $this->colors                                       = $colors;
        $this->columns                                      = $columns;
        $this->configurationFile                            = $configurationFile;
        $this->coverageFilter                               = $coverageFilter;
        $this->coverageClover                               = $coverageClover;
        $this->coverageCobertura                            = $coverageCobertura;
        $this->coverageCrap4J                               = $coverageCrap4J;
        $this->coverageHtml                                 = $coverageHtml;
        $this->coveragePhp                                  = $coveragePhp;
        $this->coverageText                                 = $coverageText;
        $this->coverageTextShowUncoveredFiles               = $coverageTextShowUncoveredFiles;
        $this->coverageTextShowOnlySummary                  = $coverageTextShowOnlySummary;
        $this->coverageXml                                  = $coverageXml;
        $this->pathCoverage                                 = $pathCoverage;
        $this->warmCoverageCache                            = $warmCoverageCache;
        $this->defaultTimeLimit                             = $defaultTimeLimit;
        $this->disableCodeCoverageIgnore                    = $disableCodeCoverageIgnore;
        $this->disallowTestOutput                           = $disallowTestOutput;
        $this->enforceTimeLimit                             = $enforceTimeLimit;
        $this->excludeGroups                                = $excludeGroups;
        $this->executionOrder                               = $executionOrder;
        $this->executionOrderDefects                        = $executionOrderDefects;
        $this->failOnAllIssues                              = $failOnAllIssues;
        $this->failOnDeprecation                            = $failOnDeprecation;
        $this->failOnPhpunitDeprecation                     = $failOnPhpunitDeprecation;
        $this->failOnPhpunitWarning                         = $failOnPhpunitWarning;
        $this->failOnEmptyTestSuite                         = $failOnEmptyTestSuite;
        $this->failOnIncomplete                             = $failOnIncomplete;
        $this->failOnNotice                                 = $failOnNotice;
        $this->failOnRisky                                  = $failOnRisky;
        $this->failOnSkipped                                = $failOnSkipped;
        $this->failOnWarning                                = $failOnWarning;
        $this->doNotFailOnDeprecation                       = $doNotFailOnDeprecation;
        $this->doNotFailOnPhpunitDeprecation                = $doNotFailOnPhpunitDeprecation;
        $this->doNotFailOnPhpunitWarning                    = $doNotFailOnPhpunitWarning;
        $this->doNotFailOnEmptyTestSuite                    = $doNotFailOnEmptyTestSuite;
        $this->doNotFailOnIncomplete                        = $doNotFailOnIncomplete;
        $this->doNotFailOnNotice                            = $doNotFailOnNotice;
        $this->doNotFailOnRisky                             = $doNotFailOnRisky;
        $this->doNotFailOnSkipped                           = $doNotFailOnSkipped;
        $this->doNotFailOnWarning                           = $doNotFailOnWarning;
        $this->stopOnDefect                                 = $stopOnDefect;
        $this->stopOnDeprecation                            = $stopOnDeprecation;
        $this->specificDeprecationToStopOn                  = $specificDeprecationToStopOn;
        $this->stopOnError                                  = $stopOnError;
        $this->stopOnFailure                                = $stopOnFailure;
        $this->stopOnIncomplete                             = $stopOnIncomplete;
        $this->stopOnNotice                                 = $stopOnNotice;
        $this->stopOnRisky                                  = $stopOnRisky;
        $this->stopOnSkipped                                = $stopOnSkipped;
        $this->stopOnWarning                                = $stopOnWarning;
        $this->filter                                       = $filter;
        $this->excludeFilter                                = $excludeFilter;
        $this->generateBaseline                             = $generateBaseline;
        $this->useBaseline                                  = $useBaseline;
        $this->ignoreBaseline                               = $ignoreBaseline;
        $this->generateConfiguration                        = $generateConfiguration;
        $this->migrateConfiguration                         = $migrateConfiguration;
        $this->groups                                       = $groups;
        $this->testsCovering                                = $testsCovering;
        $this->testsUsing                                   = $testsUsing;
        $this->testsRequiringPhpExtension                   = $testsRequiringPhpExtension;
        $this->help                                         = $help;
        $this->includePath                                  = $includePath;
        $this->iniSettings                                  = $iniSettings;
        $this->junitLogfile                                 = $junitLogfile;
        $this->listGroups                                   = $listGroups;
        $this->listSuites                                   = $listSuites;
        $this->listTestFiles                                = $listTestFiles;
        $this->listTests                                    = $listTests;
        $this->listTestsXml                                 = $listTestsXml;
        $this->noCoverage                                   = $noCoverage;
        $this->noExtensions                                 = $noExtensions;
        $this->noOutput                                     = $noOutput;
        $this->noProgress                                   = $noProgress;
        $this->noResults                                    = $noResults;
        $this->noLogging                                    = $noLogging;
        $this->processIsolation                             = $processIsolation;
        $this->randomOrderSeed                              = $randomOrderSeed;
        $this->reportUselessTests                           = $reportUselessTests;
        $this->resolveDependencies                          = $resolveDependencies;
        $this->reverseList                                  = $reverseList;
        $this->stderr                                       = $stderr;
        $this->strictCoverage                               = $strictCoverage;
        $this->teamcityLogfile                              = $teamcityLogfile;
        $this->testdoxHtmlFile                              = $testdoxHtmlFile;
        $this->testdoxTextFile                              = $testdoxTextFile;
        $this->testSuffixes                                 = $testSuffixes;
        $this->testSuite                                    = $testSuite;
        $this->excludeTestSuite                             = $excludeTestSuite;
        $this->useDefaultConfiguration                      = $useDefaultConfiguration;
        $this->displayDetailsOnAllIssues                    = $displayDetailsOnAllIssues;
        $this->displayDetailsOnIncompleteTests              = $displayDetailsOnIncompleteTests;
        $this->displayDetailsOnSkippedTests                 = $displayDetailsOnSkippedTests;
        $this->displayDetailsOnTestsThatTriggerDeprecations = $displayDetailsOnTestsThatTriggerDeprecations;
        $this->displayDetailsOnPhpunitDeprecations          = $displayDetailsOnPhpunitDeprecations;
        $this->displayDetailsOnTestsThatTriggerErrors       = $displayDetailsOnTestsThatTriggerErrors;
        $this->displayDetailsOnTestsThatTriggerNotices      = $displayDetailsOnTestsThatTriggerNotices;
        $this->displayDetailsOnTestsThatTriggerWarnings     = $displayDetailsOnTestsThatTriggerWarnings;
        $this->version                                      = $version;
        $this->logEventsText                                = $logEventsText;
        $this->logEventsVerboseText                         = $logEventsVerboseText;
        $this->teamCityPrinter                              = $printerTeamCity;
        $this->testdoxPrinter                               = $testdoxPrinter;
        $this->testdoxPrinterSummary                        = $testdoxPrinterSummary;
        $this->debug                                        = $debug;
        $this->extensions                                   = $extensions;
    }

    /**
     * @return list<non-empty-string>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * @phpstan-assert-if-true !null $this->atLeastVersion
     */
    public function hasAtLeastVersion(): bool
    {
        return $this->atLeastVersion !== null;
    }

    /**
     * @throws Exception
     */
    public function atLeastVersion(): string
    {
        if (!$this->hasAtLeastVersion()) {
            throw new Exception;
        }

        return $this->atLeastVersion;
    }

    /**
     * @phpstan-assert-if-true !null $this->backupGlobals
     */
    public function hasBackupGlobals(): bool
    {
        return $this->backupGlobals !== null;
    }

    /**
     * @throws Exception
     */
    public function backupGlobals(): bool
    {
        if (!$this->hasBackupGlobals()) {
            throw new Exception;
        }

        return $this->backupGlobals;
    }

    /**
     * @phpstan-assert-if-true !null $this->backupStaticProperties
     */
    public function hasBackupStaticProperties(): bool
    {
        return $this->backupStaticProperties !== null;
    }

    /**
     * @throws Exception
     */
    public function backupStaticProperties(): bool
    {
        if (!$this->hasBackupStaticProperties()) {
            throw new Exception;
        }

        return $this->backupStaticProperties;
    }

    /**
     * @phpstan-assert-if-true !null $this->beStrictAboutChangesToGlobalState
     */
    public function hasBeStrictAboutChangesToGlobalState(): bool
    {
        return $this->beStrictAboutChangesToGlobalState !== null;
    }

    /**
     * @throws Exception
     */
    public function beStrictAboutChangesToGlobalState(): bool
    {
        if (!$this->hasBeStrictAboutChangesToGlobalState()) {
            throw new Exception;
        }

        return $this->beStrictAboutChangesToGlobalState;
    }

    /**
     * @phpstan-assert-if-true !null $this->bootstrap
     */
    public function hasBootstrap(): bool
    {
        return $this->bootstrap !== null;
    }

    /**
     * @throws Exception
     */
    public function bootstrap(): string
    {
        if (!$this->hasBootstrap()) {
            throw new Exception;
        }

        return $this->bootstrap;
    }

    /**
     * @phpstan-assert-if-true !null $this->cacheDirectory
     */
    public function hasCacheDirectory(): bool
    {
        return $this->cacheDirectory !== null;
    }

    /**
     * @throws Exception
     */
    public function cacheDirectory(): string
    {
        if (!$this->hasCacheDirectory()) {
            throw new Exception;
        }

        return $this->cacheDirectory;
    }

    /**
     * @phpstan-assert-if-true !null $this->cacheResult
     */
    public function hasCacheResult(): bool
    {
        return $this->cacheResult !== null;
    }

    /**
     * @throws Exception
     */
    public function cacheResult(): bool
    {
        if (!$this->hasCacheResult()) {
            throw new Exception;
        }

        return $this->cacheResult;
    }

    public function checkPhpConfiguration(): bool
    {
        return $this->checkPhpConfiguration;
    }

    public function checkVersion(): bool
    {
        return $this->checkVersion;
    }

    /**
     * @phpstan-assert-if-true !null $this->colors
     */
    public function hasColors(): bool
    {
        return $this->colors !== null;
    }

    /**
     * @throws Exception
     */
    public function colors(): string
    {
        if (!$this->hasColors()) {
            throw new Exception;
        }

        return $this->colors;
    }

    /**
     * @phpstan-assert-if-true !null $this->columns
     */
    public function hasColumns(): bool
    {
        return $this->columns !== null;
    }

    /**
     * @throws Exception
     */
    public function columns(): int|string
    {
        if (!$this->hasColumns()) {
            throw new Exception;
        }

        return $this->columns;
    }

    /**
     * @phpstan-assert-if-true !null $this->configurationFile
     */
    public function hasConfigurationFile(): bool
    {
        return $this->configurationFile !== null;
    }

    /**
     * @throws Exception
     */
    public function configurationFile(): string
    {
        if (!$this->hasConfigurationFile()) {
            throw new Exception;
        }

        return $this->configurationFile;
    }

    /**
     * @phpstan-assert-if-true !null $this->coverageFilter
     */
    public function hasCoverageFilter(): bool
    {
        return $this->coverageFilter !== null;
    }

    /**
     * @throws Exception
     *
     * @return non-empty-list<non-empty-string>
     */
    public function coverageFilter(): array
    {
        if (!$this->hasCoverageFilter()) {
            throw new Exception;
        }

        return $this->coverageFilter;
    }

    /**
     * @phpstan-assert-if-true !null $this->coverageClover
     */
    public function hasCoverageClover(): bool
    {
        return $this->coverageClover !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageClover(): string
    {
        if (!$this->hasCoverageClover()) {
            throw new Exception;
        }

        return $this->coverageClover;
    }

    /**
     * @phpstan-assert-if-true !null $this->coverageCobertura
     */
    public function hasCoverageCobertura(): bool
    {
        return $this->coverageCobertura !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageCobertura(): string
    {
        if (!$this->hasCoverageCobertura()) {
            throw new Exception;
        }

        return $this->coverageCobertura;
    }

    /**
     * @phpstan-assert-if-true !null $this->coverageCrap4J
     */
    public function hasCoverageCrap4J(): bool
    {
        return $this->coverageCrap4J !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageCrap4J(): string
    {
        if (!$this->hasCoverageCrap4J()) {
            throw new Exception;
        }

        return $this->coverageCrap4J;
    }

    /**
     * @phpstan-assert-if-true !null $this->coverageHtml
     */
    public function hasCoverageHtml(): bool
    {
        return $this->coverageHtml !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageHtml(): string
    {
        if (!$this->hasCoverageHtml()) {
            throw new Exception;
        }

        return $this->coverageHtml;
    }

    /**
     * @phpstan-assert-if-true !null $this->coveragePhp
     */
    public function hasCoveragePhp(): bool
    {
        return $this->coveragePhp !== null;
    }

    /**
     * @throws Exception
     */
    public function coveragePhp(): string
    {
        if (!$this->hasCoveragePhp()) {
            throw new Exception;
        }

        return $this->coveragePhp;
    }

    /**
     * @phpstan-assert-if-true !null $this->coverageText
     */
    public function hasCoverageText(): bool
    {
        return $this->coverageText !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageText(): string
    {
        if (!$this->hasCoverageText()) {
            throw new Exception;
        }

        return $this->coverageText;
    }

    /**
     * @phpstan-assert-if-true !null $this->coverageTextShowUncoveredFiles
     */
    public function hasCoverageTextShowUncoveredFiles(): bool
    {
        return $this->coverageTextShowUncoveredFiles !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageTextShowUncoveredFiles(): bool
    {
        if (!$this->hasCoverageTextShowUncoveredFiles()) {
            throw new Exception;
        }

        return $this->coverageTextShowUncoveredFiles;
    }

    /**
     * @phpstan-assert-if-true !null $this->coverageTextShowOnlySummary
     */
    public function hasCoverageTextShowOnlySummary(): bool
    {
        return $this->coverageTextShowOnlySummary !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageTextShowOnlySummary(): bool
    {
        if (!$this->hasCoverageTextShowOnlySummary()) {
            throw new Exception;
        }

        return $this->coverageTextShowOnlySummary;
    }

    /**
     * @phpstan-assert-if-true !null $this->coverageXml
     */
    public function hasCoverageXml(): bool
    {
        return $this->coverageXml !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageXml(): string
    {
        if (!$this->hasCoverageXml()) {
            throw new Exception;
        }

        return $this->coverageXml;
    }

    /**
     * @phpstan-assert-if-true !null $this->pathCoverage
     */
    public function hasPathCoverage(): bool
    {
        return $this->pathCoverage !== null;
    }

    /**
     * @throws Exception
     */
    public function pathCoverage(): bool
    {
        if (!$this->hasPathCoverage()) {
            throw new Exception;
        }

        return $this->pathCoverage;
    }

    public function warmCoverageCache(): bool
    {
        return $this->warmCoverageCache;
    }

    /**
     * @phpstan-assert-if-true !null $this->defaultTimeLimit
     */
    public function hasDefaultTimeLimit(): bool
    {
        return $this->defaultTimeLimit !== null;
    }

    /**
     * @throws Exception
     */
    public function defaultTimeLimit(): int
    {
        if (!$this->hasDefaultTimeLimit()) {
            throw new Exception;
        }

        return $this->defaultTimeLimit;
    }

    /**
     * @phpstan-assert-if-true !null $this->disableCodeCoverageIgnore
     */
    public function hasDisableCodeCoverageIgnore(): bool
    {
        return $this->disableCodeCoverageIgnore !== null;
    }

    /**
     * @throws Exception
     */
    public function disableCodeCoverageIgnore(): bool
    {
        if (!$this->hasDisableCodeCoverageIgnore()) {
            throw new Exception;
        }

        return $this->disableCodeCoverageIgnore;
    }

    /**
     * @phpstan-assert-if-true !null $this->disallowTestOutput
     */
    public function hasDisallowTestOutput(): bool
    {
        return $this->disallowTestOutput !== null;
    }

    /**
     * @throws Exception
     */
    public function disallowTestOutput(): bool
    {
        if (!$this->hasDisallowTestOutput()) {
            throw new Exception;
        }

        return $this->disallowTestOutput;
    }

    /**
     * @phpstan-assert-if-true !null $this->enforceTimeLimit
     */
    public function hasEnforceTimeLimit(): bool
    {
        return $this->enforceTimeLimit !== null;
    }

    /**
     * @throws Exception
     */
    public function enforceTimeLimit(): bool
    {
        if (!$this->hasEnforceTimeLimit()) {
            throw new Exception;
        }

        return $this->enforceTimeLimit;
    }

    /**
     * @phpstan-assert-if-true !null $this->excludeGroups
     */
    public function hasExcludeGroups(): bool
    {
        return $this->excludeGroups !== null;
    }

    /**
     * @throws Exception
     *
     * @return non-empty-list<non-empty-string>
     */
    public function excludeGroups(): array
    {
        if (!$this->hasExcludeGroups()) {
            throw new Exception;
        }

        return $this->excludeGroups;
    }

    /**
     * @phpstan-assert-if-true !null $this->executionOrder
     */
    public function hasExecutionOrder(): bool
    {
        return $this->executionOrder !== null;
    }

    /**
     * @throws Exception
     */
    public function executionOrder(): int
    {
        if (!$this->hasExecutionOrder()) {
            throw new Exception;
        }

        return $this->executionOrder;
    }

    /**
     * @phpstan-assert-if-true !null $this->executionOrderDefects
     */
    public function hasExecutionOrderDefects(): bool
    {
        return $this->executionOrderDefects !== null;
    }

    /**
     * @throws Exception
     */
    public function executionOrderDefects(): int
    {
        if (!$this->hasExecutionOrderDefects()) {
            throw new Exception;
        }

        return $this->executionOrderDefects;
    }

    /**
     * @phpstan-assert-if-true !null $this->failOnAllIssues
     */
    public function hasFailOnAllIssues(): bool
    {
        return $this->failOnAllIssues !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnAllIssues(): bool
    {
        if (!$this->hasFailOnAllIssues()) {
            throw new Exception;
        }

        return $this->failOnAllIssues;
    }

    /**
     * @phpstan-assert-if-true !null $this->failOnDeprecation
     */
    public function hasFailOnDeprecation(): bool
    {
        return $this->failOnDeprecation !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnDeprecation(): bool
    {
        if (!$this->hasFailOnDeprecation()) {
            throw new Exception;
        }

        return $this->failOnDeprecation;
    }

    /**
     * @phpstan-assert-if-true !null $this->failOnPhpunitDeprecation
     */
    public function hasFailOnPhpunitDeprecation(): bool
    {
        return $this->failOnPhpunitDeprecation !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnPhpunitDeprecation(): bool
    {
        if (!$this->hasFailOnPhpunitDeprecation()) {
            throw new Exception;
        }

        return $this->failOnPhpunitDeprecation;
    }

    /**
     * @phpstan-assert-if-true !null $this->failOnPhpunitWarning
     */
    public function hasFailOnPhpunitWarning(): bool
    {
        return $this->failOnPhpunitWarning !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnPhpunitWarning(): bool
    {
        if (!$this->hasFailOnPhpunitWarning()) {
            throw new Exception;
        }

        return $this->failOnPhpunitWarning;
    }

    /**
     * @phpstan-assert-if-true !null $this->failOnEmptyTestSuite
     */
    public function hasFailOnEmptyTestSuite(): bool
    {
        return $this->failOnEmptyTestSuite !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnEmptyTestSuite(): bool
    {
        if (!$this->hasFailOnEmptyTestSuite()) {
            throw new Exception;
        }

        return $this->failOnEmptyTestSuite;
    }

    /**
     * @phpstan-assert-if-true !null $this->failOnIncomplete
     */
    public function hasFailOnIncomplete(): bool
    {
        return $this->failOnIncomplete !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnIncomplete(): bool
    {
        if (!$this->hasFailOnIncomplete()) {
            throw new Exception;
        }

        return $this->failOnIncomplete;
    }

    /**
     * @phpstan-assert-if-true !null $this->failOnNotice
     */
    public function hasFailOnNotice(): bool
    {
        return $this->failOnNotice !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnNotice(): bool
    {
        if (!$this->hasFailOnNotice()) {
            throw new Exception;
        }

        return $this->failOnNotice;
    }

    /**
     * @phpstan-assert-if-true !null $this->failOnRisky
     */
    public function hasFailOnRisky(): bool
    {
        return $this->failOnRisky !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnRisky(): bool
    {
        if (!$this->hasFailOnRisky()) {
            throw new Exception;
        }

        return $this->failOnRisky;
    }

    /**
     * @phpstan-assert-if-true !null $this->failOnSkipped
     */
    public function hasFailOnSkipped(): bool
    {
        return $this->failOnSkipped !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnSkipped(): bool
    {
        if (!$this->hasFailOnSkipped()) {
            throw new Exception;
        }

        return $this->failOnSkipped;
    }

    /**
     * @phpstan-assert-if-true !null $this->failOnWarning
     */
    public function hasFailOnWarning(): bool
    {
        return $this->failOnWarning !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnWarning(): bool
    {
        if (!$this->hasFailOnWarning()) {
            throw new Exception;
        }

        return $this->failOnWarning;
    }

    /**
     * @phpstan-assert-if-true !null $this->doNotFailOnDeprecation
     */
    public function hasDoNotFailOnDeprecation(): bool
    {
        return $this->doNotFailOnDeprecation !== null;
    }

    /**
     * @throws Exception
     */
    public function doNotFailOnDeprecation(): bool
    {
        if (!$this->hasDoNotFailOnDeprecation()) {
            throw new Exception;
        }

        return $this->doNotFailOnDeprecation;
    }

    /**
     * @phpstan-assert-if-true !null $this->doNotFailOnPhpunitDeprecation
     */
    public function hasDoNotFailOnPhpunitDeprecation(): bool
    {
        return $this->doNotFailOnPhpunitDeprecation !== null;
    }

    /**
     * @throws Exception
     */
    public function doNotFailOnPhpunitDeprecation(): bool
    {
        if (!$this->hasDoNotFailOnPhpunitDeprecation()) {
            throw new Exception;
        }

        return $this->doNotFailOnPhpunitDeprecation;
    }

    /**
     * @phpstan-assert-if-true !null $this->doNotFailOnPhpunitWarning
     */
    public function hasDoNotFailOnPhpunitWarning(): bool
    {
        return $this->doNotFailOnPhpunitWarning !== null;
    }

    /**
     * @throws Exception
     */
    public function doNotFailOnPhpunitWarning(): bool
    {
        if (!$this->hasDoNotFailOnPhpunitWarning()) {
            throw new Exception;
        }

        return $this->doNotFailOnPhpunitWarning;
    }

    /**
     * @phpstan-assert-if-true !null $this->doNotFailOnEmptyTestSuite
     */
    public function hasDoNotFailOnEmptyTestSuite(): bool
    {
        return $this->doNotFailOnEmptyTestSuite !== null;
    }

    /**
     * @throws Exception
     */
    public function doNotFailOnEmptyTestSuite(): bool
    {
        if (!$this->hasDoNotFailOnEmptyTestSuite()) {
            throw new Exception;
        }

        return $this->doNotFailOnEmptyTestSuite;
    }

    /**
     * @phpstan-assert-if-true !null $this->doNotFailOnIncomplete
     */
    public function hasDoNotFailOnIncomplete(): bool
    {
        return $this->doNotFailOnIncomplete !== null;
    }

    /**
     * @throws Exception
     */
    public function doNotFailOnIncomplete(): bool
    {
        if (!$this->hasDoNotFailOnIncomplete()) {
            throw new Exception;
        }

        return $this->doNotFailOnIncomplete;
    }

    /**
     * @phpstan-assert-if-true !null $this->doNotFailOnNotice
     */
    public function hasDoNotFailOnNotice(): bool
    {
        return $this->doNotFailOnNotice !== null;
    }

    /**
     * @throws Exception
     */
    public function doNotFailOnNotice(): bool
    {
        if (!$this->hasDoNotFailOnNotice()) {
            throw new Exception;
        }

        return $this->doNotFailOnNotice;
    }

    /**
     * @phpstan-assert-if-true !null $this->doNotFailOnRisky
     */
    public function hasDoNotFailOnRisky(): bool
    {
        return $this->doNotFailOnRisky !== null;
    }

    /**
     * @throws Exception
     */
    public function doNotFailOnRisky(): bool
    {
        if (!$this->hasDoNotFailOnRisky()) {
            throw new Exception;
        }

        return $this->doNotFailOnRisky;
    }

    /**
     * @phpstan-assert-if-true !null $this->doNotFailOnSkipped
     */
    public function hasDoNotFailOnSkipped(): bool
    {
        return $this->doNotFailOnSkipped !== null;
    }

    /**
     * @throws Exception
     */
    public function doNotFailOnSkipped(): bool
    {
        if (!$this->hasDoNotFailOnSkipped()) {
            throw new Exception;
        }

        return $this->doNotFailOnSkipped;
    }

    /**
     * @phpstan-assert-if-true !null $this->doNotFailOnWarning
     */
    public function hasDoNotFailOnWarning(): bool
    {
        return $this->doNotFailOnWarning !== null;
    }

    /**
     * @throws Exception
     */
    public function doNotFailOnWarning(): bool
    {
        if (!$this->hasDoNotFailOnWarning()) {
            throw new Exception;
        }

        return $this->doNotFailOnWarning;
    }

    /**
     * @phpstan-assert-if-true !null $this->stopOnDefect
     */
    public function hasStopOnDefect(): bool
    {
        return $this->stopOnDefect !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnDefect(): bool
    {
        if (!$this->hasStopOnDefect()) {
            throw new Exception;
        }

        return $this->stopOnDefect;
    }

    /**
     * @phpstan-assert-if-true !null $this->stopOnDeprecation
     */
    public function hasStopOnDeprecation(): bool
    {
        return $this->stopOnDeprecation !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnDeprecation(): bool
    {
        if (!$this->hasStopOnDeprecation()) {
            throw new Exception;
        }

        return $this->stopOnDeprecation;
    }

    /**
     * @phpstan-assert-if-true !null $this->specificDeprecationToStopOn
     */
    public function hasSpecificDeprecationToStopOn(): bool
    {
        return $this->specificDeprecationToStopOn !== null;
    }

    /**
     * @throws Exception
     */
    public function specificDeprecationToStopOn(): string
    {
        if (!$this->hasSpecificDeprecationToStopOn()) {
            throw new Exception;
        }

        return $this->specificDeprecationToStopOn;
    }

    /**
     * @phpstan-assert-if-true !null $this->stopOnError
     */
    public function hasStopOnError(): bool
    {
        return $this->stopOnError !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnError(): bool
    {
        if (!$this->hasStopOnError()) {
            throw new Exception;
        }

        return $this->stopOnError;
    }

    /**
     * @phpstan-assert-if-true !null $this->stopOnFailure
     */
    public function hasStopOnFailure(): bool
    {
        return $this->stopOnFailure !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnFailure(): bool
    {
        if (!$this->hasStopOnFailure()) {
            throw new Exception;
        }

        return $this->stopOnFailure;
    }

    /**
     * @phpstan-assert-if-true !null $this->stopOnIncomplete
     */
    public function hasStopOnIncomplete(): bool
    {
        return $this->stopOnIncomplete !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnIncomplete(): bool
    {
        if (!$this->hasStopOnIncomplete()) {
            throw new Exception;
        }

        return $this->stopOnIncomplete;
    }

    /**
     * @phpstan-assert-if-true !null $this->stopOnNotice
     */
    public function hasStopOnNotice(): bool
    {
        return $this->stopOnNotice !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnNotice(): bool
    {
        if (!$this->hasStopOnNotice()) {
            throw new Exception;
        }

        return $this->stopOnNotice;
    }

    /**
     * @phpstan-assert-if-true !null $this->stopOnRisky
     */
    public function hasStopOnRisky(): bool
    {
        return $this->stopOnRisky !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnRisky(): bool
    {
        if (!$this->hasStopOnRisky()) {
            throw new Exception;
        }

        return $this->stopOnRisky;
    }

    /**
     * @phpstan-assert-if-true !null $this->stopOnSkipped
     */
    public function hasStopOnSkipped(): bool
    {
        return $this->stopOnSkipped !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnSkipped(): bool
    {
        if (!$this->hasStopOnSkipped()) {
            throw new Exception;
        }

        return $this->stopOnSkipped;
    }

    /**
     * @phpstan-assert-if-true !null $this->stopOnWarning
     */
    public function hasStopOnWarning(): bool
    {
        return $this->stopOnWarning !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnWarning(): bool
    {
        if (!$this->hasStopOnWarning()) {
            throw new Exception;
        }

        return $this->stopOnWarning;
    }

    /**
     * @phpstan-assert-if-true !null $this->excludeFilter
     */
    public function hasExcludeFilter(): bool
    {
        return $this->excludeFilter !== null;
    }

    /**
     * @throws Exception
     */
    public function excludeFilter(): string
    {
        if (!$this->hasExcludeFilter()) {
            throw new Exception;
        }

        return $this->excludeFilter;
    }

    /**
     * @phpstan-assert-if-true !null $this->filter
     */
    public function hasFilter(): bool
    {
        return $this->filter !== null;
    }

    /**
     * @throws Exception
     */
    public function filter(): string
    {
        if (!$this->hasFilter()) {
            throw new Exception;
        }

        return $this->filter;
    }

    /**
     * @phpstan-assert-if-true !null $this->generateBaseline
     */
    public function hasGenerateBaseline(): bool
    {
        return $this->generateBaseline !== null;
    }

    /**
     * @throws Exception
     */
    public function generateBaseline(): string
    {
        if (!$this->hasGenerateBaseline()) {
            throw new Exception;
        }

        return $this->generateBaseline;
    }

    /**
     * @phpstan-assert-if-true !null $this->useBaseline
     */
    public function hasUseBaseline(): bool
    {
        return $this->useBaseline !== null;
    }

    /**
     * @throws Exception
     */
    public function useBaseline(): string
    {
        if (!$this->hasUseBaseline()) {
            throw new Exception;
        }

        return $this->useBaseline;
    }

    public function ignoreBaseline(): bool
    {
        return $this->ignoreBaseline;
    }

    public function generateConfiguration(): bool
    {
        return $this->generateConfiguration;
    }

    public function migrateConfiguration(): bool
    {
        return $this->migrateConfiguration;
    }

    /**
     * @phpstan-assert-if-true !null $this->groups
     */
    public function hasGroups(): bool
    {
        return $this->groups !== null;
    }

    /**
     * @throws Exception
     *
     * @return non-empty-list<non-empty-string>
     */
    public function groups(): array
    {
        if (!$this->hasGroups()) {
            throw new Exception;
        }

        return $this->groups;
    }

    /**
     * @phpstan-assert-if-true !null $this->testsCovering
     */
    public function hasTestsCovering(): bool
    {
        return $this->testsCovering !== null;
    }

    /**
     * @throws Exception
     *
     * @return non-empty-list<non-empty-string>
     */
    public function testsCovering(): array
    {
        if (!$this->hasTestsCovering()) {
            throw new Exception;
        }

        return $this->testsCovering;
    }

    /**
     * @phpstan-assert-if-true !null $this->testsUsing
     */
    public function hasTestsUsing(): bool
    {
        return $this->testsUsing !== null;
    }

    /**
     * @throws Exception
     *
     * @return non-empty-list<non-empty-string>
     */
    public function testsUsing(): array
    {
        if (!$this->hasTestsUsing()) {
            throw new Exception;
        }

        return $this->testsUsing;
    }

    /**
     * @phpstan-assert-if-true !null $this->testsRequiringPhpExtension
     */
    public function hasTestsRequiringPhpExtension(): bool
    {
        return $this->testsRequiringPhpExtension !== null;
    }

    /**
     * @throws Exception
     *
     * @return non-empty-list<non-empty-string>
     */
    public function testsRequiringPhpExtension(): array
    {
        if (!$this->hasTestsRequiringPhpExtension()) {
            throw new Exception;
        }

        return $this->testsRequiringPhpExtension;
    }

    public function help(): bool
    {
        return $this->help;
    }

    /**
     * @phpstan-assert-if-true !null $this->includePath
     */
    public function hasIncludePath(): bool
    {
        return $this->includePath !== null;
    }

    /**
     * @throws Exception
     */
    public function includePath(): string
    {
        if (!$this->hasIncludePath()) {
            throw new Exception;
        }

        return $this->includePath;
    }

    /**
     * @phpstan-assert-if-true !null $this->iniSettings
     */
    public function hasIniSettings(): bool
    {
        return $this->iniSettings !== null;
    }

    /**
     * @throws Exception
     *
     * @return non-empty-array<non-empty-string, non-empty-string>
     */
    public function iniSettings(): array
    {
        if (!$this->hasIniSettings()) {
            throw new Exception;
        }

        return $this->iniSettings;
    }

    /**
     * @phpstan-assert-if-true !null $this->junitLogfile
     */
    public function hasJunitLogfile(): bool
    {
        return $this->junitLogfile !== null;
    }

    /**
     * @throws Exception
     */
    public function junitLogfile(): string
    {
        if (!$this->hasJunitLogfile()) {
            throw new Exception;
        }

        return $this->junitLogfile;
    }

    public function listGroups(): bool
    {
        return $this->listGroups;
    }

    public function listSuites(): bool
    {
        return $this->listSuites;
    }

    public function listTestFiles(): bool
    {
        return $this->listTestFiles;
    }

    public function listTests(): bool
    {
        return $this->listTests;
    }

    /**
     * @phpstan-assert-if-true !null $this->listTestsXml
     */
    public function hasListTestsXml(): bool
    {
        return $this->listTestsXml !== null;
    }

    /**
     * @throws Exception
     */
    public function listTestsXml(): string
    {
        if (!$this->hasListTestsXml()) {
            throw new Exception;
        }

        return $this->listTestsXml;
    }

    /**
     * @phpstan-assert-if-true !null $this->noCoverage
     */
    public function hasNoCoverage(): bool
    {
        return $this->noCoverage !== null;
    }

    /**
     * @throws Exception
     */
    public function noCoverage(): bool
    {
        if (!$this->hasNoCoverage()) {
            throw new Exception;
        }

        return $this->noCoverage;
    }

    /**
     * @phpstan-assert-if-true !null $this->noExtensions
     */
    public function hasNoExtensions(): bool
    {
        return $this->noExtensions !== null;
    }

    /**
     * @throws Exception
     */
    public function noExtensions(): bool
    {
        if (!$this->hasNoExtensions()) {
            throw new Exception;
        }

        return $this->noExtensions;
    }

    /**
     * @phpstan-assert-if-true !null $this->noOutput
     */
    public function hasNoOutput(): bool
    {
        return $this->noOutput !== null;
    }

    /**
     * @throws Exception
     */
    public function noOutput(): bool
    {
        if ($this->noOutput === null) {
            throw new Exception;
        }

        return $this->noOutput;
    }

    /**
     * @phpstan-assert-if-true !null $this->noProgress
     */
    public function hasNoProgress(): bool
    {
        return $this->noProgress !== null;
    }

    /**
     * @throws Exception
     */
    public function noProgress(): bool
    {
        if ($this->noProgress === null) {
            throw new Exception;
        }

        return $this->noProgress;
    }

    /**
     * @phpstan-assert-if-true !null $this->noResults
     */
    public function hasNoResults(): bool
    {
        return $this->noResults !== null;
    }

    /**
     * @throws Exception
     */
    public function noResults(): bool
    {
        if ($this->noResults === null) {
            throw new Exception;
        }

        return $this->noResults;
    }

    /**
     * @phpstan-assert-if-true !null $this->noLogging
     */
    public function hasNoLogging(): bool
    {
        return $this->noLogging !== null;
    }

    /**
     * @throws Exception
     */
    public function noLogging(): bool
    {
        if (!$this->hasNoLogging()) {
            throw new Exception;
        }

        return $this->noLogging;
    }

    /**
     * @phpstan-assert-if-true !null $this->processIsolation
     */
    public function hasProcessIsolation(): bool
    {
        return $this->processIsolation !== null;
    }

    /**
     * @throws Exception
     */
    public function processIsolation(): bool
    {
        if (!$this->hasProcessIsolation()) {
            throw new Exception;
        }

        return $this->processIsolation;
    }

    /**
     * @phpstan-assert-if-true !null $this->randomOrderSeed
     */
    public function hasRandomOrderSeed(): bool
    {
        return $this->randomOrderSeed !== null;
    }

    /**
     * @throws Exception
     */
    public function randomOrderSeed(): int
    {
        if (!$this->hasRandomOrderSeed()) {
            throw new Exception;
        }

        return $this->randomOrderSeed;
    }

    /**
     * @phpstan-assert-if-true !null $this->reportUselessTests
     */
    public function hasReportUselessTests(): bool
    {
        return $this->reportUselessTests !== null;
    }

    /**
     * @throws Exception
     */
    public function reportUselessTests(): bool
    {
        if (!$this->hasReportUselessTests()) {
            throw new Exception;
        }

        return $this->reportUselessTests;
    }

    /**
     * @phpstan-assert-if-true !null $this->resolveDependencies
     */
    public function hasResolveDependencies(): bool
    {
        return $this->resolveDependencies !== null;
    }

    /**
     * @throws Exception
     */
    public function resolveDependencies(): bool
    {
        if (!$this->hasResolveDependencies()) {
            throw new Exception;
        }

        return $this->resolveDependencies;
    }

    /**
     * @phpstan-assert-if-true !null $this->reverseList
     */
    public function hasReverseList(): bool
    {
        return $this->reverseList !== null;
    }

    /**
     * @throws Exception
     */
    public function reverseList(): bool
    {
        if (!$this->hasReverseList()) {
            throw new Exception;
        }

        return $this->reverseList;
    }

    /**
     * @phpstan-assert-if-true !null $this->stderr
     */
    public function hasStderr(): bool
    {
        return $this->stderr !== null;
    }

    /**
     * @throws Exception
     */
    public function stderr(): bool
    {
        if (!$this->hasStderr()) {
            throw new Exception;
        }

        return $this->stderr;
    }

    /**
     * @phpstan-assert-if-true !null $this->strictCoverage
     */
    public function hasStrictCoverage(): bool
    {
        return $this->strictCoverage !== null;
    }

    /**
     * @throws Exception
     */
    public function strictCoverage(): bool
    {
        if (!$this->hasStrictCoverage()) {
            throw new Exception;
        }

        return $this->strictCoverage;
    }

    /**
     * @phpstan-assert-if-true !null $this->teamcityLogfile
     */
    public function hasTeamcityLogfile(): bool
    {
        return $this->teamcityLogfile !== null;
    }

    /**
     * @throws Exception
     */
    public function teamcityLogfile(): string
    {
        if (!$this->hasTeamcityLogfile()) {
            throw new Exception;
        }

        return $this->teamcityLogfile;
    }

    /**
     * @phpstan-assert-if-true !null $this->teamCityPrinter
     */
    public function hasTeamCityPrinter(): bool
    {
        return $this->teamCityPrinter !== null;
    }

    /**
     * @throws Exception
     */
    public function teamCityPrinter(): bool
    {
        if (!$this->hasTeamCityPrinter()) {
            throw new Exception;
        }

        return $this->teamCityPrinter;
    }

    /**
     * @phpstan-assert-if-true !null $this->testdoxHtmlFile
     */
    public function hasTestdoxHtmlFile(): bool
    {
        return $this->testdoxHtmlFile !== null;
    }

    /**
     * @throws Exception
     */
    public function testdoxHtmlFile(): string
    {
        if (!$this->hasTestdoxHtmlFile()) {
            throw new Exception;
        }

        return $this->testdoxHtmlFile;
    }

    /**
     * @phpstan-assert-if-true !null $this->testdoxTextFile
     */
    public function hasTestdoxTextFile(): bool
    {
        return $this->testdoxTextFile !== null;
    }

    /**
     * @throws Exception
     */
    public function testdoxTextFile(): string
    {
        if (!$this->hasTestdoxTextFile()) {
            throw new Exception;
        }

        return $this->testdoxTextFile;
    }

    /**
     * @phpstan-assert-if-true !null $this->testdoxPrinter
     */
    public function hasTestDoxPrinter(): bool
    {
        return $this->testdoxPrinter !== null;
    }

    /**
     * @throws Exception
     */
    public function testdoxPrinter(): bool
    {
        if (!$this->hasTestDoxPrinter()) {
            throw new Exception;
        }

        return $this->testdoxPrinter;
    }

    /**
     * @phpstan-assert-if-true !null $this->testdoxPrinterSummary
     */
    public function hasTestDoxPrinterSummary(): bool
    {
        return $this->testdoxPrinterSummary !== null;
    }

    /**
     * @throws Exception
     */
    public function testdoxPrinterSummary(): bool
    {
        if (!$this->hasTestDoxPrinterSummary()) {
            throw new Exception;
        }

        return $this->testdoxPrinterSummary;
    }

    /**
     * @phpstan-assert-if-true !null $this->testSuffixes
     */
    public function hasTestSuffixes(): bool
    {
        return $this->testSuffixes !== null;
    }

    /**
     * @throws Exception
     *
     * @return non-empty-list<non-empty-string>
     */
    public function testSuffixes(): array
    {
        if (!$this->hasTestSuffixes()) {
            throw new Exception;
        }

        return $this->testSuffixes;
    }

    /**
     * @phpstan-assert-if-true !null $this->testSuite
     */
    public function hasTestSuite(): bool
    {
        return $this->testSuite !== null;
    }

    /**
     * @throws Exception
     */
    public function testSuite(): string
    {
        if (!$this->hasTestSuite()) {
            throw new Exception;
        }

        return $this->testSuite;
    }

    /**
     * @phpstan-assert-if-true !null $this->excludeTestSuite
     */
    public function hasExcludedTestSuite(): bool
    {
        return $this->excludeTestSuite !== null;
    }

    /**
     * @throws Exception
     */
    public function excludedTestSuite(): string
    {
        if (!$this->hasExcludedTestSuite()) {
            throw new Exception;
        }

        return $this->excludeTestSuite;
    }

    public function useDefaultConfiguration(): bool
    {
        return $this->useDefaultConfiguration;
    }

    /**
     * @phpstan-assert-if-true !null $this->displayDetailsOnAllIssues
     */
    public function hasDisplayDetailsOnAllIssues(): bool
    {
        return $this->displayDetailsOnAllIssues !== null;
    }

    /**
     * @throws Exception
     */
    public function displayDetailsOnAllIssues(): bool
    {
        if (!$this->hasDisplayDetailsOnAllIssues()) {
            throw new Exception;
        }

        return $this->displayDetailsOnAllIssues;
    }

    /**
     * @phpstan-assert-if-true !null $this->displayDetailsOnIncompleteTests
     */
    public function hasDisplayDetailsOnIncompleteTests(): bool
    {
        return $this->displayDetailsOnIncompleteTests !== null;
    }

    /**
     * @throws Exception
     */
    public function displayDetailsOnIncompleteTests(): bool
    {
        if (!$this->hasDisplayDetailsOnIncompleteTests()) {
            throw new Exception;
        }

        return $this->displayDetailsOnIncompleteTests;
    }

    /**
     * @phpstan-assert-if-true !null $this->displayDetailsOnSkippedTests
     */
    public function hasDisplayDetailsOnSkippedTests(): bool
    {
        return $this->displayDetailsOnSkippedTests !== null;
    }

    /**
     * @throws Exception
     */
    public function displayDetailsOnSkippedTests(): bool
    {
        if (!$this->hasDisplayDetailsOnSkippedTests()) {
            throw new Exception;
        }

        return $this->displayDetailsOnSkippedTests;
    }

    /**
     * @phpstan-assert-if-true !null $this->displayDetailsOnTestsThatTriggerDeprecations
     */
    public function hasDisplayDetailsOnTestsThatTriggerDeprecations(): bool
    {
        return $this->displayDetailsOnTestsThatTriggerDeprecations !== null;
    }

    /**
     * @throws Exception
     */
    public function displayDetailsOnTestsThatTriggerDeprecations(): bool
    {
        if (!$this->hasDisplayDetailsOnTestsThatTriggerDeprecations()) {
            throw new Exception;
        }

        return $this->displayDetailsOnTestsThatTriggerDeprecations;
    }

    /**
     * @phpstan-assert-if-true !null $this->displayDetailsOnPhpunitDeprecations
     */
    public function hasDisplayDetailsOnPhpunitDeprecations(): bool
    {
        return $this->displayDetailsOnPhpunitDeprecations !== null;
    }

    /**
     * @throws Exception
     */
    public function displayDetailsOnPhpunitDeprecations(): bool
    {
        if (!$this->hasDisplayDetailsOnPhpunitDeprecations()) {
            throw new Exception;
        }

        return $this->displayDetailsOnPhpunitDeprecations;
    }

    /**
     * @phpstan-assert-if-true !null $this->displayDetailsOnTestsThatTriggerErrors
     */
    public function hasDisplayDetailsOnTestsThatTriggerErrors(): bool
    {
        return $this->displayDetailsOnTestsThatTriggerErrors !== null;
    }

    /**
     * @throws Exception
     */
    public function displayDetailsOnTestsThatTriggerErrors(): bool
    {
        if (!$this->hasDisplayDetailsOnTestsThatTriggerErrors()) {
            throw new Exception;
        }

        return $this->displayDetailsOnTestsThatTriggerErrors;
    }

    /**
     * @phpstan-assert-if-true !null $this->displayDetailsOnTestsThatTriggerNotices
     */
    public function hasDisplayDetailsOnTestsThatTriggerNotices(): bool
    {
        return $this->displayDetailsOnTestsThatTriggerNotices !== null;
    }

    /**
     * @throws Exception
     */
    public function displayDetailsOnTestsThatTriggerNotices(): bool
    {
        if (!$this->hasDisplayDetailsOnTestsThatTriggerNotices()) {
            throw new Exception;
        }

        return $this->displayDetailsOnTestsThatTriggerNotices;
    }

    /**
     * @phpstan-assert-if-true !null $this->displayDetailsOnTestsThatTriggerWarnings
     */
    public function hasDisplayDetailsOnTestsThatTriggerWarnings(): bool
    {
        return $this->displayDetailsOnTestsThatTriggerWarnings !== null;
    }

    /**
     * @throws Exception
     */
    public function displayDetailsOnTestsThatTriggerWarnings(): bool
    {
        if (!$this->hasDisplayDetailsOnTestsThatTriggerWarnings()) {
            throw new Exception;
        }

        return $this->displayDetailsOnTestsThatTriggerWarnings;
    }

    public function version(): bool
    {
        return $this->version;
    }

    /**
     * @phpstan-assert-if-true !null $this->logEventsText
     */
    public function hasLogEventsText(): bool
    {
        return $this->logEventsText !== null;
    }

    /**
     * @throws Exception
     */
    public function logEventsText(): string
    {
        if (!$this->hasLogEventsText()) {
            throw new Exception;
        }

        return $this->logEventsText;
    }

    /**
     * @phpstan-assert-if-true !null $this->logEventsVerboseText
     */
    public function hasLogEventsVerboseText(): bool
    {
        return $this->logEventsVerboseText !== null;
    }

    /**
     * @throws Exception
     */
    public function logEventsVerboseText(): string
    {
        if (!$this->hasLogEventsVerboseText()) {
            throw new Exception;
        }

        return $this->logEventsVerboseText;
    }

    public function debug(): bool
    {
        return $this->debug;
    }

    /**
     * @phpstan-assert-if-true !null $this->extensions
     */
    public function hasExtensions(): bool
    {
        return $this->extensions !== null;
    }

    /**
     * @throws Exception
     *
     * @return non-empty-list<non-empty-string>
     */
    public function extensions(): array
    {
        if (!$this->hasExtensions()) {
            throw new Exception;
        }

        return $this->extensions;
    }
}
