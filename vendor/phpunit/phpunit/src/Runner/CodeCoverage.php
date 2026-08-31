<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner;

use function file_put_contents;
use function sprintf;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\Configuration\CodeCoverageFilterRegistry;
use PHPUnit\TextUI\Configuration\Configuration;
use PHPUnit\TextUI\Output\Printer;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use SebastianBergmann\CodeCoverage\Exception as CodeCoverageException;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report\Clover as CloverReport;
use SebastianBergmann\CodeCoverage\Report\Cobertura as CoberturaReport;
use SebastianBergmann\CodeCoverage\Report\Crap4j as Crap4jReport;
use SebastianBergmann\CodeCoverage\Report\Html\Colors;
use SebastianBergmann\CodeCoverage\Report\Html\CustomCssFile;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReport;
use SebastianBergmann\CodeCoverage\Report\PHP as PhpReport;
use SebastianBergmann\CodeCoverage\Report\Text as TextReport;
use SebastianBergmann\CodeCoverage\Report\Thresholds;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade as XmlReport;
use SebastianBergmann\CodeCoverage\Test\TestSize\TestSize;
use SebastianBergmann\CodeCoverage\Test\TestStatus\TestStatus;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Timer\NoActiveTimerException;
use SebastianBergmann\Timer\Timer;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @codeCoverageIgnore
 */
final class CodeCoverage
{
    private static ?self $instance                                      = null;
    private ?\SebastianBergmann\CodeCoverage\CodeCoverage $codeCoverage = null;

    /**
     * @phpstan-ignore property.internalClass
     */
    private ?Driver $driver  = null;
    private bool $collecting = false;
    private ?TestCase $test  = null;
    private ?Timer $timer    = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function init(Configuration $configuration, CodeCoverageFilterRegistry $codeCoverageFilterRegistry, bool $extensionRequiresCodeCoverageCollection): void
    {
        $codeCoverageFilterRegistry->init($configuration);

        if (!$configuration->hasCoverageReport() && !$extensionRequiresCodeCoverageCollection) {
            return;
        }

        $this->activate($codeCoverageFilterRegistry->get(), $configuration->pathCoverage());

        if (!$this->isActive()) {
            return;
        }

        if ($configuration->hasCoverageCacheDirectory()) {
            $this->codeCoverage()->cacheStaticAnalysis($configuration->coverageCacheDirectory());
        }

        $this->codeCoverage()->excludeSubclassesOfThisClassFromUnintentionallyCoveredCodeCheck(Comparator::class);

        if ($configuration->strictCoverage()) {
            $this->codeCoverage()->enableCheckForUnintentionallyCoveredCode();
        }

        if ($configuration->ignoreDeprecatedCodeUnitsFromCodeCoverage()) {
            $this->codeCoverage()->ignoreDeprecatedCode();
        } else {
            $this->codeCoverage()->doNotIgnoreDeprecatedCode();
        }

        if ($configuration->disableCodeCoverageIgnore()) {
            $this->codeCoverage()->disableAnnotationsForIgnoringCode();
        } else {
            $this->codeCoverage()->enableAnnotationsForIgnoringCode();
        }

        if ($configuration->includeUncoveredFiles()) {
            $this->codeCoverage()->includeUncoveredFiles();
        } else {
            $this->codeCoverage()->excludeUncoveredFiles();
        }

        if ($codeCoverageFilterRegistry->get()->isEmpty()) {
            if (!$codeCoverageFilterRegistry->configured()) {
                EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
                    'No filter is configured, code coverage will not be processed',
                );
            } else {
                EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
                    'Configured filter does not match any files, code coverage will not be processed',
                );
            }

