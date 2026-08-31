<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ErrorHandler;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Component\ErrorHandler\Error\OutOfMemoryError;
use Symfony\Component\ErrorHandler\ErrorEnhancer\ClassNotFoundErrorEnhancer;
use Symfony\Component\ErrorHandler\ErrorEnhancer\ErrorEnhancerInterface;
use Symfony\Component\ErrorHandler\ErrorEnhancer\UndefinedFunctionErrorEnhancer;
use Symfony\Component\ErrorHandler\ErrorEnhancer\UndefinedMethodErrorEnhancer;
use Symfony\Component\ErrorHandler\ErrorRenderer\CliErrorRenderer;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\SilencedErrorContext;

/**
 * A generic ErrorHandler for the PHP engine.
 *
 * Provides five bit fields that control how errors are handled:
 * - thrownErrors: errors thrown as \ErrorException
 * - loggedErrors: logged errors, when not @-silenced
 * - scopedErrors: errors thrown or logged with their local context
 * - tracedErrors: errors logged with their stack trace
 * - screamedErrors: never @-silenced errors
 *
 * Each error level can be logged by a dedicated PSR-3 logger object.
 * Screaming only applies to logging.
 * Throwing takes precedence over logging.
 * Uncaught exceptions are logged as E_ERROR.
 * E_DEPRECATED and E_USER_DEPRECATED levels never throw.
 * E_RECOVERABLE_ERROR and E_USER_ERROR levels always throw.
 * Non catchable errors that can be detected at shutdown time are logged when the scream bit field allows so.
 * As errors have a performance cost, repeated errors are all logged, so that the developer
 * can see them and weight them as more important to fix than others of the same level.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 *
 * @final
 */
class ErrorHandler
{
    private array $levels = [
        \E_DEPRECATED => 'Deprecated',
        \E_USER_DEPRECATED => 'User Deprecated',
        \E_NOTICE => 'Notice',
        \E_USER_NOTICE => 'User Notice',
        \E_WARNING => 'Warning',
        \E_USER_WARNING => 'User Warning',
        \E_COMPILE_WARNING => 'Compile Warning',
        \E_CORE_WARNING => 'Core Warning',
        \E_USER_ERROR => 'User Error',
        \E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        \E_COMPILE_ERROR => 'Compile Error',
        \E_PARSE => 'Parse Error',
        \E_ERROR => 'Error',
        \E_CORE_ERROR => 'Core Error',
    ];

    private array $loggers = [
        \E_DEPRECATED => [null, LogLevel::INFO],
        \E_USER_DEPRECATED => [null, LogLevel::INFO],
        \E_NOTICE => [null, LogLevel::ERROR],
        \E_USER_NOTICE => [null, LogLevel::ERROR],
        \E_WARNING => [null, LogLevel::ERROR],
        \E_USER_WARNING => [null, LogLevel::ERROR],
        \E_COMPILE_WARNING => [null, LogLevel::ERROR],
        \E_CORE_WARNING => [null, LogLevel::ERROR],
        \E_USER_ERROR => [null, LogLevel::CRITICAL],
        \E_RECOVERABLE_ERROR => [null, LogLevel::CRITICAL],
        \E_COMPILE_ERROR => [null, LogLevel::CRITICAL],
        \E_PARSE => [null, LogLevel::CRITICAL],
        \E_ERROR => [null, LogLevel::CRITICAL],
        \E_CORE_ERROR => [null, LogLevel::CRITICAL],
    ];

    private int $thrownErrors = 0x1FFF; // E_ALL - E_DEPRECATED - E_USER_DEPRECATED
    private int $scopedErrors = 0x1FFF; // E_ALL - E_DEPRECATED - E_USER_DEPRECATED
    private int $tracedErrors = 0x77FB; // E_ALL - E_STRICT - E_PARSE
    private int $screamedErrors = 0x55; // E_ERROR + E_CORE_ERROR + E_COMPILE_ERROR + E_PARSE
    private int $loggedErrors = 0;
    private \Closure $configureException;

    private bool $isRecursive = false;
    private bool $isRoot = false;
    /** @var callable|null */
    private $exceptionHandler;
    private ?BufferingLogger $bootstrappingLogger = null;

