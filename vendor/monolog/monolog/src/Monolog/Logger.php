<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog;

use Closure;
use DateTimeZone;
use Fiber;
use Monolog\Handler\HandlerInterface;
use Monolog\Processor\ProcessorInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;
use Throwable;
use Stringable;
use WeakMap;

/**
 * Monolog log channel
 *
 * It contains a stack of Handlers and a stack of Processors,
 * and uses them to store records that are added to it.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @final
 */
class Logger implements LoggerInterface, ResettableInterface
{
    /**
     * Detailed debug information
     *
     * @deprecated Use \Monolog\Level::Debug
     */
    public const DEBUG = 100;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     *
     * @deprecated Use \Monolog\Level::Info
     */
    public const INFO = 200;

    /**
     * Uncommon events
     *
     * @deprecated Use \Monolog\Level::Notice
     */
    public const NOTICE = 250;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     *
     * @deprecated Use \Monolog\Level::Warning
     */
    public const WARNING = 300;

    /**
     * Runtime errors
     *
     * @deprecated Use \Monolog\Level::Error
     */
    public const ERROR = 400;

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @deprecated Use \Monolog\Level::Critical
     */
    public const CRITICAL = 500;

    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     *
     * @deprecated Use \Monolog\Level::Alert
     */
    public const ALERT = 550;

    /**
     * Urgent alert.
     *
     * @deprecated Use \Monolog\Level::Emergency
     */
    public const EMERGENCY = 600;

    /**
     * Monolog API version
     *
     * This is only bumped when API breaks are done and should
     * follow the major version of the library
     */
    public const API = 3;

    /**
     * Mapping between levels numbers defined in RFC 5424 and Monolog ones
     *
     * @phpstan-var array<int, Level> $rfc_5424_levels
     */
    private const RFC_5424_LEVELS = [
        7 => Level::Debug,
        6 => Level::Info,
        5 => Level::Notice,
        4 => Level::Warning,
        3 => Level::Error,
        2 => Level::Critical,
        1 => Level::Alert,
        0 => Level::Emergency,
    ];

    protected string $name;

    /**
     * The handler stack
     *
     * @var list<HandlerInterface>
     */
    protected array $handlers;

    /**
     * Processors that will process all log records
     *
     * To process records of a single handler instead, add the processor on that specific handler
     *
     * @var array<(callable(LogRecord): LogRecord)|ProcessorInterface>
     */
    protected array $processors;

    protected bool $microsecondTimestamps = true;

    protected DateTimeZone $timezone;

    protected Closure|null $exceptionHandler = null;

    /**
     * Keeps track of depth to prevent infinite logging loops
     */
    private int $logDepth = 0;

    /**
     * @var WeakMap<Fiber<mixed, mixed, mixed, mixed>, int> Keeps track of depth inside fibers to prevent infinite logging loops
     */
    private WeakMap $fiberLogDepth;

    /**
     * Whether to detect infinite logging loops
     * This can be disabled via {@see useLoggingLoopDetection} if you have async handlers that do not play well with this
     */
    private bool $detectCycles = true;

