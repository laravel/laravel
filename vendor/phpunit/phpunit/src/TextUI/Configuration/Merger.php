<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Configuration;

use const DIRECTORY_SEPARATOR;
use const PATH_SEPARATOR;
use function array_diff;
use function assert;
use function dirname;
use function explode;
use function is_int;
use function realpath;
use function time;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Runner\TestSuiteSorter;
use PHPUnit\TextUI\CliArguments\Configuration as CliConfiguration;
use PHPUnit\TextUI\CliArguments\Exception;
use PHPUnit\TextUI\XmlConfiguration\Configuration as XmlConfiguration;
use PHPUnit\TextUI\XmlConfiguration\LoadedFromFileConfiguration;
use PHPUnit\TextUI\XmlConfiguration\SchemaDetector;
use PHPUnit\Util\Filesystem;
use SebastianBergmann\CodeCoverage\Report\Html\Colors;
use SebastianBergmann\CodeCoverage\Report\Thresholds;
use SebastianBergmann\Environment\Console;
use SebastianBergmann\Invoker\Invoker;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Merger
{
    /**
     * @throws \PHPUnit\TextUI\XmlConfiguration\Exception
     * @throws Exception
     * @throws NoCustomCssFileException
     */
    public function merge(CliConfiguration $cliConfiguration, XmlConfiguration $xmlConfiguration): Configuration
    {
        $configurationFile = null;

        if ($xmlConfiguration->wasLoadedFromFile()) {
            assert($xmlConfiguration instanceof LoadedFromFileConfiguration);

            $configurationFile = $xmlConfiguration->filename();
        }

        $bootstrap = null;

        if ($cliConfiguration->hasBootstrap()) {
            $bootstrap = $cliConfiguration->bootstrap();
        } elseif ($xmlConfiguration->phpunit()->hasBootstrap()) {
            $bootstrap = $xmlConfiguration->phpunit()->bootstrap();
        }

        if ($cliConfiguration->hasCacheResult()) {
            $cacheResult = $cliConfiguration->cacheResult();
        } else {
            $cacheResult = $xmlConfiguration->phpunit()->cacheResult();
        }

        $cacheDirectory         = null;
        $coverageCacheDirectory = null;

        if ($cliConfiguration->hasCacheDirectory() && Filesystem::createDirectory($cliConfiguration->cacheDirectory())) {
            $cacheDirectory = realpath($cliConfiguration->cacheDirectory());
        } elseif ($xmlConfiguration->phpunit()->hasCacheDirectory() && Filesystem::createDirectory($xmlConfiguration->phpunit()->cacheDirectory())) {
            $cacheDirectory = realpath($xmlConfiguration->phpunit()->cacheDirectory());
        }

        if ($cacheDirectory !== null) {
            $coverageCacheDirectory = $cacheDirectory . DIRECTORY_SEPARATOR . 'code-coverage';
            $testResultCacheFile    = $cacheDirectory . DIRECTORY_SEPARATOR . 'test-results';
        }

        if (!isset($testResultCacheFile)) {
            if ($xmlConfiguration->wasLoadedFromFile()) {
                $testResultCacheFile = dirname(realpath($xmlConfiguration->filename())) . DIRECTORY_SEPARATOR . '.phpunit.result.cache';
            } else {
                $candidate = realpath($_SERVER['PHP_SELF']);

                if ($candidate) {
                    $testResultCacheFile = dirname($candidate) . DIRECTORY_SEPARATOR . '.phpunit.result.cache';
                } else {
                    $testResultCacheFile = '.phpunit.result.cache';
                }
            }
        }

        if ($cliConfiguration->hasDisableCodeCoverageIgnore()) {
            $disableCodeCoverageIgnore = $cliConfiguration->disableCodeCoverageIgnore();
        } else {
            $disableCodeCoverageIgnore = $xmlConfiguration->codeCoverage()->disableCodeCoverageIgnore();
        }

        if ($cliConfiguration->hasFailOnAllIssues()) {
            $failOnAllIssues = $cliConfiguration->failOnAllIssues();
        } else {
            $failOnAllIssues = $xmlConfiguration->phpunit()->failOnAllIssues();
        }

        if ($cliConfiguration->hasFailOnDeprecation()) {
            $failOnDeprecation = $cliConfiguration->failOnDeprecation();
        } else {
            $failOnDeprecation = $xmlConfiguration->phpunit()->failOnDeprecation();
        }

        if ($cliConfiguration->hasFailOnPhpunitDeprecation()) {
            $failOnPhpunitDeprecation = $cliConfiguration->failOnPhpunitDeprecation();
        } else {
            $failOnPhpunitDeprecation = $xmlConfiguration->phpunit()->failOnPhpunitDeprecation();
        }

        if ($cliConfiguration->hasFailOnPhpunitWarning()) {
            $failOnPhpunitWarning = $cliConfiguration->failOnPhpunitWarning();
        } else {
            $failOnPhpunitWarning = $xmlConfiguration->phpunit()->failOnPhpunitWarning();
        }

        if ($cliConfiguration->hasFailOnEmptyTestSuite()) {
            $failOnEmptyTestSuite = $cliConfiguration->failOnEmptyTestSuite();
        } else {
            $failOnEmptyTestSuite = $xmlConfiguration->phpunit()->failOnEmptyTestSuite();
        }

        if ($cliConfiguration->hasFailOnIncomplete()) {
            $failOnIncomplete = $cliConfiguration->failOnIncomplete();
        } else {
            $failOnIncomplete = $xmlConfiguration->phpunit()->failOnIncomplete();
        }

        if ($cliConfiguration->hasFailOnNotice()) {
            $failOnNotice = $cliConfiguration->failOnNotice();
        } else {
            $failOnNotice = $xmlConfiguration->phpunit()->failOnNotice();
        }

        if ($cliConfiguration->hasFailOnRisky()) {
            $failOnRisky = $cliConfiguration->failOnRisky();
        } else {
            $failOnRisky = $xmlConfiguration->phpunit()->failOnRisky();
        }

        if ($cliConfiguration->hasFailOnSkipped()) {
            $failOnSkipped = $cliConfiguration->failOnSkipped();
        } else {
            $failOnSkipped = $xmlConfiguration->phpunit()->failOnSkipped();
        }

        if ($cliConfiguration->hasFailOnWarning()) {
            $failOnWarning = $cliConfiguration->failOnWarning();
        } else {
            $failOnWarning = $xmlConfiguration->phpunit()->failOnWarning();
        }

        $doNotFailOnDeprecation = false;

        if ($cliConfiguration->hasDoNotFailOnDeprecation()) {
            $doNotFailOnDeprecation = $cliConfiguration->doNotFailOnDeprecation();
        }

        $doNotFailOnPhpunitDeprecation = false;

        if ($cliConfiguration->hasDoNotFailOnPhpunitDeprecation()) {
            $doNotFailOnPhpunitDeprecation = $cliConfiguration->doNotFailOnPhpunitDeprecation();
        }

        $doNotFailOnPhpunitWarning = false;

        if ($cliConfiguration->hasDoNotFailOnPhpunitWarning()) {
            $doNotFailOnPhpunitWarning = $cliConfiguration->doNotFailOnPhpunitWarning();
        }

        $doNotFailOnEmptyTestSuite = false;

        if ($cliConfiguration->hasDoNotFailOnEmptyTestSuite()) {
            $doNotFailOnEmptyTestSuite = $cliConfiguration->doNotFailOnEmptyTestSuite();
        }

        $doNotFailOnIncomplete = false;

        if ($cliConfiguration->hasDoNotFailOnIncomplete()) {
            $doNotFailOnIncomplete = $cliConfiguration->doNotFailOnIncomplete();
        }

        $doNotFailOnNotice = false;

        if ($cliConfiguration->hasDoNotFailOnNotice()) {
            $doNotFailOnNotice = $cliConfiguration->doNotFailOnNotice();
        }

        $doNotFailOnRisky = false;

        if ($cliConfiguration->hasDoNotFailOnRisky()) {
            $doNotFailOnRisky = $cliConfiguration->doNotFailOnRisky();
        }

        $doNotFailOnSkipped = false;

        if ($cliConfiguration->hasDoNotFailOnSkipped()) {
            $doNotFailOnSkipped = $cliConfiguration->doNotFailOnSkipped();
        }

        $doNotFailOnWarning = false;

        if ($cliConfiguration->hasDoNotFailOnWarning()) {
            $doNotFailOnWarning = $cliConfiguration->doNotFailOnWarning();
        }

        if ($cliConfiguration->hasStopOnDefect()) {
            $stopOnDefect = $cliConfiguration->stopOnDefect();
        } else {
            $stopOnDefect = $xmlConfiguration->phpunit()->stopOnDefect();
        }

        if ($cliConfiguration->hasStopOnDeprecation()) {
            $stopOnDeprecation = $cliConfiguration->stopOnDeprecation();
        } else {
            $stopOnDeprecation = $xmlConfiguration->phpunit()->stopOnDeprecation();
        }

        $specificDeprecationToStopOn = null;

        if ($cliConfiguration->hasSpecificDeprecationToStopOn()) {
            $specificDeprecationToStopOn = $cliConfiguration->specificDeprecationToStopOn();
        }

        if ($cliConfiguration->hasStopOnError()) {
            $stopOnError = $cliConfiguration->stopOnError();
        } else {
            $stopOnError = $xmlConfiguration->phpunit()->stopOnError();
        }

        if ($cliConfiguration->hasStopOnFailure()) {
            $stopOnFailure = $cliConfiguration->stopOnFailure();
        } else {
            $stopOnFailure = $xmlConfiguration->phpunit()->stopOnFailure();
        }

        if ($cliConfiguration->hasStopOnIncomplete()) {
            $stopOnIncomplete = $cliConfiguration->stopOnIncomplete();
        } else {
            $stopOnIncomplete = $xmlConfiguration->phpunit()->stopOnIncomplete();
        }

        if ($cliConfiguration->hasStopOnNotice()) {
            $stopOnNotice = $cliConfiguration->stopOnNotice();
        } else {
            $stopOnNotice = $xmlConfiguration->phpunit()->stopOnNotice();
        }

        if ($cliConfiguration->hasStopOnRisky()) {
            $stopOnRisky = $cliConfiguration->stopOnRisky();
        } else {
            $stopOnRisky = $xmlConfiguration->phpunit()->stopOnRisky();
        }

        if ($cliConfiguration->hasStopOnSkipped()) {
            $stopOnSkipped = $cliConfiguration->stopOnSkipped();
        } else {
            $stopOnSkipped = $xmlConfiguration->phpunit()->stopOnSkipped();
        }

        if ($cliConfiguration->hasStopOnWarning()) {
            $stopOnWarning = $cliConfiguration->stopOnWarning();
        } else {
            $stopOnWarning = $xmlConfiguration->phpunit()->stopOnWarning();
        }

        if ($cliConfiguration->hasStderr() && $cliConfiguration->stderr()) {
            $outputToStandardErrorStream = true;
        } else {
            $outputToStandardErrorStream = $xmlConfiguration->phpunit()->stderr();
        }

        if ($cliConfiguration->hasColumns()) {
            $columns = $cliConfiguration->columns();
        } else {
            $columns = $xmlConfiguration->phpunit()->columns();
        }

        if ($columns === 'max') {
            $columns = (new Console)->getNumberOfColumns();
        }

        if ($columns < 16) {
            $columns = 16;

            EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
                'Less than 16 columns requested, number of columns set to 16',
            );
        }

        assert(is_int($columns));

        $noExtensions = false;

        if ($cliConfiguration->hasNoExtensions() && $cliConfiguration->noExtensions()) {
            $noExtensions = true;
        }

        $pharExtensionDirectory = null;

        if ($xmlConfiguration->phpunit()->hasExtensionsDirectory()) {
            $pharExtensionDirectory = $xmlConfiguration->phpunit()->extensionsDirectory();
        }

        $extensionBootstrappers = [];

        if ($cliConfiguration->hasExtensions()) {
            foreach ($cliConfiguration->extensions() as $extension) {
                $extensionBootstrappers[] = [
                    'className'  => $extension,
                    'parameters' => [],
                ];
            }
        }

        foreach ($xmlConfiguration->extensions() as $extension) {
            $extensionBootstrappers[] = [
                'className'  => $extension->className(),
                'parameters' => $extension->parameters(),
            ];
        }

        if ($cliConfiguration->hasPathCoverage() && $cliConfiguration->pathCoverage()) {
            $pathCoverage = $cliConfiguration->pathCoverage();
        } else {
            $pathCoverage = $xmlConfiguration->codeCoverage()->pathCoverage();
        }

        $defaultColors     = Colors::default();
        $defaultThresholds = Thresholds::default();

        $coverageClover                 = null;
        $coverageCobertura              = null;
        $coverageCrap4j                 = null;
        $coverageCrap4jThreshold        = 30;
        $coverageHtml                   = null;
        $coverageHtmlLowUpperBound      = $defaultThresholds->lowUpperBound();
        $coverageHtmlHighLowerBound     = $defaultThresholds->highLowerBound();
        $coverageHtmlColorSuccessLow    = $defaultColors->successLow();
        $coverageHtmlColorSuccessMedium = $defaultColors->successMedium();
        $coverageHtmlColorSuccessHigh   = $defaultColors->successHigh();
        $coverageHtmlColorWarning       = $defaultColors->warning();
        $coverageHtmlColorDanger        = $defaultColors->danger();
        $coverageHtmlCustomCssFile      = null;
        $coveragePhp                    = null;
        $coverageText                   = null;
        $coverageTextShowUncoveredFiles = false;
        $coverageTextShowOnlySummary    = false;
        $coverageXml                    = null;
        $coverageFromXmlConfiguration   = true;

        if ($cliConfiguration->hasNoCoverage() && $cliConfiguration->noCoverage()) {
            $coverageFromXmlConfiguration = false;
        }

        if ($cliConfiguration->hasCoverageClover()) {
            $coverageClover = $cliConfiguration->coverageClover();
        } elseif ($coverageFromXmlConfiguration && $xmlConfiguration->codeCoverage()->hasClover()) {
            $coverageClover = $xmlConfiguration->codeCoverage()->clover()->target()->path();
        }

        if ($cliConfiguration->hasCoverageCobertura()) {
            $coverageCobertura = $cliConfiguration->coverageCobertura();
        } elseif ($coverageFromXmlConfiguration && $xmlConfiguration->codeCoverage()->hasCobertura()) {
            $coverageCobertura = $xmlConfiguration->codeCoverage()->cobertura()->target()->path();
        }

        if ($xmlConfiguration->codeCoverage()->hasCrap4j()) {
            $coverageCrap4jThreshold = $xmlConfiguration->codeCoverage()->crap4j()->threshold();
        }

        if ($cliConfiguration->hasCoverageCrap4J()) {
            $coverageCrap4j = $cliConfiguration->coverageCrap4J();
        } elseif ($coverageFromXmlConfiguration && $xmlConfiguration->codeCoverage()->hasCrap4j()) {
            $coverageCrap4j = $xmlConfiguration->codeCoverage()->crap4j()->target()->path();
        }

        if ($xmlConfiguration->codeCoverage()->hasHtml()) {
            $coverageHtmlHighLowerBound = $xmlConfiguration->codeCoverage()->html()->highLowerBound();
            $coverageHtmlLowUpperBound  = $xmlConfiguration->codeCoverage()->html()->lowUpperBound();

            if ($coverageHtmlLowUpperBound > $coverageHtmlHighLowerBound) {
                $coverageHtmlLowUpperBound  = $defaultThresholds->lowUpperBound();
                $coverageHtmlHighLowerBound = $defaultThresholds->highLowerBound();
            }

            $coverageHtmlColorSuccessLow    = $xmlConfiguration->codeCoverage()->html()->colorSuccessLow();
            $coverageHtmlColorSuccessMedium = $xmlConfiguration->codeCoverage()->html()->colorSuccessMedium();
            $coverageHtmlColorSuccessHigh   = $xmlConfiguration->codeCoverage()->html()->colorSuccessHigh();
            $coverageHtmlColorWarning       = $xmlConfiguration->codeCoverage()->html()->colorWarning();
            $coverageHtmlColorDanger        = $xmlConfiguration->codeCoverage()->html()->colorDanger();

            if ($xmlConfiguration->codeCoverage()->html()->hasCustomCssFile()) {
                $coverageHtmlCustomCssFile = $xmlConfiguration->codeCoverage()->html()->customCssFile();
            }
        }

        if ($cliConfiguration->hasCoverageHtml()) {
            $coverageHtml = $cliConfiguration->coverageHtml();
        } elseif ($coverageFromXmlConfiguration && $xmlConfiguration->codeCoverage()->hasHtml()) {
            $coverageHtml = $xmlConfiguration->codeCoverage()->html()->target()->path();
        }

        if ($cliConfiguration->hasCoveragePhp()) {
            $coveragePhp = $cliConfiguration->coveragePhp();
        } elseif ($coverageFromXmlConfiguration && $xmlConfiguration->codeCoverage()->hasPhp()) {
            $coveragePhp = $xmlConfiguration->codeCoverage()->php()->target()->path();
        }

        if ($xmlConfiguration->codeCoverage()->hasText()) {
            $coverageTextShowUncoveredFiles = $xmlConfiguration->codeCoverage()->text()->showUncoveredFiles();
            $coverageTextShowOnlySummary    = $xmlConfiguration->codeCoverage()->text()->showOnlySummary();
        }

        if ($cliConfiguration->hasCoverageTextShowUncoveredFiles()) {
            $coverageTextShowUncoveredFiles = $cliConfiguration->coverageTextShowUncoveredFiles();
        }

        if ($cliConfiguration->hasCoverageTextShowOnlySummary()) {
            $coverageTextShowOnlySummary = $cliConfiguration->coverageTextShowOnlySummary();
        }

        if ($cliConfiguration->hasCoverageText()) {
            $coverageText = $cliConfiguration->coverageText();
        } elseif ($coverageFromXmlConfiguration && $xmlConfiguration->codeCoverage()->hasText()) {
            $coverageText = $xmlConfiguration->codeCoverage()->text()->target()->path();
        }

        if ($cliConfiguration->hasCoverageXml()) {
            $coverageXml = $cliConfiguration->coverageXml();
        } elseif ($coverageFromXmlConfiguration && $xmlConfiguration->codeCoverage()->hasXml()) {
            $coverageXml = $xmlConfiguration->codeCoverage()->xml()->target()->path();
        }

        if ($cliConfiguration->hasBackupGlobals()) {
            $backupGlobals = $cliConfiguration->backupGlobals();
        } else {
            $backupGlobals = $xmlConfiguration->phpunit()->backupGlobals();
        }

        if ($cliConfiguration->hasBackupStaticProperties()) {
            $backupStaticProperties = $cliConfiguration->backupStaticProperties();
        } else {
            $backupStaticProperties = $xmlConfiguration->phpunit()->backupStaticProperties();
        }

        if ($cliConfiguration->hasBeStrictAboutChangesToGlobalState()) {
            $beStrictAboutChangesToGlobalState = $cliConfiguration->beStrictAboutChangesToGlobalState();
        } else {
            $beStrictAboutChangesToGlobalState = $xmlConfiguration->phpunit()->beStrictAboutChangesToGlobalState();
        }

        if ($cliConfiguration->hasProcessIsolation()) {
            $processIsolation = $cliConfiguration->processIsolation();
        } else {
            $processIsolation = $xmlConfiguration->phpunit()->processIsolation();
        }

        if ($cliConfiguration->hasEnforceTimeLimit()) {
            $enforceTimeLimit = $cliConfiguration->enforceTimeLimit();
        } else {
            $enforceTimeLimit = $xmlConfiguration->phpunit()->enforceTimeLimit();
        }

        if ($enforceTimeLimit && !(new Invoker)->canInvokeWithTimeout()) {
            EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
                'The pcntl extension is required for enforcing time limits',
            );
        }

        if ($cliConfiguration->hasDefaultTimeLimit()) {
            $defaultTimeLimit = $cliConfiguration->defaultTimeLimit();
        } else {
            $defaultTimeLimit = $xmlConfiguration->phpunit()->defaultTimeLimit();
        }

        $timeoutForSmallTests  = $xmlConfiguration->phpunit()->timeoutForSmallTests();
        $timeoutForMediumTests = $xmlConfiguration->phpunit()->timeoutForMediumTests();
        $timeoutForLargeTests  = $xmlConfiguration->phpunit()->timeoutForLargeTests();

        if ($cliConfiguration->hasReportUselessTests()) {
            $reportUselessTests = $cliConfiguration->reportUselessTests();
        } else {
            $reportUselessTests = $xmlConfiguration->phpunit()->beStrictAboutTestsThatDoNotTestAnything();
        }

        if ($cliConfiguration->hasStrictCoverage()) {
            $strictCoverage = $cliConfiguration->strictCoverage();
        } else {
            $strictCoverage = $xmlConfiguration->phpunit()->beStrictAboutCoverageMetadata();
        }

        if ($cliConfiguration->hasDisallowTestOutput()) {
            $disallowTestOutput = $cliConfiguration->disallowTestOutput();
        } else {
            $disallowTestOutput = $xmlConfiguration->phpunit()->beStrictAboutOutputDuringTests();
        }

        if ($cliConfiguration->hasDisplayDetailsOnAllIssues()) {
            $displayDetailsOnAllIssues = $cliConfiguration->displayDetailsOnAllIssues();
        } else {
            $displayDetailsOnAllIssues = $xmlConfiguration->phpunit()->displayDetailsOnAllIssues();
        }

        if ($cliConfiguration->hasDisplayDetailsOnIncompleteTests()) {
            $displayDetailsOnIncompleteTests = $cliConfiguration->displayDetailsOnIncompleteTests();
        } else {
            $displayDetailsOnIncompleteTests = $xmlConfiguration->phpunit()->displayDetailsOnIncompleteTests();
        }

        if ($cliConfiguration->hasDisplayDetailsOnSkippedTests()) {
            $displayDetailsOnSkippedTests = $cliConfiguration->displayDetailsOnSkippedTests();
        } else {
            $displayDetailsOnSkippedTests = $xmlConfiguration->phpunit()->displayDetailsOnSkippedTests();
        }

        if ($cliConfiguration->hasDisplayDetailsOnTestsThatTriggerDeprecations()) {
            $displayDetailsOnTestsThatTriggerDeprecations = $cliConfiguration->displayDetailsOnTestsThatTriggerDeprecations();
        } else {
            $displayDetailsOnTestsThatTriggerDeprecations = $xmlConfiguration->phpunit()->displayDetailsOnTestsThatTriggerDeprecations();
        }

        if ($cliConfiguration->hasDisplayDetailsOnPhpunitDeprecations()) {
            $displayDetailsOnPhpunitDeprecations = $cliConfiguration->displayDetailsOnPhpunitDeprecations();
        } else {
            $displayDetailsOnPhpunitDeprecations = $xmlConfiguration->phpunit()->displayDetailsOnPhpunitDeprecations();
        }

        if ($cliConfiguration->hasDisplayDetailsOnTestsThatTriggerErrors()) {
            $displayDetailsOnTestsThatTriggerErrors = $cliConfiguration->displayDetailsOnTestsThatTriggerErrors();
        } else {
            $displayDetailsOnTestsThatTriggerErrors = $xmlConfiguration->phpunit()->displayDetailsOnTestsThatTriggerErrors();
        }

        if ($cliConfiguration->hasDisplayDetailsOnTestsThatTriggerNotices()) {
            $displayDetailsOnTestsThatTriggerNotices = $cliConfiguration->displayDetailsOnTestsThatTriggerNotices();
        } else {
            $displayDetailsOnTestsThatTriggerNotices = $xmlConfiguration->phpunit()->displayDetailsOnTestsThatTriggerNotices();
        }

        if ($cliConfiguration->hasDisplayDetailsOnTestsThatTriggerWarnings()) {
            $displayDetailsOnTestsThatTriggerWarnings = $cliConfiguration->displayDetailsOnTestsThatTriggerWarnings();
        } else {
            $displayDetailsOnTestsThatTriggerWarnings = $xmlConfiguration->phpunit()->displayDetailsOnTestsThatTriggerWarnings();
        }

        if ($cliConfiguration->hasReverseList()) {
            $reverseDefectList = $cliConfiguration->reverseList();
        } else {
            $reverseDefectList = $xmlConfiguration->phpunit()->reverseDefectList();
        }

        $requireCoverageMetadata = $xmlConfiguration->phpunit()->requireCoverageMetadata();

        if ($cliConfiguration->hasExecutionOrder()) {
            $executionOrder = $cliConfiguration->executionOrder();
        } else {
            $executionOrder = $xmlConfiguration->phpunit()->executionOrder();
        }

        $executionOrderDefects = TestSuiteSorter::ORDER_DEFAULT;

        if ($cliConfiguration->hasExecutionOrderDefects()) {
            $executionOrderDefects = $cliConfiguration->executionOrderDefects();
        } elseif ($xmlConfiguration->phpunit()->defectsFirst()) {
            $executionOrderDefects = TestSuiteSorter::ORDER_DEFECTS_FIRST;
        }

        if ($cliConfiguration->hasResolveDependencies()) {
            $resolveDependencies = $cliConfiguration->resolveDependencies();
        } else {
            $resolveDependencies = $xmlConfiguration->phpunit()->resolveDependencies();
        }

        $colors          = false;
        $colorsSupported = (new Console)->hasColorSupport();

        if ($cliConfiguration->hasColors()) {
            if ($cliConfiguration->colors() === Configuration::COLOR_ALWAYS) {
                $colors = true;
            } elseif ($colorsSupported && $cliConfiguration->colors() === Configuration::COLOR_AUTO) {
                $colors = true;
            }
        } elseif ($xmlConfiguration->phpunit()->colors() === Configuration::COLOR_ALWAYS) {
            $colors = true;
        } elseif ($colorsSupported && $xmlConfiguration->phpunit()->colors() === Configuration::COLOR_AUTO) {
            $colors = true;
        }

        $logfileTeamcity             = null;
        $logfileJunit                = null;
        $logfileTestdoxHtml          = null;
        $logfileTestdoxText          = null;
        $loggingFromXmlConfiguration = true;

        if ($cliConfiguration->hasNoLogging() && $cliConfiguration->noLogging()) {
            $loggingFromXmlConfiguration = false;
        }

        if ($cliConfiguration->hasTeamcityLogfile()) {
            $logfileTeamcity = $cliConfiguration->teamcityLogfile();
        } elseif ($loggingFromXmlConfiguration && $xmlConfiguration->logging()->hasTeamCity()) {
            $logfileTeamcity = $xmlConfiguration->logging()->teamCity()->target()->path();
        }

        if ($cliConfiguration->hasJunitLogfile()) {
            $logfileJunit = $cliConfiguration->junitLogfile();
        } elseif ($loggingFromXmlConfiguration && $xmlConfiguration->logging()->hasJunit()) {
            $logfileJunit = $xmlConfiguration->logging()->junit()->target()->path();
        }

        if ($cliConfiguration->hasTestdoxHtmlFile()) {
            $logfileTestdoxHtml = $cliConfiguration->testdoxHtmlFile();
        } elseif ($loggingFromXmlConfiguration && $xmlConfiguration->logging()->hasTestDoxHtml()) {
            $logfileTestdoxHtml = $xmlConfiguration->logging()->testDoxHtml()->target()->path();
        }

        if ($cliConfiguration->hasTestdoxTextFile()) {
            $logfileTestdoxText = $cliConfiguration->testdoxTextFile();
        } elseif ($loggingFromXmlConfiguration && $xmlConfiguration->logging()->hasTestDoxText()) {
            $logfileTestdoxText = $xmlConfiguration->logging()->testDoxText()->target()->path();
        }

        $logEventsText = null;

        if ($cliConfiguration->hasLogEventsText()) {
            $logEventsText = $cliConfiguration->logEventsText();
        }

        $logEventsVerboseText = null;

        if ($cliConfiguration->hasLogEventsVerboseText()) {
            $logEventsVerboseText = $cliConfiguration->logEventsVerboseText();
        }

        $teamCityOutput = false;

        if ($cliConfiguration->hasTeamCityPrinter() && $cliConfiguration->teamCityPrinter()) {
            $teamCityOutput = true;
        }

        if ($cliConfiguration->hasTestDoxPrinter() && $cliConfiguration->testdoxPrinter()) {
            $testDoxOutput = true;
        } else {
            $testDoxOutput = $xmlConfiguration->phpunit()->testdoxPrinter();
        }

        if ($cliConfiguration->hasTestDoxPrinterSummary() && $cliConfiguration->testdoxPrinterSummary()) {
            $testDoxOutputSummary = true;
        } else {
            $testDoxOutputSummary = $xmlConfiguration->phpunit()->testdoxPrinterSummary();
        }

        $noProgress = false;

        if ($cliConfiguration->hasNoProgress() && $cliConfiguration->noProgress()) {
            $noProgress = true;
        }

        $noResults = false;

        if ($cliConfiguration->hasNoResults() && $cliConfiguration->noResults()) {
            $noResults = true;
        }

        $noOutput = false;

        if ($cliConfiguration->hasNoOutput() && $cliConfiguration->noOutput()) {
            $noOutput = true;
        }

        $testsCovering = null;

        if ($cliConfiguration->hasTestsCovering()) {
            $testsCovering = $cliConfiguration->testsCovering();
        }

        $testsUsing = null;

        if ($cliConfiguration->hasTestsUsing()) {
            $testsUsing = $cliConfiguration->testsUsing();
        }

        $testsRequiringPhpExtension = null;

        if ($cliConfiguration->hasTestsRequiringPhpExtension()) {
            $testsRequiringPhpExtension = $cliConfiguration->testsRequiringPhpExtension();
        }

        $filter = null;

        if ($cliConfiguration->hasFilter()) {
            $filter = $cliConfiguration->filter();
        }

        $excludeFilter = null;

        if ($cliConfiguration->hasExcludeFilter()) {
            $excludeFilter = $cliConfiguration->excludeFilter();
        }

        if ($cliConfiguration->hasGroups()) {
            $groups = $cliConfiguration->groups();
        } else {
            $groups = $xmlConfiguration->groups()->include()->asArrayOfStrings();
        }

        if ($cliConfiguration->hasExcludeGroups()) {
            $excludeGroups = $cliConfiguration->excludeGroups();
        } else {
            $excludeGroups = $xmlConfiguration->groups()->exclude()->asArrayOfStrings();
        }

        $excludeGroups = array_diff($excludeGroups, $groups);

        if ($cliConfiguration->hasRandomOrderSeed()) {
            $randomOrderSeed = $cliConfiguration->randomOrderSeed();
        } else {
            $randomOrderSeed = time();
        }

        if ($xmlConfiguration->wasLoadedFromFile() && $xmlConfiguration->hasValidationErrors()) {
            if ((new SchemaDetector)->detect($xmlConfiguration->filename())->detected()) {
                EventFacade::emitter()->testRunnerTriggeredPhpunitDeprecation(
                    'Your XML configuration validates against a deprecated schema. Migrate your XML configuration using "--migrate-configuration"!',
                );
            } else {
                EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
                    "Test results may not be as expected because the XML configuration file did not pass validation:\n" .
                    $xmlConfiguration->validationErrors(),
                );
            }
        }

        $includeUncoveredFiles = $xmlConfiguration->codeCoverage()->includeUncoveredFiles();

        $includePaths = [];

        if ($cliConfiguration->hasIncludePath()) {
            foreach (explode(PATH_SEPARATOR, $cliConfiguration->includePath()) as $includePath) {
                $includePaths[] = new Directory($includePath);
            }
        }

        foreach ($xmlConfiguration->php()->includePaths() as $includePath) {
            $includePaths[] = $includePath;
        }

        $iniSettings = [];

        if ($cliConfiguration->hasIniSettings()) {
            foreach ($cliConfiguration->iniSettings() as $name => $value) {
                $iniSettings[] = new IniSetting($name, $value);
            }
        }

        foreach ($xmlConfiguration->php()->iniSettings() as $iniSetting) {
            $iniSettings[] = $iniSetting;
        }

        $includeTestSuite = '';

        if ($cliConfiguration->hasTestSuite()) {
            $includeTestSuite = $cliConfiguration->testSuite();
        } elseif ($xmlConfiguration->phpunit()->hasDefaultTestSuite()) {
            $includeTestSuite = $xmlConfiguration->phpunit()->defaultTestSuite();
        }

        $excludeTestSuite = '';

        if ($cliConfiguration->hasExcludedTestSuite()) {
            $excludeTestSuite = $cliConfiguration->excludedTestSuite();
        }

        $testSuffixes = ['Test.php', '.phpt'];

        if ($cliConfiguration->hasTestSuffixes()) {
            $testSuffixes = $cliConfiguration->testSuffixes();
        }

        $sourceIncludeDirectories = [];

        if ($cliConfiguration->hasCoverageFilter()) {
            foreach ($cliConfiguration->coverageFilter() as $directory) {
                $sourceIncludeDirectories[] = new FilterDirectory($directory, '', '.php');
            }
        }

        foreach ($xmlConfiguration->source()->includeDirectories() as $directory) {
            $sourceIncludeDirectories[] = $directory;
        }

        $sourceIncludeFiles       = $xmlConfiguration->source()->includeFiles();
        $sourceExcludeDirectories = $xmlConfiguration->source()->excludeDirectories();
        $sourceExcludeFiles       = $xmlConfiguration->source()->excludeFiles();

        $useBaseline      = null;
        $generateBaseline = null;

        if (!$cliConfiguration->hasGenerateBaseline()) {
            if ($cliConfiguration->hasUseBaseline()) {
                $useBaseline = $cliConfiguration->useBaseline();
            } elseif ($xmlConfiguration->source()->hasBaseline()) {
                $useBaseline = $xmlConfiguration->source()->baseline();
            }
        } else {
            $generateBaseline = $cliConfiguration->generateBaseline();
        }

        assert($useBaseline !== '');
        assert($generateBaseline !== '');

        if ($failOnAllIssues) {
            $displayDetailsOnAllIssues = true;
        }

        if ($failOnDeprecation && !$doNotFailOnDeprecation) {
            $displayDetailsOnTestsThatTriggerDeprecations = true;
        }

        if ($failOnPhpunitDeprecation && !$doNotFailOnPhpunitDeprecation) {
            $displayDetailsOnPhpunitDeprecations = true;
        }

        if ($failOnNotice && !$doNotFailOnNotice) {
            $displayDetailsOnTestsThatTriggerNotices = true;
        }

        if ($failOnWarning && !$doNotFailOnWarning) {
            $displayDetailsOnTestsThatTriggerWarnings = true;
        }

        if ($failOnIncomplete && !$doNotFailOnIncomplete) {
            $displayDetailsOnIncompleteTests = true;
        }

        if ($failOnSkipped && !$doNotFailOnSkipped) {
            $displayDetailsOnSkippedTests = true;
        }

        $issueTriggerIdentificationNeeded = $xmlConfiguration->source()->ignoreSelfDeprecations() || $xmlConfiguration->source()->ignoreDirectDeprecations() || $xmlConfiguration->source()->ignoreIndirectDeprecations();

        if ($issueTriggerIdentificationNeeded && !$xmlConfiguration->source()->identifyIssueTrigger()) {
            EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
                'The identification of issue triggers is disabled. However, ignoring self-deprecations, direct deprecations, or indirect deprecations is requested.',
            );
        }

        return new Configuration(
            $cliConfiguration->arguments(),
            $configurationFile,
            $bootstrap,
            $cacheResult,
            $cacheDirectory,
            $coverageCacheDirectory,
            new Source(
                $useBaseline,
                $cliConfiguration->ignoreBaseline(),
                FilterDirectoryCollection::fromArray($sourceIncludeDirectories),
                $sourceIncludeFiles,
                $sourceExcludeDirectories,
                $sourceExcludeFiles,
                $xmlConfiguration->source()->restrictDeprecations(),
                $xmlConfiguration->source()->restrictNotices(),
                $xmlConfiguration->source()->restrictWarnings(),
                $xmlConfiguration->source()->ignoreSuppressionOfDeprecations(),
                $xmlConfiguration->source()->ignoreSuppressionOfPhpDeprecations(),
                $xmlConfiguration->source()->ignoreSuppressionOfErrors(),
                $xmlConfiguration->source()->ignoreSuppressionOfNotices(),
                $xmlConfiguration->source()->ignoreSuppressionOfPhpNotices(),
                $xmlConfiguration->source()->ignoreSuppressionOfWarnings(),
                $xmlConfiguration->source()->ignoreSuppressionOfPhpWarnings(),
                $xmlConfiguration->source()->deprecationTriggers(),
                $xmlConfiguration->source()->ignoreSelfDeprecations(),
                $xmlConfiguration->source()->ignoreDirectDeprecations(),
                $xmlConfiguration->source()->ignoreIndirectDeprecations(),
                $xmlConfiguration->source()->identifyIssueTrigger(),
            ),
            $testResultCacheFile,
            $coverageClover,
            $coverageCobertura,
            $coverageCrap4j,
            $coverageCrap4jThreshold,
            $coverageHtml,
            $coverageHtmlLowUpperBound,
            $coverageHtmlHighLowerBound,
            $coverageHtmlColorSuccessLow,
            $coverageHtmlColorSuccessMedium,
            $coverageHtmlColorSuccessHigh,
            $coverageHtmlColorWarning,
            $coverageHtmlColorDanger,
            $coverageHtmlCustomCssFile,
            $coveragePhp,
            $coverageText,
            $coverageTextShowUncoveredFiles,
            $coverageTextShowOnlySummary,
            $coverageXml,
            $pathCoverage,
            $xmlConfiguration->codeCoverage()->ignoreDeprecatedCodeUnits(),
            $disableCodeCoverageIgnore,
            $failOnAllIssues,
            $failOnDeprecation,
            $failOnPhpunitDeprecation,
            $failOnPhpunitWarning,
            $failOnEmptyTestSuite,
            $failOnIncomplete,
            $failOnNotice,
            $failOnRisky,
            $failOnSkipped,
            $failOnWarning,
            $doNotFailOnDeprecation,
            $doNotFailOnPhpunitDeprecation,
            $doNotFailOnPhpunitWarning,
            $doNotFailOnEmptyTestSuite,
            $doNotFailOnIncomplete,
            $doNotFailOnNotice,
            $doNotFailOnRisky,
            $doNotFailOnSkipped,
            $doNotFailOnWarning,
            $stopOnDefect,
            $stopOnDeprecation,
            $specificDeprecationToStopOn,
            $stopOnError,
            $stopOnFailure,
            $stopOnIncomplete,
            $stopOnNotice,
            $stopOnRisky,
            $stopOnSkipped,
            $stopOnWarning,
            $outputToStandardErrorStream,
            $columns,
            $noExtensions,
            $pharExtensionDirectory,
            $extensionBootstrappers,
            $backupGlobals,
            $backupStaticProperties,
            $beStrictAboutChangesToGlobalState,
            $colors,
            $processIsolation,
            $enforceTimeLimit,
            $defaultTimeLimit,
            $timeoutForSmallTests,
            $timeoutForMediumTests,
            $timeoutForLargeTests,
            $reportUselessTests,
            $strictCoverage,
            $disallowTestOutput,
            $displayDetailsOnAllIssues,
            $displayDetailsOnIncompleteTests,
            $displayDetailsOnSkippedTests,
            $displayDetailsOnTestsThatTriggerDeprecations,
            $displayDetailsOnPhpunitDeprecations,
            $displayDetailsOnTestsThatTriggerErrors,
            $displayDetailsOnTestsThatTriggerNotices,
            $displayDetailsOnTestsThatTriggerWarnings,
            $reverseDefectList,
            $requireCoverageMetadata,
            $noProgress,
            $noResults,
            $noOutput,
            $executionOrder,
            $executionOrderDefects,
            $resolveDependencies,
            $logfileTeamcity,
            $logfileJunit,
            $logfileTestdoxHtml,
            $logfileTestdoxText,
            $logEventsText,
            $logEventsVerboseText,
            $teamCityOutput,
            $testDoxOutput,
            $testDoxOutputSummary,
            $testsCovering,
            $testsUsing,
            $testsRequiringPhpExtension,
            $filter,
            $excludeFilter,
            $groups,
            $excludeGroups,
            $randomOrderSeed,
            $includeUncoveredFiles,
            $xmlConfiguration->testSuite(),
            $includeTestSuite,
            $excludeTestSuite,
            $xmlConfiguration->phpunit()->hasDefaultTestSuite() ? $xmlConfiguration->phpunit()->defaultTestSuite() : null,
            $testSuffixes,
            new Php(
                DirectoryCollection::fromArray($includePaths),
                IniSettingCollection::fromArray($iniSettings),
                $xmlConfiguration->php()->constants(),
                $xmlConfiguration->php()->globalVariables(),
                $xmlConfiguration->php()->envVariables(),
                $xmlConfiguration->php()->postVariables(),
                $xmlConfiguration->php()->getVariables(),
                $xmlConfiguration->php()->cookieVariables(),
                $xmlConfiguration->php()->serverVariables(),
                $xmlConfiguration->php()->filesVariables(),
                $xmlConfiguration->php()->requestVariables(),
            ),
            $xmlConfiguration->phpunit()->controlGarbageCollector(),
            $xmlConfiguration->phpunit()->numberOfTestsBeforeGarbageCollection(),
            $generateBaseline,
            $cliConfiguration->debug(),
            $xmlConfiguration->phpunit()->shortenArraysForExportThreshold(),
        );
    }
}
