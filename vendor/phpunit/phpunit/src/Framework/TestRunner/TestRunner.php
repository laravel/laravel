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

use const PHP_EOL;
use function assert;
use function extension_loaded;
use function sprintf;
use function xdebug_is_debugger_active;
use AssertionError;
use PHPUnit\Event\Facade;
use PHPUnit\Metadata\Api\CodeCoverage as CodeCoverageMetadataApi;
use PHPUnit\Metadata\Parser\Registry as MetadataRegistry;
use PHPUnit\Runner\CodeCoverage;
use PHPUnit\Runner\ErrorHandler;
use PHPUnit\Runner\Exception;
use PHPUnit\TextUI\Configuration\Configuration;
use PHPUnit\TextUI\Configuration\Registry as ConfigurationRegistry;
use SebastianBergmann\CodeCoverage\Exception as OriginalCodeCoverageException;
use SebastianBergmann\CodeCoverage\InvalidArgumentException;
use SebastianBergmann\CodeCoverage\UnintentionallyCoveredCodeException;
use SebastianBergmann\Invoker\Invoker;
use SebastianBergmann\Invoker\TimeoutException;
use Throwable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestRunner
{
    private ?bool $timeLimitCanBeEnforced = null;
    private readonly Configuration $configuration;

    public function __construct()
    {
        $this->configuration = ConfigurationRegistry::get();
    }

    /**
     * @throws CodeCoverageException
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws UnintentionallyCoveredCodeException
     */
    public function run(TestCase $test): void
    {
        Assert::resetCount();

        $codeCoverageMetadataApi = new CodeCoverageMetadataApi;

        $shouldCodeCoverageBeCollected = $codeCoverageMetadataApi->shouldCodeCoverageBeCollectedFor(
            $test::class,
            $test->name(),
        );

        $error      = false;
        $failure    = false;
        $incomplete = false;
        $risky      = false;
        $skipped    = false;

        if ($this->shouldErrorHandlerBeUsed($test)) {
            ErrorHandler::instance()->enable();
        }

        $collectCodeCoverage = CodeCoverage::instance()->isActive() &&
                               $shouldCodeCoverageBeCollected;

        if ($collectCodeCoverage) {
            CodeCoverage::instance()->start($test);
        }

        try {
            if ($this->canTimeLimitBeEnforced() &&
                $this->shouldTimeLimitBeEnforced($test)) {
                $risky = $this->runTestWithTimeout($test);
            } else {
                $test->runBare();
            }
        } catch (AssertionFailedError $e) {
            $failure = true;

            if ($e instanceof IncompleteTestError) {
                $incomplete = true;
            } elseif ($e instanceof SkippedTest) {
                $skipped = true;
            }
        } catch (AssertionError $e) {
            $test->addToAssertionCount(1);

            $failure = true;
            $frame   = $e->getTrace()[0];

            assert(isset($frame['file']));
            assert(isset($frame['line']));

            $e = new AssertionFailedError(
                sprintf(
                    '%s in %s:%s',
                    $e->getMessage(),
                    $frame['file'],
                    $frame['line'],
                ),
            );
        } catch (Throwable $e) {
            $error = true;
        }

        $test->addToAssertionCount(Assert::getCount());

        if ($this->configuration->reportUselessTests() &&
            !$test->doesNotPerformAssertions() &&
            $test->numberOfAssertionsPerformed() === 0) {
            $risky = true;
        }

        if (!$error && !$failure && !$incomplete && !$skipped && !$risky &&
            $this->configuration->requireCoverageMetadata() &&
            !$this->hasCoverageMetadata($test::class, $test->name())) {
            Facade::emitter()->testConsideredRisky(
                $test->valueObjectForEvents(),
                'This test does not define a code coverage target but is expected to do so',
            );

            $risky = true;
        }

        if ($collectCodeCoverage) {
            $append           = !$risky && !$incomplete && !$skipped;
            $linesToBeCovered = [];
            $linesToBeUsed    = [];

            if ($append) {
                try {
                    $linesToBeCovered = $codeCoverageMetadataApi->linesToBeCovered(
                        $test::class,
                        $test->name(),
                    );

                    $linesToBeUsed = $codeCoverageMetadataApi->linesToBeUsed(
                        $test::class,
                        $test->name(),
                    );
                } catch (InvalidCoversTargetException $cce) {
                    Facade::emitter()->testTriggeredPhpunitWarning(
                        $test->valueObjectForEvents(),
                        $cce->getMessage(),
                    );

                    $append = false;
                }
            }

            try {
                CodeCoverage::instance()->stop(
                    $append,
                    $linesToBeCovered,
                    $linesToBeUsed,
                );
            } catch (UnintentionallyCoveredCodeException $cce) {
                Facade::emitter()->testConsideredRisky(
                    $test->valueObjectForEvents(),
                    'This test executed code that is not listed as code to be covered or used:' .
                    PHP_EOL .
                    $cce->getMessage(),
                );
            } catch (OriginalCodeCoverageException $cce) {
                $error = true;

                $e = $e ?? $cce;
            }
        }

        ErrorHandler::instance()->disable();

        if (!$error &&
            !$incomplete &&
            !$skipped &&
            $this->configuration->reportUselessTests() &&
            !$test->doesNotPerformAssertions() &&
            $test->numberOfAssertionsPerformed() === 0) {
            Facade::emitter()->testConsideredRisky(
                $test->valueObjectForEvents(),
                'This test did not perform any assertions',
            );
        }

        if ($test->doesNotPerformAssertions() &&
            $test->numberOfAssertionsPerformed() > 0) {
            Facade::emitter()->testConsideredRisky(
                $test->valueObjectForEvents(),
                sprintf(
                    'This test is not expected to perform assertions but performed %d assertion%s',
                    $test->numberOfAssertionsPerformed(),
                    $test->numberOfAssertionsPerformed() > 1 ? 's' : '',
                ),
            );
        }

        if ($test->hasUnexpectedOutput()) {
            Facade::emitter()->testPrintedUnexpectedOutput($test->output());
        }

        if ($this->configuration->disallowTestOutput() && $test->hasUnexpectedOutput()) {
            Facade::emitter()->testConsideredRisky(
                $test->valueObjectForEvents(),
                sprintf(
                    'Test code or tested code printed unexpected output: %s',
                    $test->output(),
                ),
            );
        }

        if ($test->wasPrepared()) {
            Facade::emitter()->testFinished(
                $test->valueObjectForEvents(),
                $test->numberOfAssertionsPerformed(),
            );
        }
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    private function hasCoverageMetadata(string $className, string $methodName): bool
    {
        foreach (MetadataRegistry::parser()->forClassAndMethod($className, $methodName) as $metadata) {
            if ($metadata->isCovers()) {
                return true;
            }

            if ($metadata->isCoversClass()) {
                return true;
            }

            if ($metadata->isCoversTrait()) {
                return true;
            }

            if ($metadata->isCoversMethod()) {
                return true;
            }

            if ($metadata->isCoversFunction()) {
                return true;
            }

            if ($metadata->isCoversNothing()) {
                return true;
            }
        }

        return false;
    }

    private function canTimeLimitBeEnforced(): bool
    {
        if ($this->timeLimitCanBeEnforced !== null) {
            return $this->timeLimitCanBeEnforced;
        }

        $this->timeLimitCanBeEnforced = (new Invoker)->canInvokeWithTimeout();

        return $this->timeLimitCanBeEnforced;
    }

    private function shouldTimeLimitBeEnforced(TestCase $test): bool
    {
        if (!$this->configuration->enforceTimeLimit()) {
            return false;
        }

        if (!(($this->configuration->defaultTimeLimit() || $test->size()->isKnown()))) {
            return false;
        }

        if (extension_loaded('xdebug') && xdebug_is_debugger_active()) {
            return false;
        }

        return true;
    }

    /**
     * @throws Throwable
     */
    private function runTestWithTimeout(TestCase $test): bool
    {
        $_timeout = $this->configuration->defaultTimeLimit();
        $testSize = $test->size();

        if ($testSize->isSmall()) {
            $_timeout = $this->configuration->timeoutForSmallTests();
        } elseif ($testSize->isMedium()) {
            $_timeout = $this->configuration->timeoutForMediumTests();
        } elseif ($testSize->isLarge()) {
            $_timeout = $this->configuration->timeoutForLargeTests();
        }

        try {
            (new Invoker)->invoke([$test, 'runBare'], [], $_timeout);
        } catch (TimeoutException) {
            Facade::emitter()->testConsideredRisky(
                $test->valueObjectForEvents(),
                sprintf(
                    'This test was aborted after %d second%s',
                    $_timeout,
                    $_timeout !== 1 ? 's' : '',
                ),
            );

            return true;
        }

        return false;
    }

    private function shouldErrorHandlerBeUsed(TestCase $test): bool
    {
        if (MetadataRegistry::parser()->forMethod($test::class, $test->name())->isWithoutErrorHandler()->isNotEmpty()) {
            return false;
        }

        return true;
    }
}