            $this->deactivate();
        }
    }

    /**
     * @phpstan-assert-if-true !null $this->codeCoverage
     */
    public function isActive(): bool
    {
        return $this->codeCoverage !== null;
    }

    public function codeCoverage(): \SebastianBergmann\CodeCoverage\CodeCoverage
    {
        return $this->codeCoverage;
    }

    /**
     * @return non-empty-string
     */
    public function driverNameAndVersion(): string
    {
        return $this->driver->nameAndVersion();
    }

    public function start(TestCase $test): void
    {
        if ($this->collecting) {
            return;
        }

        $size = TestSize::unknown();

        if ($test->size()->isSmall()) {
            $size = TestSize::small();
        } elseif ($test->size()->isMedium()) {
            $size = TestSize::medium();
        } elseif ($test->size()->isLarge()) {
            $size = TestSize::large();
        }

        $this->test = $test;

        $this->codeCoverage->start(
            $test->valueObjectForEvents()->id(),
            $size,
        );

        $this->collecting = true;
    }

    /**
     * @param array<string,list<int>>|false $linesToBeCovered
     * @param array<string,list<int>>       $linesToBeUsed
     */
    public function stop(bool $append, array|false $linesToBeCovered = [], array $linesToBeUsed = []): void
    {
        if (!$this->collecting) {
            return;
        }

        $status = TestStatus::unknown();

        if ($this->test !== null) {
            if ($this->test->status()->isSuccess()) {
                $status = TestStatus::success();
            } else {
                $status = TestStatus::failure();
            }
        }

        /* @noinspection UnusedFunctionResultInspection */
        $this->codeCoverage->stop($append, $status, $linesToBeCovered, $linesToBeUsed);

        $this->test       = null;
        $this->collecting = false;
    }

    public function deactivate(): void
    {
        $this->driver       = null;
        $this->codeCoverage = null;
        $this->test         = null;
    }

    public function generateReports(Printer $printer, Configuration $configuration): void
    {
        if (!$this->isActive()) {
            return;
        }

        if ($configuration->hasCoveragePhp()) {
            $this->codeCoverageGenerationStart($printer, 'PHP');

            try {
                $writer = new PhpReport;
                $writer->process($this->codeCoverage(), $configuration->coveragePhp());

                $this->codeCoverageGenerationSucceeded($printer);

                unset($writer);
            } catch (CodeCoverageException $e) {
                $this->codeCoverageGenerationFailed($printer, $e);
            }
        }

        if ($configuration->hasCoverageClover()) {
            $this->codeCoverageGenerationStart($printer, 'Clover XML');

            try {
                $writer = new CloverReport;
                $writer->process($this->codeCoverage(), $configuration->coverageClover(), 'Clover Coverage');

                $this->codeCoverageGenerationSucceeded($printer);

                unset($writer);
            } catch (CodeCoverageException $e) {
                $this->codeCoverageGenerationFailed($printer, $e);
            }
        }

        if ($configuration->hasCoverageCobertura()) {
            $this->codeCoverageGenerationStart($printer, 'Cobertura XML');

            try {
                $writer = new CoberturaReport;
                $writer->process($this->codeCoverage(), $configuration->coverageCobertura());

                $this->codeCoverageGenerationSucceeded($printer);

                unset($writer);
            } catch (CodeCoverageException $e) {
                $this->codeCoverageGenerationFailed($printer, $e);
            }
        }

        if ($configuration->hasCoverageCrap4j()) {
            $this->codeCoverageGenerationStart($printer, 'Crap4J XML');

            try {
                $writer = new Crap4jReport($configuration->coverageCrap4jThreshold());
                $writer->process($this->codeCoverage(), $configuration->coverageCrap4j());

                $this->codeCoverageGenerationSucceeded($printer);

                unset($writer);
            } catch (CodeCoverageException $e) {
                $this->codeCoverageGenerationFailed($printer, $e);
            }
        }

        if ($configuration->hasCoverageHtml()) {
            $this->codeCoverageGenerationStart($printer, 'HTML');

            try {
                $customCssFile = CustomCssFile::default();

                if ($configuration->hasCoverageHtmlCustomCssFile()) {
                    $customCssFile = CustomCssFile::from($configuration->coverageHtmlCustomCssFile());
                }

                $writer = new HtmlReport(
                    sprintf(
                        ' and <a href="https://phpunit.de/">PHPUnit %s</a>',
                        Version::id(),
                    ),
                    Colors::from(
                        $configuration->coverageHtmlColorSuccessLow(),
                        $configuration->coverageHtmlColorSuccessMedium(),
                        $configuration->coverageHtmlColorSuccessHigh(),
                        $configuration->coverageHtmlColorWarning(),
                        $configuration->coverageHtmlColorDanger(),
                    ),
                    Thresholds::from(
                        $configuration->coverageHtmlLowUpperBound(),
                        $configuration->coverageHtmlHighLowerBound(),
                    ),
                    $customCssFile,
                );

                $writer->process($this->codeCoverage(), $configuration->coverageHtml());

                $this->codeCoverageGenerationSucceeded($printer);

                unset($writer);
            } catch (CodeCoverageException $e) {
                $this->codeCoverageGenerationFailed($printer, $e);
            }
        }

        if ($configuration->hasCoverageText()) {
            $processor = new TextReport(
                Thresholds::default(),
                $configuration->coverageTextShowUncoveredFiles(),
                $configuration->coverageTextShowOnlySummary(),
            );

            $textReport = $processor->process($this->codeCoverage(), $configuration->colors());

            if ($configuration->coverageText() === 'php://stdout') {
                if (!$configuration->noOutput() && !$configuration->debug()) {
                    $printer->print($textReport);
                }
            } else {
                file_put_contents($configuration->coverageText(), $textReport);
            }
        }

        if ($configuration->hasCoverageXml()) {
            $this->codeCoverageGenerationStart($printer, 'PHPUnit XML');

            try {
                $writer = new XmlReport(Version::id());
                $writer->process($this->codeCoverage(), $configuration->coverageXml());

                $this->codeCoverageGenerationSucceeded($printer);

                unset($writer);
            } catch (CodeCoverageException $e) {
                $this->codeCoverageGenerationFailed($printer, $e);
            }
        }
    }

    private function activate(Filter $filter, bool $pathCoverage): void
    {
        try {
            if ($pathCoverage) {
                $this->driver = (new Selector)->forLineAndPathCoverage($filter);
            } else {
                $this->driver = (new Selector)->forLineCoverage($filter);
            }

            $this->codeCoverage = new \SebastianBergmann\CodeCoverage\CodeCoverage(
                $this->driver,
                $filter,
            );
        } catch (CodeCoverageException $e) {
            EventFacade::emitter()->testRunnerTriggeredPhpunitWarning(
                $e->getMessage(),
            );
        }
    }

    private function codeCoverageGenerationStart(Printer $printer, string $format): void
    {
        $printer->print(
            sprintf(
                "\nGenerating code coverage report in %s format ... ",
                $format,
            ),
        );

        $this->timer()->start();
    }

    /**
     * @throws NoActiveTimerException
     */
    private function codeCoverageGenerationSucceeded(Printer $printer): void
    {
        $printer->print(
            sprintf(
                "done [%s]\n",
                $this->timer()->stop()->asString(),
            ),
        );
    }

    /**
     * @throws NoActiveTimerException
     */
    private function codeCoverageGenerationFailed(Printer $printer, CodeCoverageException $e): void
    {
        $printer->print(
            sprintf(
                "failed [%s]\n%s\n",
                $this->timer()->stop()->asString(),
                $e->getMessage(),
            ),
        );
    }

    private function timer(): Timer
    {
        if ($this->timer === null) {
            $this->timer = new Timer;
        }

        return $this->timer;
    }
}