    /**
     * @param string             $name       The logging channel, a simple descriptive name that is attached to all log records
     * @param list<HandlerInterface> $handlers   Optional stack of handlers, the first one in the array is called first, etc.
     * @param callable[]         $processors Optional array of processors
     * @param DateTimeZone|null  $timezone   Optional timezone, if not provided date_default_timezone_get() will be used
     *
     * @phpstan-param array<(callable(LogRecord): LogRecord)|ProcessorInterface> $processors
     */
    public function __construct(string $name, array $handlers = [], array $processors = [], DateTimeZone|null $timezone = null)
    {
        $this->name = $name;
        $this->setHandlers($handlers);
        $this->processors = $processors;
        $this->timezone = $timezone ?? new DateTimeZone(date_default_timezone_get());
        $this->fiberLogDepth = new \WeakMap();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return a new cloned instance with the name changed
     *
     * @return static
     */
    public function withName(string $name): self
    {
        $new = clone $this;
        $new->name = $name;

        return $new;
    }

    /**
     * Pushes a handler on to the stack.
     *
     * @return $this
     */
    public function pushHandler(HandlerInterface $handler): self
    {
        array_unshift($this->handlers, $handler);

        return $this;
    }

    /**
     * Pops a handler from the stack
     *
     * @throws \LogicException If empty handler stack
     */
    public function popHandler(): HandlerInterface
    {
        if (0 === \count($this->handlers)) {
            throw new \LogicException('You tried to pop from an empty handler stack.');
        }

        return array_shift($this->handlers);
    }

    /**
     * Set handlers, replacing all existing ones.
     *
     * If a map is passed, keys will be ignored.
     *
     * @param  list<HandlerInterface> $handlers
     * @return $this
     */
    public function setHandlers(array $handlers): self
    {
        $this->handlers = [];
        foreach (array_reverse($handlers) as $handler) {
            $this->pushHandler($handler);
        }

        return $this;
    }

    /**
     * @return list<HandlerInterface>
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * Adds a processor on to the stack.
     *
     * @phpstan-param ProcessorInterface|(callable(LogRecord): LogRecord) $callback
     * @return $this
     */
    public function pushProcessor(ProcessorInterface|callable $callback): self
    {
        array_unshift($this->processors, $callback);

        return $this;
    }

    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @phpstan-return ProcessorInterface|(callable(LogRecord): LogRecord)
     * @throws \LogicException If empty processor stack
     */
    public function popProcessor(): callable
    {
        if (0 === \count($this->processors)) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }

        return array_shift($this->processors);
    }

    /**
     * @return callable[]
     * @phpstan-return array<ProcessorInterface|(callable(LogRecord): LogRecord)>
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    /**
     * Control the use of microsecond resolution timestamps in the 'datetime'
     * member of new records.
     *
     * As of PHP7.1 microseconds are always included by the engine, so
     * there is no performance penalty and Monolog 2 enabled microseconds
     * by default. This function lets you disable them though in case you want
     * to suppress microseconds from the output.
     *
     * @param  bool  $micro True to use microtime() to create timestamps
     * @return $this
     */
    public function useMicrosecondTimestamps(bool $micro): self
    {
        $this->microsecondTimestamps = $micro;

        return $this;
    }

    /**
     * @return $this
     */
    public function useLoggingLoopDetection(bool $detectCycles): self
    {
        $this->detectCycles = $detectCycles;

        return $this;
    }

    /**
     * Adds a log record.
     *
     * @param  int                    $level    The logging level (a Monolog or RFC 5424 level)
     * @param  string                 $message  The log message
     * @param  mixed[]                $context  The log context
     * @param  JsonSerializableDateTimeImmutable|null $datetime Optional log date to log into the past or future
     *
     * @return bool                   Whether the record has been processed
     *
     * @phpstan-param value-of<Level::VALUES>|Level $level
     */
    public function addRecord(int|Level $level, string $message, array $context = [], JsonSerializableDateTimeImmutable|null $datetime = null): bool
    {
        if (\is_int($level) && isset(self::RFC_5424_LEVELS[$level])) {
            $level = self::RFC_5424_LEVELS[$level];
        }

        if ($this->detectCycles) {
            if (null !== ($fiber = Fiber::getCurrent())) {
                $logDepth = $this->fiberLogDepth[$fiber] = ($this->fiberLogDepth[$fiber] ?? 0) + 1;
            } else {
                $logDepth = ++$this->logDepth;
            }
        } else {
            $logDepth = 0;
        }

        if ($logDepth === 3) {
            $this->warning('A possible infinite logging loop was detected and aborted. It appears some of your handler code is triggering logging, see the previous log record for a hint as to what may be the cause.');

            return false;
        } elseif ($logDepth >= 5) { // log depth 4 is let through, so we can log the warning above
            return false;
        }

        try {
            $recordInitialized = \count($this->processors) === 0;

            $record = new LogRecord(
                datetime: $datetime ?? new JsonSerializableDateTimeImmutable($this->microsecondTimestamps, $this->timezone),
                channel: $this->name,
                level: self::toMonologLevel($level),
                message: $message,
                context: $context,
                extra: [],
            );
            $handled = false;

            foreach ($this->handlers as $handler) {
                if (false === $recordInitialized) {
                    // skip initializing the record as long as no handler is going to handle it
                    if (!$handler->isHandling($record)) {
                        continue;
                    }

                    try {
                        foreach ($this->processors as $processor) {
                            $record = $processor($record);
                        }
                        $recordInitialized = true;
                    } catch (Throwable $e) {
                        $this->handleException($e, $record);

                        return true;
                    }
                }

                // once the record is initialized, send it to all handlers as long as the bubbling chain is not interrupted
                try {
                    $handled = true;
                    if (true === $handler->handle(clone $record)) {
                        break;
                    }
                } catch (Throwable $e) {
                    $this->handleException($e, $record);

                    return true;
                }
            }

            return $handled;
        } finally {
            if ($this->detectCycles) {
                if (isset($fiber)) {
                    $this->fiberLogDepth[$fiber]--;
                } else {
                    $this->logDepth--;
                }
            }
        }
    }

