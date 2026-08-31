<?php

namespace Illuminate\Support;

use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Illuminate\Support\Defer\DeferredCallback;
use Illuminate\Support\Defer\DeferredCallbackCollection;
use Illuminate\Support\Facades\Date;
use Symfony\Component\Process\PhpExecutableFinder;

if (! function_exists('Illuminate\Support\defer')) {
    /**
     * Defer execution of the given callback.
     *
     * @param  callable|null  $callback
     * @param  string|null  $name
     * @param  bool  $always
     * @return ($callback is null ? \Illuminate\Support\Defer\DeferredCallbackCollection : \Illuminate\Support\Defer\DeferredCallback)
     */
    function defer(?callable $callback = null, ?string $name = null, bool $always = false): DeferredCallback|DeferredCallbackCollection
    {
        if ($callback === null) {
            return app(DeferredCallbackCollection::class);
        }

        return tap(
            new DeferredCallback($callback, $name, $always),
            fn ($deferred) => app(DeferredCallbackCollection::class)[] = $deferred
        );
    }
}

if (! function_exists('Illuminate\Support\php_binary')) {
    /**
     * Determine the PHP Binary.
     */
    function php_binary(): string
    {
        return (new PhpExecutableFinder)->find(false) ?: 'php';
    }
}

if (! function_exists('Illuminate\Support\artisan_binary')) {
    /**
     * Determine the proper Artisan executable.
     */
    function artisan_binary(): string
    {
        return defined('ARTISAN_BINARY') ? ARTISAN_BINARY : 'artisan';
    }
}

// Time functions...

if (! function_exists('Illuminate\Support\now')) {
    /**
     * Create a new Carbon instance for the current time.
     *
     * @param  \DateTimeZone|\UnitEnum|string|null  $tz
     * @return \Illuminate\Support\Carbon
     */
    function now($tz = null): CarbonInterface
    {
        return Date::now(enum_value($tz));
    }
}

if (! function_exists('Illuminate\Support\microseconds')) {
    /**
     * Get the current date / time plus the given number of microseconds.
     */
    function microseconds(int|float $microseconds): CarbonInterval
    {
        return CarbonInterval::microseconds($microseconds);
    }
}

if (! function_exists('Illuminate\Support\milliseconds')) {
    /**
     * Get the current date / time plus the given number of milliseconds.
     */
    function milliseconds(int|float $milliseconds): CarbonInterval
    {
        return CarbonInterval::milliseconds($milliseconds);
    }
}

if (! function_exists('Illuminate\Support\seconds')) {
    /**
     * Get the current date / time plus the given number of seconds.
     */
    function seconds(int|float $seconds): CarbonInterval
    {
        return CarbonInterval::seconds($seconds);
    }
}

if (! function_exists('Illuminate\Support\minutes')) {
    /**
     * Get the current date / time plus the given number of minutes.
     */
    function minutes(int|float $minutes): CarbonInterval
    {
        return CarbonInterval::minutes($minutes);
    }
}

if (! function_exists('Illuminate\Support\hours')) {
    /**
     * Get the current date / time plus the given number of hours.
     */
    function hours(int|float $hours): CarbonInterval
    {
        return CarbonInterval::hours($hours);
    }
}

if (! function_exists('Illuminate\Support\days')) {
    /**
     * Get the current date / time plus the given number of days.
     */
    function days(int|float $days): CarbonInterval
    {
        return CarbonInterval::days($days);
    }
}

if (! function_exists('Illuminate\Support\weeks')) {
    /**
     * Get the current date / time plus the given number of weeks.
     */
    function weeks(int $weeks): CarbonInterval
    {
        return CarbonInterval::weeks($weeks);
    }
}

if (! function_exists('Illuminate\Support\months')) {
    /**
     * Get the current date / time plus the given number of months.
     */
    function months(int $months): CarbonInterval
    {
        return CarbonInterval::months($months);
    }
}

if (! function_exists('Illuminate\Support\years')) {
    /**
     * Get the current date / time plus the given number of years.
     */
    function years(int $years): CarbonInterval
    {
        return CarbonInterval::years($years);
    }
}
