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

use const DEBUG_BACKTRACE_IGNORE_ARGS;
use const E_COMPILE_ERROR;
use const E_COMPILE_WARNING;
use const E_CORE_ERROR;
use const E_CORE_WARNING;
use const E_DEPRECATED;
use const E_ERROR;
use const E_NOTICE;
use const E_PARSE;
use const E_RECOVERABLE_ERROR;
use const E_USER_DEPRECATED;
use const E_USER_ERROR;
use const E_USER_NOTICE;
use const E_USER_WARNING;
use const E_WARNING;
use function array_keys;
use function array_values;
use function assert;
use function debug_backtrace;
use function defined;
use function error_reporting;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;
use PHPUnit\Event;
use PHPUnit\Event\Code\IssueTrigger\Code;
use PHPUnit\Event\Code\IssueTrigger\IssueTrigger;
use PHPUnit\Event\Code\NoTestCaseObjectOnCallStackException;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Runner\Baseline\Baseline;
use PHPUnit\Runner\Baseline\Issue;
use PHPUnit\TextUI\Configuration\Registry as ConfigurationRegistry;
use PHPUnit\TextUI\Configuration\SourceFilter;
use PHPUnit\Util\ExcludeList;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ErrorHandler
{
    private const UNHANDLEABLE_LEVELS   = E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING;
    private const INSUPPRESSIBLE_LEVELS = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;
    private static ?self $instance      = null;
    private ?Baseline $baseline         = null;
    private ExcludeList $excludeList;
    private bool $enabled                     = false;
    private ?int $originalErrorReportingLevel = null;
    private readonly bool $identifyIssueTrigger;

    /**
     * @var ?array{functions: list<non-empty-string>, methods: list<array{className: class-string, methodName: non-empty-string}>}
     */
    private ?array $deprecationTriggers = null;

    public static function instance(): self
    {
        $source = ConfigurationRegistry::get()->source();

        $identifyIssueTrigger = true;

        if (!$source->identifyIssueTrigger()) {
            $identifyIssueTrigger = false;
        }

        if (!$source->notEmpty()) {
            $identifyIssueTrigger = false;
        }

        return self::$instance ?? self::$instance = new self($identifyIssueTrigger);
    }

    private function __construct(bool $identifyIssueTrigger)
    {
        $this->excludeList          = new ExcludeList;
        $this->identifyIssueTrigger = $identifyIssueTrigger;
    }

    /**
     * @throws NoTestCaseObjectOnCallStackException
     */
    public function __invoke(int $errorNumber, string $errorString, string $errorFile, int $errorLine): false
    {
        $suppressed = (error_reporting() & ~self::INSUPPRESSIBLE_LEVELS) === 0;

        if ($suppressed && $this->excludeList->isExcluded($errorFile)) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        /**
         * E_STRICT is deprecated since PHP 8.4.
         *
         * @see https://github.com/sebastianbergmann/phpunit/issues/5956
         */
        if (defined('E_STRICT') && $errorNumber === 2048) {
            // @codeCoverageIgnoreStart
            $errorNumber = E_NOTICE;
            // @codeCoverageIgnoreEnd
        }

        $test = Event\Code\TestMethodBuilder::fromCallStack();

        $ignoredByBaseline = $this->ignoredByBaseline($errorFile, $errorLine, $errorString);
        $ignoredByTest     = $test->metadata()->isIgnoreDeprecations()->isNotEmpty();

        switch ($errorNumber) {
            case E_NOTICE:
                Event\Facade::emitter()->testTriggeredPhpNotice(
                    $test,
                    $errorString,
                    $errorFile,
                    $errorLine,
                    $suppressed,
                    $ignoredByBaseline,
                );

                break;

            case E_USER_NOTICE:
                Event\Facade::emitter()->testTriggeredNotice(
                    $test,
                    $errorString,
                    $errorFile,
                    $errorLine,
                    $suppressed,
                    $ignoredByBaseline,
                );

                break;

            case E_WARNING:
                Event\Facade::emitter()->testTriggeredPhpWarning(
                    $test,
                    $errorString,
                    $errorFile,
                    $errorLine,
                    $suppressed,
                    $ignoredByBaseline,
                );

                break;

            case E_USER_WARNING:
                Event\Facade::emitter()->testTriggeredWarning(
                    $test,
                    $errorString,
                    $errorFile,
                    $errorLine,
                    $suppressed,
                    $ignoredByBaseline,
                );

                break;

            case E_DEPRECATED:
                Event\Facade::emitter()->testTriggeredPhpDeprecation(
                    $test,
                    $errorString,
                    $errorFile,
                    $errorLine,
                    $suppressed,
                    $ignoredByBaseline,
                    $ignoredByTest,
                    $this->trigger($test, false, $errorFile),
                );

                break;

            case E_USER_DEPRECATED:
                $deprecationFrame = $this->guessDeprecationFrame();

                Event\Facade::emitter()->testTriggeredDeprecation(
                    $test,
                    $errorString,
                    $deprecationFrame['file'] ?? $errorFile,
                    $deprecationFrame['line'] ?? $errorLine,
                    $suppressed,
                    $ignoredByBaseline,
                    $ignoredByTest,
                    $this->trigger($test, true),
                    $this->stackTrace(),
                );

                break;

            case E_USER_ERROR:
                Event\Facade::emitter()->testTriggeredError(
                    $test,
                    $errorString,
                    $errorFile,
                    $errorLine,
                    $suppressed,
                );

                throw new ErrorException('E_USER_ERROR was triggered');

            default:
                return false;
        }

        return false;
    }

    public function enable(): void
    {
        assert(!$this->enabled);

        $oldErrorHandler = set_error_handler($this);

        if ($oldErrorHandler !== null) {
            restore_error_handler();

            return;
        }

        $this->enabled                     = true;
        $this->originalErrorReportingLevel = error_reporting();

        error_reporting($this->originalErrorReportingLevel & self::UNHANDLEABLE_LEVELS);
    }

    public function disable(): void
    {
        if (!$this->enabled) {
            return;
        }

        restore_error_handler();

        error_reporting(error_reporting() | $this->originalErrorReportingLevel);

        $this->enabled                     = false;
        $this->originalErrorReportingLevel = null;
    }

    public function useBaseline(Baseline $baseline): void
    {
        $this->baseline = $baseline;
    }

    /**
     * @param array{functions: list<non-empty-string>, methods: list<array{className: class-string, methodName: non-empty-string}>} $deprecationTriggers
     */
    public function useDeprecationTriggers(array $deprecationTriggers): void
    {
        $this->deprecationTriggers = $deprecationTriggers;
    }

    /**
     * @param non-empty-string $file
     * @param positive-int     $line
     * @param non-empty-string $description
     */
    private function ignoredByBaseline(string $file, int $line, string $description): bool
    {
        if ($this->baseline === null) {
            return false;
        }

        return $this->baseline->has(Issue::from($file, $line, null, $description));
    }

    /**
     * @param null|non-empty-string $errorFile
     */
    private function trigger(TestMethod $test, bool $isUserland, ?string $errorFile = null): IssueTrigger
    {
        if (!$this->identifyIssueTrigger) {
            return IssueTrigger::from(null, null);
        }

        if (!$isUserland) {
            assert($errorFile !== null);

            return IssueTrigger::from(Code::PHP, $this->categorizeFile($errorFile, $test));
        }

        $trace = $this->filteredStackTrace();

        return $this->triggerForUserlandDeprecation($test, $trace);
    }

    /**
     * @param list<array{function: string, line?: int, file?: string, class?: class-string, type?: '->'|'::', args?: list<mixed>, object?: object}> $trace
     */
    private function triggerForUserlandDeprecation(TestMethod $test, array $trace): IssueTrigger
    {
        $callee = null;
        $caller = null;

        if (isset($trace[0]['file'])) {
            $callee = $this->categorizeFile($trace[0]['file'], $test);
        }

        if (isset($trace[1]['file'])) {
            $caller = $this->categorizeFile($trace[1]['file'], $test);
        }

        return IssueTrigger::from($callee, $caller);
    }

    /**
     * @param non-empty-string $file
     */
    private function categorizeFile(string $file, TestMethod $test): Code
    {
        if ($file === $test->file()) {
            return Code::Test;
        }

        if (SourceFilter::instance()->includes($file)) {
            return Code::FirstParty;
        }

        if ($this->excludeList->isExcluded($file)) {
            return Code::PHPUnit;
        }

        return Code::ThirdParty;
    }

    /**
     * @return list<array{function: string, line?: int, file?: string, class?: class-string, type?: '->'|'::', args?: list<mixed>, object?: object}>
     */
    private function filteredStackTrace(): array
    {
        $trace = $this->errorStackTrace();

        if ($this->deprecationTriggers === null) {
            return array_values($trace);
        }

        foreach (array_keys($trace) as $frame) {
            foreach ($this->deprecationTriggers['functions'] as $function) {
                if ($this->frameIsFunction($trace[$frame], $function)) {
                    unset($trace[$frame]);

                    continue 2;
                }
            }

            foreach ($this->deprecationTriggers['methods'] as $method) {
                if ($this->frameIsMethod($trace[$frame], $method)) {
                    unset($trace[$frame]);

                    continue 2;
                }
            }
        }

        return array_values($trace);
    }

    /**
     * @return ?array{function: string, line?: int, file?: string, class?: class-string, type?: '->'|'::', args?: list<mixed>, object?: object}
     */
    private function guessDeprecationFrame(): ?array
    {
        if ($this->deprecationTriggers === null) {
            return null;
        }

        $trace = $this->errorStackTrace();

        foreach ($trace as $frame) {
            foreach ($this->deprecationTriggers['functions'] as $function) {
                if ($this->frameIsFunction($frame, $function)) {
                    return $frame;
                }
            }

            foreach ($this->deprecationTriggers['methods'] as $method) {
                if ($this->frameIsMethod($frame, $method)) {
                    return $frame;
                }
            }
        }

        return null;
    }

    /**
     * @return list<array{function: string, line?: int, file?: string, class?: class-string, type?: '->'|'::', args?: list<mixed>, object?: object}>
     */
    private function errorStackTrace(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $i = 0;

        do {
            unset($trace[$i]);
        } while (self::class === ($trace[++$i]['class'] ?? null));

        return array_values($trace);
    }

    /**
     * @param array{function: string, line?: int, file?: string, class?: class-string, type?: '->'|'::', args?: list<mixed>, object?: object} $frame
     * @param non-empty-string                                                                                                                $function
     */
    private function frameIsFunction(array $frame, string $function): bool
    {
        return !isset($frame['class']) && isset($frame['function']) && $frame['function'] === $function;
    }

    /**
     * @param array{function: string, line?: int, file?: string, class?: class-string, type?: '->'|'::', args?: list<mixed>, object?: object} $frame
     * @param array{className: class-string, methodName: non-empty-string}                                                                    $method
     */
    private function frameIsMethod(array $frame, array $method): bool
    {
        return isset($frame['class']) &&
            $frame['class'] === $method['className'] &&
            isset($frame['function']) &&
            $frame['function'] === $method['methodName'];
    }

    /**
     * @return non-empty-string
     */
    private function stackTrace(): string
    {
        $buffer = '';

        foreach ($this->errorStackTrace() as $frame) {
            /**
             * @see https://github.com/sebastianbergmann/phpunit/issues/6043
             */
            if (!isset($frame['file'])) {
                continue;
            }

            if ($this->excludeList->isExcluded($frame['file'])) {
                continue;
            }

            $buffer .= sprintf(
                "%s:%s\n",
                $frame['file'],
                $frame['line'] ?? '?',
            );
        }

        return $buffer;
    }
}
