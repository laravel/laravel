<?php

namespace Illuminate\Foundation\Configuration;

use Closure;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;

class Exceptions
{
    /**
     * Create a new exception handling configuration instance.
     *
     * @param  \Illuminate\Foundation\Exceptions\Handler  $handler
     */
    public function __construct(public Handler $handler)
    {
    }

    /**
     * Register a reportable callback.
     *
     * @param  callable  $using
     * @return \Illuminate\Foundation\Exceptions\ReportableHandler
     */
    public function report(callable $using)
    {
        return $this->handler->reportable($using);
    }

    /**
     * Register a reportable callback.
     *
     * @param  callable  $reportUsing
     * @return \Illuminate\Foundation\Exceptions\ReportableHandler
     */
    public function reportable(callable $reportUsing)
    {
        return $this->handler->reportable($reportUsing);
    }

    /**
     * Register a renderable callback.
     *
     * @param  callable  $using
     * @return $this
     */
    public function render(callable $using)
    {
        $this->handler->renderable($using);

        return $this;
    }

    /**
     * Register a renderable callback.
     *
     * @param  callable  $renderUsing
     * @return $this
     */
    public function renderable(callable $renderUsing)
    {
        $this->handler->renderable($renderUsing);

        return $this;
    }

    /**
     * Register a callback to prepare the final, rendered exception response.
     *
     * @param  callable  $using
     * @return $this
     */
    public function respond(callable $using)
    {
        $this->handler->respondUsing($using);

        return $this;
    }

    /**
     * Specify the callback that should be used to throttle reportable exceptions.
     *
     * @param  callable  $throttleUsing
     * @return $this
     */
    public function throttle(callable $throttleUsing)
    {
        $this->handler->throttleUsing($throttleUsing);

        return $this;
    }

    /**
     * Register a new exception mapping.
     *
     * @param  \Closure|string  $from
     * @param  \Closure|string|null  $to
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function map($from, $to = null)
    {
        $this->handler->map($from, $to);

        return $this;
    }

    /**
     * Set the log level for the given exception type.
     *
     * @param  class-string<\Throwable>  $type
     * @param  \Psr\Log\LogLevel::*  $level
     * @return $this
     */
    public function level(string $type, string $level)
    {
        $this->handler->level($type, $level);

        return $this;
    }

    /**
     * Register a closure that should be used to build exception context data.
     *
     * @param  \Closure  $contextCallback
     * @return $this
     */
    public function context(Closure $contextCallback)
    {
        $this->handler->buildContextUsing($contextCallback);

        return $this;
    }

    /**
     * Indicate that the given exception type should not be reported.
     *
     * @param  array|string  $class
     * @return $this
     */
    public function dontReport(array|string $class)
    {
        foreach (Arr::wrap($class) as $exceptionClass) {
            $this->handler->dontReport($exceptionClass);
        }

        return $this;
    }

    /**
     * Register a callback to determine if an exception should not be reported.
     *
     * @param  (\Closure(\Throwable): bool)  $dontReportWhen
     * @return $this
     */
    public function dontReportWhen(Closure $dontReportWhen)
    {
        $this->handler->dontReportWhen($dontReportWhen);

        return $this;
    }

    /**
     * Do not report duplicate exceptions.
     *
     * @return $this
     */
    public function dontReportDuplicates()
    {
        $this->handler->dontReportDuplicates();

        return $this;
    }

    /**
     * Indicate that the given attributes should never be flashed to the session on validation errors.
     *
     * @param  array|string  $attributes
     * @return $this
     */
    public function dontFlash(array|string $attributes)
    {
        $this->handler->dontFlash($attributes);

        return $this;
    }

    /**
     * Register the callable that determines if the exception handler response should be JSON.
     *
     * @param  callable(\Illuminate\Http\Request $request, \Throwable): bool  $callback
     * @return $this
     */
    public function shouldRenderJsonWhen(callable $callback)
    {
        $this->handler->shouldRenderJsonWhen($callback);

        return $this;
    }

    /**
     * Indicate that the given exception class should not be ignored.
     *
     * @param  array<int, class-string<\Throwable>>|class-string<\Throwable>  $class
     * @return $this
     */
    public function stopIgnoring(array|string $class)
    {
        $this->handler->stopIgnoring($class);

        return $this;
    }

    /**
     * Set the truncation length for request exception messages.
     *
     * @param  int  $length
     * @return $this
     */
    public function truncateRequestExceptionsAt(int $length)
    {
        RequestException::truncateAt($length);

        return $this;
    }

    /**
     * Disable truncation of request exception messages.
     *
     * @return $this
     */
    public function dontTruncateRequestExceptions()
    {
        RequestException::dontTruncate();

        return $this;
    }
}
