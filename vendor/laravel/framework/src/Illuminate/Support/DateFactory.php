<?php

namespace Illuminate\Support;

use Carbon\Factory;
use InvalidArgumentException;

/**
 * @see https://carbon.nesbot.com/docs/
 * @see https://github.com/briannesbitt/Carbon/blob/master/src/Carbon/Factory.php
 *
 * @method bool canBeCreatedFromFormat(?string $date, string $format)
 * @method \Illuminate\Support\Carbon|null create($year = 0, $month = 1, $day = 1, $hour = 0, $minute = 0, $second = 0, $timezone = null)
 * @method \Illuminate\Support\Carbon createFromDate($year = null, $month = null, $day = null, $timezone = null)
 * @method \Illuminate\Support\Carbon|null createFromFormat($format, $time, $timezone = null)
 * @method \Illuminate\Support\Carbon|null createFromIsoFormat(string $format, string $time, $timezone = null, ?string $locale = 'en', ?\Symfony\Contracts\Translation\TranslatorInterface $translator = null)
 * @method \Illuminate\Support\Carbon|null createFromLocaleFormat(string $format, string $locale, string $time, $timezone = null)
 * @method \Illuminate\Support\Carbon|null createFromLocaleIsoFormat(string $format, string $locale, string $time, $timezone = null)
 * @method \Illuminate\Support\Carbon createFromTime($hour = 0, $minute = 0, $second = 0, $timezone = null)
 * @method \Illuminate\Support\Carbon createFromTimeString(string $time, \DateTimeZone|string|int|null $timezone = null)
 * @method \Illuminate\Support\Carbon createFromTimestamp(string|int|float $timestamp, \DateTimeZone|string|int|null $timezone = null)
 * @method \Illuminate\Support\Carbon createFromTimestampMs(string|int|float $timestamp, \DateTimeZone|string|int|null $timezone = null)
 * @method \Illuminate\Support\Carbon createFromTimestampMsUTC($timestamp)
 * @method \Illuminate\Support\Carbon createFromTimestampUTC(string|int|float $timestamp)
 * @method \Illuminate\Support\Carbon createMidnightDate($year = null, $month = null, $day = null, $timezone = null)
 * @method \Illuminate\Support\Carbon|null createSafe($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null, $timezone = null)
 * @method \Illuminate\Support\Carbon createStrict(?int $year = 0, ?int $month = 1, ?int $day = 1, ?int $hour = 0, ?int $minute = 0, ?int $second = 0, $timezone = null)
 * @method void disableHumanDiffOption($humanDiffOption)
 * @method void enableHumanDiffOption($humanDiffOption)
 * @method mixed executeWithLocale(string $locale, callable $func)
 * @method \Illuminate\Support\Carbon fromSerialized($value)
 * @method array getAvailableLocales()
 * @method array getAvailableLocalesInfo()
 * @method array getDays()
 * @method ?string getFallbackLocale()
 * @method array getFormatsToIsoReplacements()
 * @method int getHumanDiffOptions()
 * @method array getIsoUnits()
 * @method array|false getLastErrors()
 * @method string getLocale()
 * @method int getMidDayAt()
 * @method string getTimeFormatByPrecision(string $unitPrecision)
 * @method string|\Closure|null getTranslationMessageWith($translator, string $key, ?string $locale = null, ?string $default = null)
 * @method \Illuminate\Support\Carbon|null getTestNow()
 * @method \Symfony\Contracts\Translation\TranslatorInterface getTranslator()
 * @method int getWeekEndsAt(?string $locale = null)
 * @method int getWeekStartsAt(?string $locale = null)
 * @method array getWeekendDays()
 * @method bool hasFormat(string $date, string $format)
 * @method bool hasFormatWithModifiers(string $date, string $format)
 * @method bool hasMacro($name)
 * @method bool hasRelativeKeywords(?string $time)
 * @method bool hasTestNow()
 * @method \Illuminate\Support\Carbon instance(\DateTimeInterface $date)
 * @method bool isImmutable()
 * @method bool isModifiableUnit($unit)
 * @method bool isMutable()
 * @method bool isStrictModeEnabled()
 * @method bool localeHasDiffOneDayWords(string $locale)
 * @method bool localeHasDiffSyntax(string $locale)
 * @method bool localeHasDiffTwoDayWords(string $locale)
 * @method bool localeHasPeriodSyntax($locale)
 * @method bool localeHasShortUnits(string $locale)
 * @method void macro(string $name, ?callable $macro)
 * @method \Illuminate\Support\Carbon|null make($var, \DateTimeZone|string|null $timezone = null)
 * @method void mixin(object|string $mixin)
 * @method \Illuminate\Support\Carbon now(\DateTimeZone|string|int|null $timezone = null)
 * @method \Illuminate\Support\Carbon parse(\DateTimeInterface|\Carbon\WeekDay|\Carbon\Month|string|int|float|null $time, \DateTimeZone|string|int|null $timezone = null)
 * @method \Illuminate\Support\Carbon parseFromLocale(string $time, ?string $locale = null, \DateTimeZone|string|int|null $timezone = null)
 * @method string pluralUnit(string $unit)
 * @method \Illuminate\Support\Carbon|null rawCreateFromFormat(string $format, string $time, $timezone = null)
 * @method \Illuminate\Support\Carbon rawParse(\DateTimeInterface|\Carbon\WeekDay|\Carbon\Month|string|int|float|null $time, \DateTimeZone|string|int|null $timezone = null)
 * @method void resetMonthsOverflow()
 * @method void resetToStringFormat()
 * @method void resetYearsOverflow()
 * @method void serializeUsing($callback)
 * @method void setFallbackLocale(string $locale)
 * @method void setHumanDiffOptions($humanDiffOptions)
 * @method void setLocale(string $locale)
 * @method void setMidDayAt($hour)
 * @method void setTestNow(mixed $testNow = null)
 * @method void setTestNowAndTimezone(mixed $testNow = null, $timezone = null)
 * @method void setToStringFormat(string|\Closure|null $format)
 * @method void setTranslator(\Symfony\Contracts\Translation\TranslatorInterface $translator)
 * @method void setWeekEndsAt($day)
 * @method void setWeekStartsAt($day)
 * @method void setWeekendDays($days)
 * @method bool shouldOverflowMonths()
 * @method bool shouldOverflowYears()
 * @method string singularUnit(string $unit)
 * @method void sleep(int|float $seconds)
 * @method \Illuminate\Support\Carbon today(\DateTimeZone|string|int|null $timezone = null)
 * @method \Illuminate\Support\Carbon tomorrow(\DateTimeZone|string|int|null $timezone = null)
 * @method string translateTimeString(string $timeString, ?string $from = null, ?string $to = null, int $mode = \Carbon\CarbonInterface::TRANSLATE_ALL)
 * @method string translateWith(\Symfony\Contracts\Translation\TranslatorInterface $translator, string $key, array $parameters = [], $number = null)
 * @method void useMonthsOverflow($monthsOverflow = true)
 * @method void useStrictMode($strictModeEnabled = true)
 * @method void useYearsOverflow($yearsOverflow = true)
 * @method mixed withTestNow(mixed $testNow, callable $callback)
 * @method static withTimeZone(\DateTimeZone|string|int|null $timezone)
 * @method \Illuminate\Support\Carbon yesterday(\DateTimeZone|string|int|null $timezone = null)
 */
