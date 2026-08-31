<?php

namespace Illuminate\Cache\RateLimiting;

class Limit
{
    /**
     * The rate limit signature key.
     *
     * @var mixed
     */
    public $key;

    /**
     * The maximum number of attempts allowed within the given number of seconds.
     *
     * @var int
     */
    public $maxAttempts;

    /**
     * The number of seconds until the rate limit is reset.
     *
     * @var int
     */
    public $decaySeconds;

    /**
     * The after callback used to determine if the limiter should be hit.
     *
     * @var ?callable
     */
    public $afterCallback = null;

    /**
     * The response generator callback.
     *
     * @var callable
     */
    public $responseCallback;

    /**
     * Create a new limit instance.
     *
     * @param  mixed  $key
     * @param  int  $maxAttempts
     * @param  int  $decaySeconds
     */
    public function __construct($key = '', int $maxAttempts = 60, int $decaySeconds = 60)
    {
        $this->key = $key;
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
    }

    /**
     * Create a new rate limit.
     *
     * @param  int  $maxAttempts
     * @param  int  $decaySeconds
     * @return static
     */
    public static function perSecond($maxAttempts, $decaySeconds = 1)
    {
        return new static('', $maxAttempts, $decaySeconds);
    }

    /**
     * Create a new rate limit.
     *
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return static
     */
    public static function perMinute($maxAttempts, $decayMinutes = 1)
    {
        return new static('', $maxAttempts, 60 * $decayMinutes);
    }

    /**
     * Create a new rate limit using minutes as decay time.
     *
     * @param  int  $decayMinutes
     * @param  int  $maxAttempts
     * @return static
     */
    public static function perMinutes($decayMinutes, $maxAttempts)
    {
        return new static('', $maxAttempts, 60 * $decayMinutes);
    }

    /**
     * Create a new rate limit using hours as decay time.
     *
     * @param  int  $maxAttempts
     * @param  int  $decayHours
     * @return static
     */
    public static function perHour($maxAttempts, $decayHours = 1)
    {
        return new static('', $maxAttempts, 60 * 60 * $decayHours);
    }

    /**
     * Create a new rate limit using days as decay time.
     *
     * @param  int  $maxAttempts
     * @param  int  $decayDays
     * @return static
     */
    public static function perDay($maxAttempts, $decayDays = 1)
    {
        return new static('', $maxAttempts, 60 * 60 * 24 * $decayDays);
    }

    /**
     * Create a new unlimited rate limit.
     *
     * @return static
     */
    public static function none()
    {
        return new Unlimited;
    }

    /**
     * Set the key of the rate limit.
     *
     * @param  mixed  $key
     * @return $this
     */
    public function by($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Set the callback to determine if the limiter should be hit.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function after($callback)
    {
        $this->afterCallback = $callback;

        return $this;
    }

    /**
     * Set the callback that should generate the response when the limit is exceeded.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function response(callable $callback)
    {
        $this->responseCallback = $callback;

        return $this;
    }

    /**
     * Get a potential fallback key for the limit.
     *
     * @return string
     */
    public function fallbackKey()
    {
        $prefix = $this->key ? "{$this->key}:" : '';

        return "{$prefix}attempts:{$this->maxAttempts}:decay:{$this->decaySeconds}";
    }
}
