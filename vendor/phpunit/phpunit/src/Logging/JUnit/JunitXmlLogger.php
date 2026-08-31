<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Logging\JUnit;

use const PHP_EOL;
use function assert;
use function basename;
use function is_int;
use function sprintf;
use function str_replace;
use function trim;
use DOMDocument;
use DOMElement;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade;
use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\Telemetry\HRTime;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\PreparationStarted;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PrintedUnexpectedOutput;
use PHPUnit\Event\Test\Skipped;
use PHPUnit\Event\TestSuite\Started;
use PHPUnit\Event\UnknownSubscriberTypeException;
use PHPUnit\TextUI\Output\Printer;
use PHPUnit\Util\Xml;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class JunitXmlLogger
{
    private readonly Printer $printer;
    private DOMDocument $document;
    private DOMElement $root;

    /**
     * @var array<int, DOMElement>
     */
    private array $testSuites = [];

    /**
     * @var array<int, int>
     */
    private array $testSuiteTests = [0];

    /**
     * @var array<int, int>
     */
    private array $testSuiteAssertions = [0];

    /**
     * @var array<int, int>
     */
    private array $testSuiteErrors = [0];

    /**
     * @var array<int, int>
     */
    private array $testSuiteFailures = [0];

    /**
     * @var array<int, int>
     */
    private array $testSuiteSkipped = [0];

    /**
     * @var array<int, float>
     */
    private array $testSuiteTimes        = [0.0];
    private int $testSuiteLevel          = 0;
    private ?DOMElement $currentTestCase = null;
    private ?HRTime $time                = null;
    private bool $prepared               = false;
    private bool $preparationFailed      = false;
    private ?string $unexpectedOutput    = null;

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     */
    public function __construct(Printer $printer, Facade $facade)
    {
        $this->printer = $printer;

        $this->registerSubscribers($facade);
        $this->createDocument();
    }

    public function flush(): void
    {
        $this->printer->print($this->document->saveXML());

        $this->printer->flush();
    }

    public function testSuiteStarted(Started $event): void
    {
        $testSuite = $this->document->createElement('testsuite');
        $testSuite->setAttribute('name', $event->testSuite()->name());

        if ($event->testSuite()->isForTestClass()) {
            $testSuite->setAttribute('file', $event->testSuite()->file());
        }

        if ($this->testSuiteLevel > 0) {
            $this->testSuites[$this->testSuiteLevel]->appendChild($testSuite);
        } else {
            $this->root->appendChild($testSuite);
        }

        $this->testSuiteLevel++;
        $this->testSuites[$this->testSuiteLevel]          = $testSuite;
        $this->testSuiteTests[$this->testSuiteLevel]      = 0;
        $this->testSuiteAssertions[$this->testSuiteLevel] = 0;
        $this->testSuiteErrors[$this->testSuiteLevel]     = 0;
        $this->testSuiteFailures[$this->testSuiteLevel]   = 0;
        $this->testSuiteSkipped[$this->testSuiteLevel]    = 0;
        $this->testSuiteTimes[$this->testSuiteLevel]      = 0.0;
    }

    public function testSuiteFinished(): void
    {
        $this->testSuites[$this->testSuiteLevel]->setAttribute(
            'tests',
            (string) $this->testSuiteTests[$this->testSuiteLevel],
        );

        $this->testSuites[$this->testSuiteLevel]->setAttribute(
            'assertions',
            (string) $this->testSuiteAssertions[$this->testSuiteLevel],
        );

        $this->testSuites[$this->testSuiteLevel]->setAttribute(
            'errors',
            (string) $this->testSuiteErrors[$this->testSuiteLevel],
        );

        $this->testSuites[$this->testSuiteLevel]->setAttribute(
            'failures',
            (string) $this->testSuiteFailures[$this->testSuiteLevel],
        );

        $this->testSuites[$this->testSuiteLevel]->setAttribute(
            'skipped',
            (string) $this->testSuiteSkipped[$this->testSuiteLevel],
        );

        $this->testSuites[$this->testSuiteLevel]->setAttribute(
            'time',
            sprintf('%F', $this->testSuiteTimes[$this->testSuiteLevel]),
        );

        if ($this->testSuiteLevel > 1) {
            $this->testSuiteTests[$this->testSuiteLevel - 1]      += $this->testSuiteTests[$this->testSuiteLevel];
            $this->testSuiteAssertions[$this->testSuiteLevel - 1] += $this->testSuiteAssertions[$this->testSuiteLevel];
            $this->testSuiteErrors[$this->testSuiteLevel - 1]     += $this->testSuiteErrors[$this->testSuiteLevel];
            $this->testSuiteFailures[$this->testSuiteLevel - 1]   += $this->testSuiteFailures[$this->testSuiteLevel];
            $this->testSuiteSkipped[$this->testSuiteLevel - 1]    += $this->testSuiteSkipped[$this->testSuiteLevel];
            $this->testSuiteTimes[$this->testSuiteLevel - 1]      += $this->testSuiteTimes[$this->testSuiteLevel];
        }

        $this->testSuiteLevel--;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testPreparationStarted(PreparationStarted $event): void
    {
        $this->createTestCase($event);

        $this->preparationFailed = false;
    }

    public function testPreparationFailed(): void
    {
        $this->preparationFailed = true;
    }

    public function testPrepared(): void
    {
        $this->prepared = true;
    }

    public function testPrintedUnexpectedOutput(PrintedUnexpectedOutput $event): void
    {
        $this->unexpectedOutput = $event->output();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testFinished(Finished $event): void
    {
        if (!$this->prepared || $this->preparationFailed) {
            return;
        }

        $this->handleFinish($event->telemetryInfo(), $event->numberOfAssertionsPerformed());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testMarkedIncomplete(MarkedIncomplete $event): void
    {
        $this->handleIncompleteOrSkipped($event);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testSkipped(Skipped $event): void
    {
        $this->handleIncompleteOrSkipped($event);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testErrored(Errored $event): void
    {
        $this->handleFault($event, 'error');

        $this->testSuiteErrors[$this->testSuiteLevel]++;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testFailed(Failed $event): void
    {
        $this->handleFault($event, 'failure');

        $this->testSuiteFailures[$this->testSuiteLevel]++;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function handleFinish(Info $telemetryInfo, int $numberOfAssertionsPerformed): void
    {
        assert($this->currentTestCase !== null);
        assert($this->time !== null);

        $time = $telemetryInfo->time()->duration($this->time)->asFloat();

        $this->testSuiteAssertions[$this->testSuiteLevel] += $numberOfAssertionsPerformed;

        $this->currentTestCase->setAttribute(
            'assertions',
            (string) $numberOfAssertionsPerformed,
        );

        $this->currentTestCase->setAttribute(
            'time',
            sprintf('%F', $time),
        );

        if ($this->unexpectedOutput !== null) {
            $systemOut = $this->document->createElement(
                'system-out',
                Xml::prepareString($this->unexpectedOutput),
            );

            $this->currentTestCase->appendChild($systemOut);
        }

        $this->testSuites[$this->testSuiteLevel]->appendChild(
            $this->currentTestCase,
        );

        $this->testSuiteTests[$this->testSuiteLevel]++;
        $this->testSuiteTimes[$this->testSuiteLevel] += $time;

        $this->currentTestCase   = null;
        $this->time              = null;
        $this->preparationFailed = false;
        $this->prepared          = false;
        $this->unexpectedOutput  = null;
    }

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     */
    private function registerSubscribers(Facade $facade): void
    {
        $facade->registerSubscribers(
            new TestSuiteStartedSubscriber($this),
            new TestSuiteFinishedSubscriber($this),
            new TestPreparationStartedSubscriber($this),
            new TestPreparationFailedSubscriber($this),
            new TestPreparedSubscriber($this),
            new TestPrintedUnexpectedOutputSubscriber($this),
            new TestFinishedSubscriber($this),
            new TestErroredSubscriber($this),
            new TestFailedSubscriber($this),
            new TestMarkedIncompleteSubscriber($this),
            new TestSkippedSubscriber($this),
            new TestRunnerExecutionFinishedSubscriber($this),
        );
    }

    private function createDocument(): void
    {
        $this->document               = new DOMDocument('1.0', 'UTF-8');
        $this->document->formatOutput = true;

        $this->root = $this->document->createElement('testsuites');
        $this->document->appendChild($this->root);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function handleFault(Errored|Failed $event, string $type): void
    {
        if (!$this->prepared) {
            $this->createTestCase($event);
        }

        assert($this->currentTestCase !== null);

        $buffer = $this->testAsString($event->test());

        $throwable = $event->throwable();
        $buffer .= trim(
            $throwable->description() . PHP_EOL .
            $throwable->stackTrace(),
        );

        $fault = $this->document->createElement(
            $type,
            Xml::prepareString($buffer),
        );

        $fault->setAttribute('type', $throwable->className());

        $this->currentTestCase->appendChild($fault);

        if (!$this->prepared) {
            $this->handleFinish($event->telemetryInfo(), 0);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function handleIncompleteOrSkipped(MarkedIncomplete|Skipped $event): void
    {
        if (!$this->prepared) {
            $this->createTestCase($event);
        }

        assert($this->currentTestCase !== null);

        $skipped = $this->document->createElement('skipped');

        $this->currentTestCase->appendChild($skipped);

        $this->testSuiteSkipped[$this->testSuiteLevel]++;

        if (!$this->prepared) {
            $this->handleFinish($event->telemetryInfo(), 0);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function testAsString(Test $test): string
    {
        if ($test->isPhpt()) {
            return basename($test->file());
        }

        assert($test instanceof TestMethod);

        return sprintf(
            '%s::%s%s',
            $test->className(),
            $this->name($test),
            PHP_EOL,
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    private function name(Test $test): string
    {
        if ($test->isPhpt()) {
            return basename($test->file());
        }

        assert($test instanceof TestMethod);

        if (!$test->testData()->hasDataFromDataProvider()) {
            return $test->methodName();
        }

        $dataSetName = $test->testData()->dataFromDataProvider()->dataSetName();

        if (is_int($dataSetName)) {
            return sprintf(
                '%s with data set #%d',
                $test->methodName(),
                $dataSetName,
            );
        }

        return sprintf(
            '%s with data set "%s"',
            $test->methodName(),
            $dataSetName,
        );
    }

    /**
     * @throws InvalidArgumentException
     *
     * @phpstan-assert !null $this->currentTestCase
     */
    private function createTestCase(Errored|Failed|MarkedIncomplete|PreparationStarted|Prepared|Skipped $event): void
    {
        $testCase = $this->document->createElement('testcase');

        $test = $event->test();

        $testCase->setAttribute('name', $this->name($test));
        $testCase->setAttribute('file', $test->file());

        if ($test->isTestMethod()) {
            assert($test instanceof TestMethod);

            $testCase->setAttribute('line', (string) $test->line());
            $testCase->setAttribute('class', $test->className());
            $testCase->setAttribute('classname', str_replace('\\', '.', $test->className()));
        }

        $this->currentTestCase = $testCase;
        $this->time            = $event->telemetryInfo()->time();
    }
}
