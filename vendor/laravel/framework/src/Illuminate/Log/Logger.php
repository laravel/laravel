<?php

namespace Illuminate\Log;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Traits\Conditionable;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Logger implements LoggerInterface
{
    use Conditionable;

    /**
     * The underlying logger implementation.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $dispatcher;

    /**
     * Any context to be added to logs.
     *
     * @var array
     */
    protected $context = [];

    /**
     * Create a new log writer instance.
     *
     * @param  \Psr\Log\LoggerInterface  $logger
     * @param  \Illuminate\Contracts\Events\Dispatcher|null  $dispatcher
     */
    public function __construct(LoggerInterface $logger, ?Dispatcher $dispatcher = null)
    {
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Log an emergency message to the logs.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @param  array  $context
     * @return void
     */
    public function emergency($message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an alert message to the logs.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @param  array  $context
     * @return void
     */
    public function alert($message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a critical message to the logs.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @param  array  $context
     * @return void
     */
    public function critical($message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an error message to the logs.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @param  array  $context
     * @return void
     */
    public function error($message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a warning message to the logs.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @param  array  $context
     * @return void
     */
    public function warning($message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a notice to the logs.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @param  array  $context
     * @return void
     */
    public function notice($message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an informational message to the logs.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @param  array  $context
     * @return void
     */
    public function info($message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a debug message to the logs.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @param  array  $context
     * @return void
     */
    public function debug($message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a message to the logs.
     *
     * @param  string  $level
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @param  array  $context
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        $this->writeLog($level, $message, $context);
    }

    /**
     * Dynamically pass log calls into the writer.
     *
     * @param  string  $level
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @param  array  $context
     * @return void
     */
    public function write($level, $message, array $context = []): void
    {
        $this->writeLog($level, $message, $context);
    }

    /**
     * Write a message to the log.
     *
     * @param  string  $level
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @param  array  $context
     * @return void
     */
    protected function writeLog($level, $message, $context): void
    {
        if (method_exists($this->logger, 'isHandling') && ! $this->logger->isHandling($level)) {
            return;
        }

        $this->logger->{$level}(
            $message = $this->formatMessage($message),
            $context = array_merge($this->context, $context)
        );

        $this->fireLogEvent($level, $message, $context);
    }

    /**
     * Add context to all future logs.
     *
     * @param  array  $context
     * @return $this
     */
    public function withContext(array $context = [])
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    /**
     * Flush the log context on all currently resolved channels.
     *
     * @param  string[]|null  $keys
     * @return $this
     */
    public function withoutContext(?array $keys = null)
    {
        if (is_array($keys)) {
            $this->context = array_diff_key($this->context, array_flip($keys));
        } else {
            $this->context = [];
        }

        return $this;
    }

    /**
     * Register a new callback handler for when a log event is triggered.
     *
     * @param  \Closure  $callback
     * @return void
     *
     * @throws \RuntimeException
     */
    public function listen(Closure $callback)
    {
        if (! isset($this->dispatcher)) {
            throw new RuntimeException('Events dispatcher has not been set.');
        }

        $this->dispatcher->listen(MessageLogged::class, $callback);
    }

    /**
     * Fires a log event.
     *
     * @param  string  $level
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    protected function fireLogEvent($level, $message, array $context = [])
    {
        // Avoid dispatching the event multiple times if our logger instance is the LogManager...
        if ($this->logger instanceof LogManager &&
            $this->logger->getEventDispatcher() !== null) {
            return;
        }

        // If the event dispatcher is set, we will pass along the parameters to the
        // log listeners. These are useful for building profilers or other tools
        // that aggregate all of the log messages for a given "request" cycle.
        $this->dispatcher?->dispatch(new MessageLogged($level, $message, $context));
    }

    /**
     * Format the parameters for the logger.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|\Illuminate\Contracts\Support\Jsonable|\Illuminate\Support\Stringable|array|string  $message
     * @return string
     */
    protected function formatMessage($message)
    {
        return match (true) {
            is_array($message) => var_export($message, true),
            $message instanceof Jsonable => $message->toJson(),
            $message instanceof Arrayable => var_export($message->toArray(), true),
            default => (string) $message,
        };
    }

    /**
     * Get the underlying logger implementation.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher|null
     */
    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @return void
     */
    public function setEventDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Dynamically proxy method calls to the underlying logger.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->logger->{$method}(...$parameters);
    }
}
