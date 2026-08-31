<?php

namespace Illuminate\Routing\Exceptions;

use Exception;

class MissingRateLimiterException extends Exception
{
    /**
     * Create a new exception for invalid named rate limiter.
     *
     * @param  string  $limiter
     * @return static
     */
    public static function forLimiter(string $limiter)
    {
        return new static("Rate limiter [{$limiter}] is not defined.");
    }

    /**
     * Create a new exception for an invalid rate limiter based on a model property.
     *
     * @param  string  $limiter
     * @param  class-string  $model
     * @return static
     */
    public static function forLimiterAndUser(string $limiter, string $model)
    {
        return new static("Rate limiter [{$model}::{$limiter}] is not defined.");
    }
}
