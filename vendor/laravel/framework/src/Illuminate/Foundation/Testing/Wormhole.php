<?php

namespace Illuminate\Foundation\Testing;

use Illuminate\Support\Carbon;

class Wormhole
{
    /**
     * The amount of time to travel.
     *
     * @var int
     */
    public $value;

    /**
     * Create a new wormhole instance.
     *
     * @param  int  $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of microseconds.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function microsecond($callback = null)
    {
        return $this->microseconds($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of microseconds.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function microseconds($callback = null)
    {
        Carbon::setTestNow(Carbon::now()->addMicroseconds($this->value));

        return $this->handleCallback($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of milliseconds.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function millisecond($callback = null)
    {
        return $this->milliseconds($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of milliseconds.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function milliseconds($callback = null)
    {
        Carbon::setTestNow(Carbon::now()->addMilliseconds($this->value));

        return $this->handleCallback($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of seconds.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function second($callback = null)
    {
        return $this->seconds($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of seconds.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function seconds($callback = null)
    {
        Carbon::setTestNow(Carbon::now()->addSeconds($this->value));

        return $this->handleCallback($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of minutes.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function minute($callback = null)
    {
        return $this->minutes($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of minutes.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function minutes($callback = null)
    {
        Carbon::setTestNow(Carbon::now()->addMinutes($this->value));

        return $this->handleCallback($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of hours.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function hour($callback = null)
    {
        return $this->hours($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of hours.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function hours($callback = null)
    {
        Carbon::setTestNow(Carbon::now()->addHours($this->value));

        return $this->handleCallback($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of days.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function day($callback = null)
    {
        return $this->days($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of days.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function days($callback = null)
    {
        Carbon::setTestNow(Carbon::now()->addDays($this->value));

        return $this->handleCallback($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of weeks.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function week($callback = null)
    {
        return $this->weeks($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of weeks.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function weeks($callback = null)
    {
        Carbon::setTestNow(Carbon::now()->addWeeks($this->value));

        return $this->handleCallback($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of months.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function month($callback = null)
    {
        return $this->months($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of months.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function months($callback = null)
    {
        Carbon::setTestNow(Carbon::now()->addMonths($this->value));

        return $this->handleCallback($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of years.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function year($callback = null)
    {
        return $this->years($callback);
    }

    /**
     * @template TReturn of mixed
     *
     * Travel forward the given number of years.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function years($callback = null)
    {
        Carbon::setTestNow(Carbon::now()->addYears($this->value));

        return $this->handleCallback($callback);
    }

    /**
     * Travel back to the current time.
     *
     * @return \DateTimeInterface
     */
    public static function back()
    {
        Carbon::setTestNow();

        return Carbon::now();
    }

    /**
     * @template TReturn of mixed
     *
     * Handle the given optional execution callback.
     *
     * @param  (callable(): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    protected function handleCallback($callback)
    {
        if ($callback) {
            return tap($callback(), function () {
                Carbon::setTestNow();
            });
        }
    }
}