    private static ?string $reservedMemory = null;
    private static array $silencedErrorCache = [];
    private static int $silencedErrorCount = 0;
    private static int $exitCode = 0;

    /**
     * Registers the error handler.
     */
    public static function register(?self $handler = null, bool $replace = true): self
    {
        if (null === self::$reservedMemory) {
            self::$reservedMemory = str_repeat('x', 32768);
            register_shutdown_function(self::handleFatalError(...));
        }

        if ($handlerIsNew = null === $handler) {
            $handler = new static();
        }

        if (null === $prev = get_error_handler()) {
            // Specifying the error types earlier would expose us to https://bugs.php.net/63206
            set_error_handler([$handler, 'handleError'], $handler->thrownErrors | $handler->loggedErrors);
            $handler->isRoot = true;
        } else {
            set_error_handler([$handler, 'handleError']);
        }

        if ($handlerIsNew && \is_array($prev) && $prev[0] instanceof self) {
            $handler = $prev[0];
            $replace = false;
        }
        if (!$replace && $prev) {
            restore_error_handler();
            $handlerIsRegistered = \is_array($prev) && $handler === $prev[0];
        } else {
            $handlerIsRegistered = true;
        }
        if (\is_array($prev = set_exception_handler([$handler, 'handleException'])) && $prev[0] instanceof self) {
            restore_exception_handler();
            if (!$handlerIsRegistered) {
                $handler = $prev[0];
            } elseif ($handler !== $prev[0] && $replace) {
                set_exception_handler([$handler, 'handleException']);
                $p = $prev[0]->setExceptionHandler(null);
                $handler->setExceptionHandler($p);
                $prev[0]->setExceptionHandler($p);
            }
        } else {
            $handler->setExceptionHandler($prev ?? [$handler, 'renderException']);
        }

        $handler->throwAt(\E_ALL & $handler->thrownErrors, true);

        return $handler;
    }

    /**
     * Calls a function and turns any PHP error into \ErrorException.
     *
     * @throws \ErrorException When $function(...$arguments) triggers a PHP error
     */
    public static function call(callable $function, mixed ...$arguments): mixed
    {
        set_error_handler(static function (int $type, string $message, string $file, int $line) {
            if (__FILE__ === $file) {
                $trace = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 3);
                $file = $trace[2]['file'] ?? $file;
                $line = $trace[2]['line'] ?? $line;
            }

            throw new \ErrorException($message, 0, $type, $file, $line);
        });