class DateFactory
{
    /**
     * The default class that will be used for all created dates.
     *
     * @var string
     */
    const DEFAULT_CLASS_NAME = Carbon::class;

    /**
     * The type (class) of dates that should be created.
     *
     * @var string
     */
    protected static $dateClass;

    /**
     * This callable may be used to intercept date creation.
     *
     * @var callable
     */
    protected static $callable;

    /**
     * The Carbon factory that should be used when creating dates.
     *
     * @var object
     */
    protected static $factory;

    /**
     * Use the given handler when generating dates (class name, callable, or factory).
     *
     * @param  mixed  $handler
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function use($handler)
    {
        if (is_callable($handler) && is_object($handler)) {
            return static::useCallable($handler);
        } elseif (is_string($handler)) {
            return static::useClass($handler);
        } elseif ($handler instanceof Factory) {
            return static::useFactory($handler);
        }

        throw new InvalidArgumentException('Invalid date creation handler. Please provide a class name, callable, or Carbon factory.');
    }

    /**
     * Use the default date class when generating dates.
     *
     * @return void
     */
    public static function useDefault()
    {
        static::$dateClass = null;
        static::$callable = null;
        static::$factory = null;
    }

    /**
     * Execute the given callable on each date creation.
     *
     * @param  callable  $callable
     * @return void
     */
    public static function useCallable(callable $callable)
    {
        static::$callable = $callable;

        static::$dateClass = null;
        static::$factory = null;
    }

    /**
     * Use the given date type (class) when generating dates.
     *
     * @param  string  $dateClass
     * @return void
     */
    public static function useClass($dateClass)
    {
        static::$dateClass = $dateClass;

        static::$factory = null;
        static::$callable = null;
    }

    /**
     * Use the given Carbon factory when generating dates.
     *
     * @param  object  $factory
     * @return void
     */
    public static function useFactory($factory)
    {
        static::$factory = $factory;

        static::$dateClass = null;
        static::$callable = null;
    }

    /**
     * Handle dynamic calls to generate dates.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function __call($method, $parameters)
    {
        $defaultClassName = static::DEFAULT_CLASS_NAME;

        // Using callable to generate dates...
        if (static::$callable) {
            return call_user_func(static::$callable, $defaultClassName::$method(...$parameters));
        }

        // Using Carbon factory to generate dates...
        if (static::$factory) {
            return static::$factory->$method(...$parameters);
        }

        $dateClass = static::$dateClass ?: $defaultClassName;

        // Check if the date can be created using the public class method...
        if (method_exists($dateClass, $method) ||
            method_exists($dateClass, 'hasMacro') && $dateClass::hasMacro($method)) {
            return $dateClass::$method(...$parameters);
        }

        // If that fails, create the date with the default class...
        $date = $defaultClassName::$method(...$parameters);

        // If the configured class has an "instance" method, we'll try to pass our date into there...
        if (method_exists($dateClass, 'instance')) {
            return $dateClass::instance($date);
        }

        // Otherwise, assume the configured class has a DateTime compatible constructor...
        return new $dateClass($date->format('Y-m-d H:i:s.u'), $date->getTimezone());
    }
}