    /**
     * Ends a log cycle and frees all resources used by handlers.
     *
     * Closing a Handler means flushing all buffers and freeing any open resources/handles.
     * Handlers that have been closed should be able to accept log records again and re-open
     * themselves on demand, but this may not always be possible depending on implementation.
     *
     * This is useful at the end of a request and will be called automatically on every handler
     * when they get destructed.
     */
    public function close(): void
    {
        foreach ($this->handlers as $handler) {
            $handler->close();
        }
    }

    /**
     * Ends a log cycle and resets all handlers and processors to their initial state.
     *
     * Resetting a Handler or a Processor means flushing/cleaning all buffers, resetting internal
     * state, and getting it back to a state in which it can receive log records again.
     *
     * This is useful in case you want to avoid logs leaking between two requests or jobs when you
     * have a long running process like a worker or an application server serving multiple requests
     * in one process.
     */
    public function reset(): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof ResettableInterface) {
                $handler->reset();
            }
        }

        foreach ($this->processors as $processor) {
            if ($processor instanceof ResettableInterface) {
                $processor->reset();
            }
        }
    }

    /**
     * Gets the name of the logging level as a string.
     *
     * This still returns a string instead of a Level for BC, but new code should not rely on this method.
     *
     * @throws \Psr\Log\InvalidArgumentException If level is not defined
     *
     * @phpstan-param  value-of<Level::VALUES>|Level $level
     * @phpstan-return value-of<Level::NAMES>
     *
     * @deprecated Since 3.0, use {@see toMonologLevel} or {@see \Monolog\Level->getName()} instead
     */
    public static function getLevelName(int|Level $level): string
    {
        return self::toMonologLevel($level)->getName();
    }

    /**
     * Converts PSR-3 levels to Monolog ones if necessary
     *
     * @param  int|string|Level|LogLevel::*      $level Level number (monolog) or name (PSR-3)
     * @throws \Psr\Log\InvalidArgumentException If level is not defined
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $level
     */
    public static function toMonologLevel(string|int|Level $level): Level
    {
        if ($level instanceof Level) {
            return $level;
        }

        if (\is_string($level)) {
            if (is_numeric($level)) {
                $levelEnum = Level::tryFrom((int) $level);
                if ($levelEnum === null) {
                    throw new InvalidArgumentException('Level "'.$level.'" is not defined, use one of: '.implode(', ', Level::NAMES + Level::VALUES));
                }

                return $levelEnum;
            }

            // Contains first char of all log levels and avoids using strtoupper() which may have
            // strange results depending on locale (for example, "i" will become "İ" in Turkish locale)
            $upper = strtr(substr($level, 0, 1), 'dinweca', 'DINWECA') . strtolower(substr($level, 1));
            if (\defined(Level::class.'::'.$upper)) {
                return \constant(Level::class . '::' . $upper);
            }

            throw new InvalidArgumentException('Level "'.$level.'" is not defined, use one of: '.implode(', ', Level::NAMES + Level::VALUES));
        }

        $levelEnum = Level::tryFrom($level);
        if ($levelEnum === null) {
            throw new InvalidArgumentException('Level "'.var_export($level, true).'" is not defined, use one of: '.implode(', ', Level::NAMES + Level::VALUES));
        }

        return $levelEnum;
    }

    /**
     * Checks whether the Logger has a handler that listens on the given level
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $level
     */
    public function isHandling(int|string|Level $level): bool
    {
        $record = new LogRecord(
            datetime: new JsonSerializableDateTimeImmutable($this->microsecondTimestamps, $this->timezone),
            channel: $this->name,
            message: '',
            level: self::toMonologLevel($level),
        );

        foreach ($this->handlers as $handler) {
            if ($handler->isHandling($record)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set a custom exception handler that will be called if adding a new record fails
     *
     * The Closure will receive an exception object and the record that failed to be logged
     *
     * @return $this
     */
    public function setExceptionHandler(Closure|null $callback): self
    {
        $this->exceptionHandler = $callback;

        return $this;
    }

    public function getExceptionHandler(): Closure|null
    {
        return $this->exceptionHandler;
    }

    /**
     * Adds a log record at an arbitrary level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param mixed             $level   The log level (a Monolog, PSR-3 or RFC 5424 level)
     * @param string|Stringable $message The log message
     * @param mixed[]           $context The log context
     *
     * @phpstan-param Level|LogLevel::* $level
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if (!$level instanceof Level) {
            if (!\is_string($level) && !\is_int($level)) {
                throw new \InvalidArgumentException('$level is expected to be a string, int or '.Level::class.' instance');
            }

            if (isset(self::RFC_5424_LEVELS[$level])) {
                $level = self::RFC_5424_LEVELS[$level];
            }

            $level = static::toMonologLevel($level);
        }

        $this->addRecord($level, (string) $message, $context);
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string|Stringable $message The log message
     * @param mixed[]           $context The log context
     */
    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->addRecord(Level::Debug, (string) $message, $context);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string|Stringable $message The log message
     * @param mixed[]           $context The log context
     */
    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->addRecord(Level::Info, (string) $message, $context);
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string|Stringable $message The log message
     * @param mixed[]           $context The log context
     */
    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->addRecord(Level::Notice, (string) $message, $context);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string|Stringable $message The log message
     * @param mixed[]           $context The log context
     */
    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->addRecord(Level::Warning, (string) $message, $context);
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string|Stringable $message The log message
     * @param mixed[]           $context The log context
     */
    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->addRecord(Level::Error, (string) $message, $context);
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string|Stringable $message The log message
     * @param mixed[]           $context The log context
     */
    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->addRecord(Level::Critical, (string) $message, $context);
    }

    /**
     * Adds a log record at the ALERT level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string|Stringable $message The log message
     * @param mixed[]           $context The log context
     */
    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->addRecord(Level::Alert, (string) $message, $context);
    }

    /**
     * Adds a log record at the EMERGENCY level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param string|Stringable $message The log message
     * @param mixed[]           $context The log context
     */
    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->addRecord(Level::Emergency, (string) $message, $context);
    }

    /**
     * Sets the timezone to be used for the timestamp of log records.
     *
     * @return $this
     */
    public function setTimezone(DateTimeZone $tz): self
    {
        $this->timezone = $tz;

        return $this;
    }

    /**
     * Returns the timezone to be used for the timestamp of log records.
     */
    public function getTimezone(): DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * Delegates exception management to the custom exception handler,
     * or throws the exception if no custom handler is set.
     */
    protected function handleException(Throwable $e, LogRecord $record): void
    {
        if (null === $this->exceptionHandler) {
            throw $e;
        }

        ($this->exceptionHandler)($e, $record);
    }

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'handlers' => $this->handlers,
            'processors' => $this->processors,
            'microsecondTimestamps' => $this->microsecondTimestamps,
            'timezone' => $this->timezone,
            'exceptionHandler' => $this->exceptionHandler,
            'logDepth' => $this->logDepth,
            'detectCycles' => $this->detectCycles,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        foreach (['name', 'handlers', 'processors', 'microsecondTimestamps', 'timezone', 'exceptionHandler', 'logDepth', 'detectCycles'] as $property) {
            if (isset($data[$property])) {
                $this->$property = $data[$property];
            }
        }

        $this->fiberLogDepth = new \WeakMap();
    }
}
