<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework;

use function assert;
use function trim;
use function unserialize;
use PHPUnit\Event\Code\TestMethodBuilder;
use PHPUnit\Event\Code\ThrowableBuilder;
use PHPUnit\Event\Emitter;
use PHPUnit\Event\Facade;
use PHPUnit\Runner\CodeCoverage;
use PHPUnit\TestRunner\TestResult\PassedTests;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ChildProcessResultProcessor
{
    private Facade $eventFacade;
    private Emitter $emitter;
    private PassedTests $passedTests;
    private CodeCoverage $codeCoverage;

    public function __construct(Facade $eventFacade, Emitter $emitter, PassedTests $passedTests, CodeCoverage $codeCoverage)
    {
        $this->eventFacade  = $eventFacade;
        $this->emitter      = $emitter;
        $this->passedTests  = $passedTests;
        $this->codeCoverage = $codeCoverage;
    }

    public function process(Test $test, string $serializedProcessResult, string $stderr): void
    {
        if (!empty($stderr)) {
            $exception = new Exception(trim($stderr));

            assert($test instanceof TestCase);

            $this->emitter->testErrored(
                TestMethodBuilder::fromTestCase($test),
                ThrowableBuilder::from($exception),
            );

            return;
        }

        $childResult = @unserialize($serializedProcessResult);

        if ($childResult === false) {
            $exception = new AssertionFailedError('Test was run in child process and ended unexpectedly');

            assert($test instanceof TestCase);

            $this->emitter->testErrored(
                TestMethodBuilder::fromTestCase($test),
                ThrowableBuilder::from($exception),
            );

            $this->emitter->testFinished(
                TestMethodBuilder::fromTestCase($test),
                0,
            );

            return;
        }

        $this->eventFacade->forward($childResult['events']);
        $this->passedTests->import($childResult['passedTests']);

        assert($test instanceof TestCase);

        $test->setResult($childResult['testResult']);
        $test->addToAssertionCount($childResult['numAssertions']);

        if (!$this->codeCoverage->isActive()) {
            return;
        }

        // @codeCoverageIgnoreStart
        if (!$childResult['codeCoverage'] instanceof \SebastianBergmann\CodeCoverage\CodeCoverage) {
            return;
        }

        CodeCoverage::instance()->codeCoverage()->merge(
            $childResult['codeCoverage'],
        );
        // @codeCoverageIgnoreEnd
    }
}