        try {
            return $function(...$arguments);
        } finally {
            restore_error_handler();
        }
    }

    public function __construct(
        ?BufferingLogger $bootstrappingLogger = null,
        private bool $debug = false,
    ) {
        if (\PHP_VERSION_ID < 80400) {
            $this->levels[\E_STRICT] = 'Runtime Notice';
            $this->loggers[\E_STRICT] = [null, LogLevel::ERROR];
        }

        if ($bootstrappingLogger) {
            $this->bootstrappingLogger = $bootstrappingLogger;
            $this->setDefaultLogger($bootstrappingLogger);
        }
        $traceReflector = new \ReflectionProperty(\Exception::class, 'trace');
        $this->configureException = \Closure::bind(static function ($e, $trace, $file = null, $line = null) use ($traceReflector) {
            $traceReflector->setValue($e, $trace);
            $e->file = $file ?? $e->file;
            $e->line = $line ?? $e->line;
        }, null, new class extends \Exception {
        });
    }

    /**
     * Sets a logger to non assigned errors levels.
     *
     * @param LoggerInterface $logger  A PSR-3 logger to put as default for the given levels
     * @param array|int|null  $levels  An array map of E_* to LogLevel::* or an integer bit field of E_* constants
     * @param bool            $replace Whether to replace or not any existing logger
     */
    public function setDefaultLogger(LoggerInterface $logger, array|int|null $levels = \E_ALL, bool $replace = false): void
    {
        $loggers = [];

        if (\is_array($levels)) {
            foreach ($levels as $type => $logLevel) {
                if (empty($this->loggers[$type][0]) || $replace || $this->loggers[$type][0] === $this->bootstrappingLogger) {
                    $loggers[$type] = [$logger, $logLevel];
                }
            }
        } else {
            $levels ??= \E_ALL;
            foreach ($this->loggers as $type => $log) {
                if (($type & $levels) && (empty($log[0]) || $replace || $log[0] === $this->bootstrappingLogger)) {
                    $log[0] = $logger;
                    $loggers[$type] = $log;
                }
            }
        }

        $this->setLoggers($loggers);
    }

    /**
     * Sets a logger for each error level.
     *
     * @param array $loggers Error levels to [LoggerInterface|null, LogLevel::*] map
     *
     * @throws \InvalidArgumentException
     */
    public function setLoggers(array $loggers): array
    {
        $prevLogged = $this->loggedErrors;
        $prev = $this->loggers;
        $flush = [];

        foreach ($loggers as $type => $log) {
            if (!isset($prev[$type])) {
                throw new \InvalidArgumentException('Unknown error type: '.$type);
            }
            if (!\is_array($log)) {
                $log = [$log];
            } elseif (!\array_key_exists(0, $log)) {
                throw new \InvalidArgumentException('No logger provided.');
            }
            if (null === $log[0]) {
                $this->loggedErrors &= ~$type;
            } elseif ($log[0] instanceof LoggerInterface) {
                $this->loggedErrors |= $type;
            } else {
                throw new \InvalidArgumentException('Invalid logger provided.');
            }
            $this->loggers[$type] = $log + $prev[$type];

            if ($this->bootstrappingLogger && $prev[$type][0] === $this->bootstrappingLogger) {
                $flush[$type] = $type;
            }
        }
        $this->reRegister($prevLogged | $this->thrownErrors);

        if ($flush) {
            foreach ($this->bootstrappingLogger->cleanLogs() as $log) {
                $type = ThrowableUtils::getSeverity($log[2]['exception']);
                if (!isset($flush[$type])) {
                    $this->bootstrappingLogger->log($log[0], $log[1], $log[2]);
                } elseif ($this->loggers[$type][0]) {
                    $this->loggers[$type][0]->log($this->loggers[$type][1], $log[1], $log[2]);
                }
            }
        }

        return $prev;
    }

    public function setExceptionHandler(?callable $handler): ?callable
    {
        $prev = $this->exceptionHandler;
        $this->exceptionHandler = $handler;

        return $prev;
    }

    /**
     * Sets the PHP error levels that throw an exception when a PHP error occurs.
     *
     * @param int  $levels  A bit field of E_* constants for thrown errors
     * @param bool $replace Replace or amend the previous value
     */
    public function throwAt(int $levels, bool $replace = false): int
    {
        $prev = $this->thrownErrors;
        $this->thrownErrors = ($levels | \E_RECOVERABLE_ERROR | \E_USER_ERROR) & ~\E_USER_DEPRECATED & ~\E_DEPRECATED;
        if (!$replace) {
            $this->thrownErrors |= $prev;
        }
        $this->reRegister($prev | $this->loggedErrors);

        return $prev;
    }

    /**
     * Sets the PHP error levels for which local variables are preserved.
     *
     * @param int  $levels  A bit field of E_* constants for scoped errors
     * @param bool $replace Replace or amend the previous value
     */
    public function scopeAt(int $levels, bool $replace = false): int
    {
        $prev = $this->scopedErrors;
        $this->scopedErrors = $levels;
        if (!$replace) {
            $this->scopedErrors |= $prev;
        }

        return $prev;
    }

    /**
     * Sets the PHP error levels for which the stack trace is preserved.
     *
     * @param int  $levels  A bit field of E_* constants for traced errors
     * @param bool $replace Replace or amend the previous value
     */
    public function traceAt(int $levels, bool $replace = false): int
    {
        $prev = $this->tracedErrors;
        $this->tracedErrors = $levels;
        if (!$replace) {
            $this->tracedErrors |= $prev;
        }

        return $prev;
    }

    /**
     * Sets the error levels where the @-operator is ignored.
     *
     * @param int  $levels  A bit field of E_* constants for screamed errors
     * @param bool $replace Replace or amend the previous value
     */
    public function screamAt(int $levels, bool $replace = false): int
    {
        $prev = $this->screamedErrors;
        $this->screamedErrors = $levels;
        if (!$replace) {
            $this->screamedErrors |= $prev;
        }

        return $prev;
    }

    /**
     * Re-registers as a PHP error handler if levels changed.
     */
    private function reRegister(int $prev): void
    {
        if ($prev !== ($this->thrownErrors | $this->loggedErrors)) {
            $handler = get_error_handler();
            $handler = \is_array($handler) ? $handler[0] : null;
            if ($handler === $this) {
                restore_error_handler();
                if ($this->isRoot) {
                    set_error_handler([$this, 'handleError'], $this->thrownErrors | $this->loggedErrors);
                } else {
                    set_error_handler([$this, 'handleError']);
                }
            }
        }
    }

    /**
     * Handles errors by filtering then logging them according to the configured bit fields.
     *
     * @return bool Returns false when no handling happens so that the PHP engine can handle the error itself
     *
     * @throws \ErrorException When $this->thrownErrors requests so
     *
     * @internal
     */
    public function handleError(int $type, string $message, string $file, int $line): bool
    {
        if (\E_WARNING === $type && '"' === $message[0] && str_contains($message, '" targeting switch is equivalent to "break')) {
            $type = \E_DEPRECATED;
        }

        // Level is the current error reporting level to manage silent error.
        $level = error_reporting();
        $silenced = 0 === ($level & $type);
        // Strong errors are not authorized to be silenced.
        $level |= \E_RECOVERABLE_ERROR | \E_USER_ERROR | \E_DEPRECATED | \E_USER_DEPRECATED;
        $log = $this->loggedErrors & $type;
        $throw = $this->thrownErrors & $type & $level;
        $type &= $level | $this->screamedErrors;

        // Never throw on warnings triggered by assert()
        if (\E_WARNING === $type && 'a' === $message[0] && 0 === strncmp($message, 'assert(): ', 10)) {
            $throw = 0;
        }

        if (!$type || (!$log && !$throw)) {
            return false;
        }

        $logMessage = $this->levels[$type].': '.$message;

        if (!$throw && !($type & $level)) {
            if (!isset(self::$silencedErrorCache[$id = $file.':'.$line])) {
                $lightTrace = $this->tracedErrors & $type ? $this->cleanTrace(debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 5), $type, $file, $line, false) : [];
                $errorAsException = new SilencedErrorContext($type, $file, $line, isset($lightTrace[1]) ? [$lightTrace[0]] : $lightTrace);
            } elseif (isset(self::$silencedErrorCache[$id][$message])) {
                $lightTrace = null;
                $errorAsException = self::$silencedErrorCache[$id][$message];
                ++$errorAsException->count;
            } else {
                $lightTrace = [];
                $errorAsException = null;
            }

            if (100 < ++self::$silencedErrorCount) {
                self::$silencedErrorCache = $lightTrace = [];
                self::$silencedErrorCount = 1;
            }
            if ($errorAsException) {
                self::$silencedErrorCache[$id][$message] = $errorAsException;
            }
            if (null === $lightTrace) {
                return true;
            }
        } else {
            if (\PHP_VERSION_ID < 80303 && str_contains($message, '@anonymous')) {
                $backtrace = debug_backtrace(false, 5);

                for ($i = 1; isset($backtrace[$i]); ++$i) {
                    if (isset($backtrace[$i]['function'], $backtrace[$i]['args'][0])
                        && ('trigger_error' === $backtrace[$i]['function'] || 'user_error' === $backtrace[$i]['function'])
                    ) {
                        if ($backtrace[$i]['args'][0] !== $message) {
                            $message = $backtrace[$i]['args'][0];
                        }

                        break;
                    }
                }
            }

            if (str_contains($message, "@anonymous\0")) {
                $message = $this->parseAnonymousClass($message);
                $logMessage = $this->levels[$type].': '.$message;
            }

            $errorAsException = new \ErrorException($logMessage, 0, $type, $file, $line);

            if ($throw || $this->tracedErrors & $type) {
                $backtrace = $errorAsException->getTrace();
                $backtrace = $this->cleanTrace($backtrace, $type, $file, $line, $throw);
                ($this->configureException)($errorAsException, $backtrace, $file, $line);
            } else {
                ($this->configureException)($errorAsException, []);
            }
        }

        if ($throw) {
            throw $errorAsException;
        }

        if ($this->isRecursive) {
            $log = 0;
        } else {
            try {
                $this->isRecursive = true;
                $level = ($type & $level) ? $this->loggers[$type][1] : LogLevel::DEBUG;
                $this->loggers[$type][0]->log($level, $logMessage, $errorAsException ? ['exception' => $errorAsException] : []);
            } finally {
                $this->isRecursive = false;
            }
        }

        return !$silenced && $type && $log;
    }

    /**
     * Handles an exception by logging then forwarding it to another handler.
     *
     * @internal
     */
    public function handleException(\Throwable $exception): void
    {
        $handlerException = null;

        if (!$exception instanceof FatalError) {
            self::$exitCode = 255;

            $type = ThrowableUtils::getSeverity($exception);
        } else {
            $type = $exception->getError()['type'];
        }

        if ($this->loggedErrors & $type) {
            if (str_contains($message = $exception->getMessage(), "@anonymous\0")) {
                $message = $this->parseAnonymousClass($message);
            }

            if ($exception instanceof FatalError) {
                $message = 'Fatal '.$message;
            } elseif ($exception instanceof \Error) {
                $message = 'Uncaught Error: '.$message;
            } elseif ($exception instanceof \ErrorException) {
                $message = 'Uncaught '.$message;
            } else {
                $message = 'Uncaught Exception: '.$message;
            }

            try {
                $this->loggers[$type][0]->log($this->loggers[$type][1], $message, ['exception' => $exception]);
            } catch (\Throwable $handlerException) {
            }
        }

        $exception = $this->enhanceError($exception);

        $exceptionHandler = $this->exceptionHandler;
        $this->exceptionHandler = [$this, 'renderException'];

        if (null === $exceptionHandler || $exceptionHandler === $this->exceptionHandler) {
            $this->exceptionHandler = null;
        }

        try {
            if (null !== $exceptionHandler) {
                $exceptionHandler($exception);

                return;
            }
            $handlerException ??= $exception;
        } catch (\Throwable $handlerException) {
        }
        if ($exception === $handlerException && null === $this->exceptionHandler) {
            self::$reservedMemory = null; // Disable the fatal error handler
            throw $exception; // Give back $exception to the native handler
        }

        $loggedErrors = $this->loggedErrors;
        if ($exception === $handlerException) {
            $this->loggedErrors &= ~$type;
        }

        try {
            $this->handleException($handlerException);
        } finally {
            $this->loggedErrors = $loggedErrors;
        }
    }

    /**
     * Shutdown registered function for handling PHP fatal errors.
     *
     * @param array|null $error An array as returned by error_get_last()
     *
     * @internal
     */
    public static function handleFatalError(?array $error = null): void
    {
        if (null === self::$reservedMemory) {
            return;
        }

        $handler = self::$reservedMemory = null;
        $handlers = [];
        $previousHandler = null;
        $sameHandlerLimit = 10;

        while (!\is_array($handler) || !$handler[0] instanceof self) {
            $handler = set_exception_handler('is_int');
            restore_exception_handler();

            if (!$handler) {
                break;
            }
            restore_exception_handler();

            if ($handler !== $previousHandler) {
                array_unshift($handlers, $handler);
                $previousHandler = $handler;
            } elseif (0 === --$sameHandlerLimit) {
                $handler = null;
                break;
            }
        }
        foreach ($handlers as $h) {
            set_exception_handler($h);
        }
        if (!$handler) {
            if (null === $error && $exitCode = self::$exitCode) {
                register_shutdown_function('register_shutdown_function', static function () use ($exitCode) { exit($exitCode); });
            }

            return;
        }
        if ($handler !== $h) {
            $handler[0]->setExceptionHandler($h);
        }
        $handler = $handler[0];
        $handlers = [];

        if ($exit = null === $error) {
            $error = error_get_last();
        }

        if ($error && $error['type'] &= \E_PARSE | \E_ERROR | \E_CORE_ERROR | \E_COMPILE_ERROR) {
            // Let's not throw anymore but keep logging
            $handler->throwAt(0, true);
            $trace = $error['backtrace'] ?? null;

            if (str_starts_with($error['message'], 'Allowed memory') || str_starts_with($error['message'], 'Out of memory')) {
                $fatalError = new OutOfMemoryError($handler->levels[$error['type']].': '.$error['message'], 0, $error, 2, false, $trace);
            } else {
                $fatalError = new FatalError($handler->levels[$error['type']].': '.$error['message'], 0, $error, 2, true, $trace);
            }
        } else {
            $fatalError = null;
        }

        try {
            if (null !== $fatalError) {
                self::$exitCode = 255;
                $handler->handleException($fatalError);
            }
        } catch (FatalError) {
            // Ignore this re-throw
        }

        if ($exit && $exitCode = self::$exitCode) {
            register_shutdown_function('register_shutdown_function', static function () use ($exitCode) { exit($exitCode); });
        }
    }

    /**
     * Renders the given exception.
     *
     * As this method is mainly called during boot where nothing is yet available,
     * the output is always either HTML or CLI depending where PHP runs.
     */
    private function renderException(\Throwable $exception): void
    {
        $renderer = \in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) ? new CliErrorRenderer() : new HtmlErrorRenderer($this->debug);

        $exception = $renderer->render($exception);

        if (!headers_sent()) {
            http_response_code($exception->getStatusCode());

            foreach ($exception->getHeaders() as $name => $value) {
                header($name.': '.$value, false);
            }
        }

        echo $exception->getAsString();
    }

    public function enhanceError(\Throwable $exception): \Throwable
    {
        if ($exception instanceof OutOfMemoryError) {
            return $exception;
        }

        foreach ($this->getErrorEnhancers() as $errorEnhancer) {
            if ($e = $errorEnhancer->enhance($exception)) {
                return $e;
            }
        }

        return $exception;
    }

    /**
     * Override this method if you want to define more error enhancers.
     *
     * @return ErrorEnhancerInterface[]
     */
    protected function getErrorEnhancers(): iterable
    {
        return [
            new UndefinedFunctionErrorEnhancer(),
            new UndefinedMethodErrorEnhancer(),
            new ClassNotFoundErrorEnhancer(),
        ];
    }

    /**
     * Cleans the trace by removing function arguments and the frames added by the error handler and DebugClassLoader.
     */
    private function cleanTrace(array $backtrace, int $type, string &$file, int &$line, bool $throw): array
    {
        $lightTrace = $backtrace;

        for ($i = 0; isset($backtrace[$i]); ++$i) {
            if (isset($backtrace[$i]['file'], $backtrace[$i]['line']) && $backtrace[$i]['line'] === $line && $backtrace[$i]['file'] === $file) {
                $lightTrace = \array_slice($lightTrace, 1 + $i);
                break;
            }
        }
        if (\E_USER_DEPRECATED === $type) {
            for ($i = 0; isset($lightTrace[$i]); ++$i) {
                if (!isset($lightTrace[$i]['file'], $lightTrace[$i]['line'], $lightTrace[$i]['function'])) {
                    continue;
                }
                if (!isset($lightTrace[$i]['class']) && 'trigger_deprecation' === $lightTrace[$i]['function']) {
                    $file = $lightTrace[$i]['file'];
                    $line = $lightTrace[$i]['line'];
                    $lightTrace = \array_slice($lightTrace, 1 + $i);
                    break;
                }
            }
        }
        if (class_exists(DebugClassLoader::class, false)) {
            for ($i = \count($lightTrace) - 2; 0 < $i; --$i) {
                if (DebugClassLoader::class === ($lightTrace[$i]['class'] ?? null)) {
                    array_splice($lightTrace, --$i, 2);
                }
            }
        }
        if (!($throw || $this->scopedErrors & $type)) {
            for ($i = 0; isset($lightTrace[$i]); ++$i) {
                unset($lightTrace[$i]['args'], $lightTrace[$i]['object']);
            }
        }

        return $lightTrace;
    }

    /**
     * Parse the error message by removing the anonymous class notation
     * and using the parent class instead if possible.
     */
    private function parseAnonymousClass(string $message): string
    {
        return preg_replace_callback('/[a-zA-Z_\x7f-\xff][\\\\a-zA-Z0-9_\x7f-\xff]*+@anonymous\x00.*?\.php(?:0x?|:[0-9]++\$)?[0-9a-fA-F]++/', static fn ($m) => class_exists($m[0], false) ? (get_parent_class($m[0]) ?: key(class_implements($m[0])) ?: 'class').'@anonymous' : $m[0], $message);
    }
}
