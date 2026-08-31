<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon;

use Carbon\Constants\UnitValue;
use Carbon\Exceptions\EndLessPeriodException;
use Carbon\Exceptions\InvalidCastException;
use Carbon\Exceptions\InvalidIntervalException;
use Carbon\Exceptions\InvalidPeriodDateException;
use Carbon\Exceptions\InvalidPeriodParameterException;
use Carbon\Exceptions\NotACarbonClassException;
use Carbon\Exceptions\NotAPeriodException;
use Carbon\Exceptions\UnknownGetterException;
use Carbon\Exceptions\UnknownMethodException;
use Carbon\Exceptions\UnreachableException;
use Carbon\Traits\DeprecatedPeriodProperties;
use Carbon\Traits\IntervalRounding;
use Carbon\Traits\LocalFactory;
use Carbon\Traits\Mixin;
use Carbon\Traits\Options;
use Carbon\Traits\ToStringFormat;
use Closure;
use Countable;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Generator;
use InvalidArgumentException;
use JsonSerializable;
use ReflectionException;
use ReturnTypeWillChange;
use RuntimeException;
use Throwable;

// @codeCoverageIgnoreStart
require PHP_VERSION < 8.2
    ? __DIR__.'/../../lazy/Carbon/ProtectedDatePeriod.php'
    : __DIR__.'/../../lazy/Carbon/UnprotectedDatePeriod.php';
// @codeCoverageIgnoreEnd

/**
 * Substitution of DatePeriod with some modifications and many more features.
 *
 * @method static static|CarbonInterface start($date = null, $inclusive = null) Create instance specifying start date or modify the start date if called on an instance.
 * @method static static since($date = null, $inclusive = null) Alias for start().
 * @method static static sinceNow($inclusive = null) Create instance with start date set to now or set the start date to now if called on an instance.
 * @method static static|CarbonInterface end($date = null, $inclusive = null) Create instance specifying end date or modify the end date if called on an instance.
 * @method static static until($date = null, $inclusive = null) Alias for end().
 * @method static static untilNow($inclusive = null) Create instance with end date set to now or set the end date to now if called on an instance.
 * @method static static dates($start, $end = null) Create instance with start and end dates or modify the start and end dates if called on an instance.
 * @method static static between($start, $end = null) Create instance with start and end dates or modify the start and end dates if called on an instance.
 * @method static static recurrences($recurrences = null) Create instance with maximum number of recurrences or modify the number of recurrences if called on an instance.
 * @method static static times($recurrences = null) Alias for recurrences().
 * @method static static|int|null options($options = null) Create instance with options or modify the options if called on an instance.
 * @method static static toggle($options, $state = null) Create instance with options toggled on or off, or toggle options if called on an instance.
 * @method static static filter($callback, $name = null) Create instance with filter added to the stack or append a filter if called on an instance.
 * @method static static push($callback, $name = null) Alias for filter().
 * @method static static prepend($callback, $name = null) Create instance with filter prepended to the stack or prepend a filter if called on an instance.
 * @method static static|array filters(array $filters = []) Create instance with filters stack or replace the whole filters stack if called on an instance.
 * @method static static|CarbonInterval interval($interval = null) Create instance with given date interval or modify the interval if called on an instance.
 * @method static static each($interval) Create instance with given date interval or modify the interval if called on an instance.
 * @method static static every($interval) Create instance with given date interval or modify the interval if called on an instance.
 * @method static static step($interval) Create instance with given date interval or modify the interval if called on an instance.
 * @method static static stepBy($interval) Create instance with given date interval or modify the interval if called on an instance.
 * @method static static invert() Create instance with inverted date interval or invert the interval if called on an instance.
 * @method static static years($years = 1) Create instance specifying a number of years for date interval or replace the interval by the given a number of years if called on an instance.
 * @method static static year($years = 1) Alias for years().
 * @method static static months($months = 1) Create instance specifying a number of months for date interval or replace the interval by the given a number of months if called on an instance.
 * @method static static month($months = 1) Alias for months().
 * @method static static weeks($weeks = 1) Create instance specifying a number of weeks for date interval or replace the interval by the given a number of weeks if called on an instance.
 * @method static static week($weeks = 1) Alias for weeks().
 * @method static static days($days = 1) Create instance specifying a number of days for date interval or replace the interval by the given a number of days if called on an instance.
 * @method static static dayz($days = 1) Alias for days().
 * @method static static day($days = 1) Alias for days().
 * @method static static hours($hours = 1) Create instance specifying a number of hours for date interval or replace the interval by the given a number of hours if called on an instance.
 * @method static static hour($hours = 1) Alias for hours().
 * @method static static minutes($minutes = 1) Create instance specifying a number of minutes for date interval or replace the interval by the given a number of minutes if called on an instance.
 * @method static static minute($minutes = 1) Alias for minutes().
 * @method static static seconds($seconds = 1) Create instance specifying a number of seconds for date interval or replace the interval by the given a number of seconds if called on an instance.
 * @method static static second($seconds = 1) Alias for seconds().
 * @method static static milliseconds($milliseconds = 1) Create instance specifying a number of milliseconds for date interval or replace the interval by the given a number of milliseconds if called on an instance.
 * @method static static millisecond($milliseconds = 1) Alias for milliseconds().
 * @method static static microseconds($microseconds = 1) Create instance specifying a number of microseconds for date interval or replace the interval by the given a number of microseconds if called on an instance.
 * @method static static microsecond($microseconds = 1) Alias for microseconds().
 * @method $this roundYear(float $precision = 1, string $function = "round") Round the current instance year with given precision using the given function.
 * @method $this roundYears(float $precision = 1, string $function = "round") Round the current instance year with given precision using the given function.
 * @method $this floorYear(float $precision = 1) Truncate the current instance year with given precision.
 * @method $this floorYears(float $precision = 1) Truncate the current instance year with given precision.
 * @method $this ceilYear(float $precision = 1) Ceil the current instance year with given precision.
 * @method $this ceilYears(float $precision = 1) Ceil the current instance year with given precision.
 * @method $this roundMonth(float $precision = 1, string $function = "round") Round the current instance month with given precision using the given function.
 * @method $this roundMonths(float $precision = 1, string $function = "round") Round the current instance month with given precision using the given function.
 * @method $this floorMonth(float $precision = 1) Truncate the current instance month with given precision.
 * @method $this floorMonths(float $precision = 1) Truncate the current instance month with given precision.
 * @method $this ceilMonth(float $precision = 1) Ceil the current instance month with given precision.
 * @method $this ceilMonths(float $precision = 1) Ceil the current instance month with given precision.
 * @method $this roundWeek(float $precision = 1, string $function = "round") Round the current instance day with given precision using the given function.
 * @method $this roundWeeks(float $precision = 1, string $function = "round") Round the current instance day with given precision using the given function.
 * @method $this floorWeek(float $precision = 1) Truncate the current instance day with given precision.
 * @method $this floorWeeks(float $precision = 1) Truncate the current instance day with given precision.
 * @method $this ceilWeek(float $precision = 1) Ceil the current instance day with given precision.
 * @method $this ceilWeeks(float $precision = 1) Ceil the current instance day with given precision.
 * @method $this roundDay(float $precision = 1, string $function = "round") Round the current instance day with given precision using the given function.
 * @method $this roundDays(float $precision = 1, string $function = "round") Round the current instance day with given precision using the given function.
 * @method $this floorDay(float $precision = 1) Truncate the current instance day with given precision.
 * @method $this floorDays(float $precision = 1) Truncate the current instance day with given precision.
 * @method $this ceilDay(float $precision = 1) Ceil the current instance day with given precision.
 * @method $this ceilDays(float $precision = 1) Ceil the current instance day with given precision.
 * @method $this roundHour(float $precision = 1, string $function = "round") Round the current instance hour with given precision using the given function.
 * @method $this roundHours(float $precision = 1, string $function = "round") Round the current instance hour with given precision using the given function.
 * @method $this floorHour(float $precision = 1) Truncate the current instance hour with given precision.
 * @method $this floorHours(float $precision = 1) Truncate the current instance hour with given precision.
 * @method $this ceilHour(float $precision = 1) Ceil the current instance hour with given precision.
 * @method $this ceilHours(float $precision = 1) Ceil the current instance hour with given precision.
 * @method $this roundMinute(float $precision = 1, string $function = "round") Round the current instance minute with given precision using the given function.
 * @method $this roundMinutes(float $precision = 1, string $function = "round") Round the current instance minute with given precision using the given function.
 * @method $this floorMinute(float $precision = 1) Truncate the current instance minute with given precision.
 * @method $this floorMinutes(float $precision = 1) Truncate the current instance minute with given precision.
 * @method $this ceilMinute(float $precision = 1) Ceil the current instance minute with given precision.
 * @method $this ceilMinutes(float $precision = 1) Ceil the current instance minute with given precision.
 * @method $this roundSecond(float $precision = 1, string $function = "round") Round the current instance second with given precision using the given function.
 * @method $this roundSeconds(float $precision = 1, string $function = "round") Round the current instance second with given precision using the given function.
 * @method $this floorSecond(float $precision = 1) Truncate the current instance second with given precision.
 * @method $this floorSeconds(float $precision = 1) Truncate the current instance second with given precision.
 * @method $this ceilSecond(float $precision = 1) Ceil the current instance second with given precision.
 * @method $this ceilSeconds(float $precision = 1) Ceil the current instance second with given precision.
 * @method $this roundMillennium(float $precision = 1, string $function = "round") Round the current instance millennium with given precision using the given function.
 * @method $this roundMillennia(float $precision = 1, string $function = "round") Round the current instance millennium with given precision using the given function.
 * @method $this floorMillennium(float $precision = 1) Truncate the current instance millennium with given precision.
 * @method $this floorMillennia(float $precision = 1) Truncate the current instance millennium with given precision.
 * @method $this ceilMillennium(float $precision = 1) Ceil the current instance millennium with given precision.
 * @method $this ceilMillennia(float $precision = 1) Ceil the current instance millennium with given precision.
 * @method $this roundCentury(float $precision = 1, string $function = "round") Round the current instance century with given precision using the given function.
 * @method $this roundCenturies(float $precision = 1, string $function = "round") Round the current instance century with given precision using the given function.
 * @method $this floorCentury(float $precision = 1) Truncate the current instance century with given precision.
 * @method $this floorCenturies(float $precision = 1) Truncate the current instance century with given precision.
 * @method $this ceilCentury(float $precision = 1) Ceil the current instance century with given precision.
 * @method $this ceilCenturies(float $precision = 1) Ceil the current instance century with given precision.
 * @method $this roundDecade(float $precision = 1, string $function = "round") Round the current instance decade with given precision using the given function.
 * @method $this roundDecades(float $precision = 1, string $function = "round") Round the current instance decade with given precision using the given function.
 * @method $this floorDecade(float $precision = 1) Truncate the current instance decade with given precision.
 * @method $this floorDecades(float $precision = 1) Truncate the current instance decade with given precision.
 * @method $this ceilDecade(float $precision = 1) Ceil the current instance decade with given precision.
 * @method $this ceilDecades(float $precision = 1) Ceil the current instance decade with given precision.
 * @method $this roundQuarter(float $precision = 1, string $function = "round") Round the current instance quarter with given precision using the given function.
 * @method $this roundQuarters(float $precision = 1, string $function = "round") Round the current instance quarter with given precision using the given function.
 * @method $this floorQuarter(float $precision = 1) Truncate the current instance quarter with given precision.
 * @method $this floorQuarters(float $precision = 1) Truncate the current instance quarter with given precision.
 * @method $this ceilQuarter(float $precision = 1) Ceil the current instance quarter with given precision.
 * @method $this ceilQuarters(float $precision = 1) Ceil the current instance quarter with given precision.
 * @method $this roundMillisecond(float $precision = 1, string $function = "round") Round the current instance millisecond with given precision using the given function.
 * @method $this roundMilliseconds(float $precision = 1, string $function = "round") Round the current instance millisecond with given precision using the given function.
 * @method $this floorMillisecond(float $precision = 1) Truncate the current instance millisecond with given precision.
 * @method $this floorMilliseconds(float $precision = 1) Truncate the current instance millisecond with given precision.
 * @method $this ceilMillisecond(float $precision = 1) Ceil the current instance millisecond with given precision.
 * @method $this ceilMilliseconds(float $precision = 1) Ceil the current instance millisecond with given precision.
 * @method $this roundMicrosecond(float $precision = 1, string $function = "round") Round the current instance microsecond with given precision using the given function.
 * @method $this roundMicroseconds(float $precision = 1, string $function = "round") Round the current instance microsecond with given precision using the given function.
 * @method $this floorMicrosecond(float $precision = 1) Truncate the current instance microsecond with given precision.
 * @method $this floorMicroseconds(float $precision = 1) Truncate the current instance microsecond with given precision.
 * @method $this ceilMicrosecond(float $precision = 1) Ceil the current instance microsecond with given precision.
 * @method $this ceilMicroseconds(float $precision = 1) Ceil the current instance microsecond with given precision.
 *
 * @mixin DeprecatedPeriodProperties
 *
 * @SuppressWarnings(TooManyFields)
 * @SuppressWarnings(CamelCasePropertyName)
 * @SuppressWarnings(CouplingBetweenObjects)
 */
class CarbonPeriod extends DatePeriodBase implements Countable, JsonSerializable, UnitValue
{
    use LocalFactory;
    use IntervalRounding;
    use Mixin {
        Mixin::mixin as baseMixin;
    }
    use Options {
        Options::__debugInfo as baseDebugInfo;
    }
    use ToStringFormat;

    /**
     * Built-in filter for limit by recurrences.
     *
     * @var callable
     */
    public const RECURRENCES_FILTER = [self::class, 'filterRecurrences'];

    /**
     * Built-in filter for limit to an end.
     *
     * @var callable
     */
    public const END_DATE_FILTER = [self::class, 'filterEndDate'];

    /**
     * Special value which can be returned by filters to end iteration. Also a filter.
     *
     * @var callable
     */
    public const END_ITERATION = [self::class, 'endIteration'];

    /**
     * Exclude end date from iteration.
     *
     * @var int
     */
    public const EXCLUDE_END_DATE = 8;

    /**
     * Yield CarbonImmutable instances.
     *
     * @var int
     */
    public const IMMUTABLE = 4;

    /**
     * Number of maximum attempts before giving up on finding next valid date.
     *
     * @var int
     */
    public const NEXT_MAX_ATTEMPTS = 1000;

    /**
     * Number of maximum attempts before giving up on finding end date.
     *
     * @var int
     */
    public const END_MAX_ATTEMPTS = 10000;

    /**
     * Default date class of iteration items.
     *
     * @var string
     */
    protected const DEFAULT_DATE_CLASS = Carbon::class;

    /**
     * The registered macros.
     */
    protected static array $macros = [];

    /**
     * Date class of iteration items.
     */
    protected string $dateClass = Carbon::class;

    /**
     * Underlying date interval instance. Always present, one day by default.
     */
    protected ?CarbonInterval $dateInterval = null;

    /**
     * True once __construct is finished.
     */
    protected bool $constructed = false;

    /**
     * Whether current date interval was set by default.
     */
    protected bool $isDefaultInterval = false;

    /**
     * The filters stack.
     */
    protected array $filters = [];

    /**
     * Period start date. Applied on rewind. Always present, now by default.
     */
    protected ?CarbonInterface $startDate = null;

    /**
     * Period end date. For inverted interval should be before the start date. Applied via a filter.
     */
    protected ?CarbonInterface $endDate = null;

    /**
     * Limit for number of recurrences. Applied via a filter.
     */
    protected int|float|null $carbonRecurrences = null;

    /**
     * Iteration options.
     */
    protected ?int $options = null;

    /**
     * Index of current date. Always sequential, even if some dates are skipped by filters.
     * Equal to null only before the first iteration.
     */
    protected int $key = 0;

    /**
     * Current date. May temporarily hold unaccepted value when looking for a next valid date.
     * Equal to null only before the first iteration.
     */
    protected ?CarbonInterface $carbonCurrent = null;

    /**
     * Timezone of current date. Taken from the start date.
     */
    protected ?DateTimeZone $timezone = null;

    /**
     * The cached validation result for current date.
     */
    protected array|string|bool|null $validationResult = null;

    /**
     * Timezone handler for settings() method.
     */
    protected DateTimeZone|string|int|null $timezoneSetting = null;

    public function getIterator(): Generator
    {
        $this->rewind();

        while ($this->valid()) {
            $key = $this->key();
            $value = $this->current();

            yield $key => $value;

            $this->next();
        }
    }

    /**
     * Make a CarbonPeriod instance from given variable if possible.
     */
    public static function make(mixed $var): ?static
    {
        try {
            return static::instance($var);
        } catch (NotAPeriodException) {
            return static::create($var);
        }
    }

    /**
     * Create a new instance from a DatePeriod or CarbonPeriod object.
     */
    public static function instance(mixed $period): static
    {
        if ($period instanceof static) {
            return $period->copy();
        }

        if ($period instanceof self) {
            return new static(
                $period->getStartDate(),
                $period->getEndDate() ?? $period->getRecurrences(),
                $period->getDateInterval(),
                $period->getOptions(),
            );
        }

        if ($period instanceof DatePeriod) {
            return new static(
                $period->start,
                $period->end ?: ($period->recurrences - 1),
                $period->interval,
                $period->include_start_date ? 0 : static::EXCLUDE_START_DATE,
            );
        }

        $class = static::class;
        $type = \gettype($period);
        $chunks = explode('::', __METHOD__);

        throw new NotAPeriodException(
            'Argument 1 passed to '.$class.'::'.end($chunks).'() '.
            'must be an instance of DatePeriod or '.$class.', '.
            ($type === 'object' ? 'instance of '.\get_class($period) : $type).' given.',
        );
    }

    /**
     * Create a new instance.
     */
    public static function create(...$params): static
    {
        return static::createFromArray($params);
    }

    /**
     * Create a new instance from an array of parameters.
     */
    public static function createFromArray(array $params): static
    {
        return new static(...$params);
    }

    /**
     * Create CarbonPeriod from ISO 8601 string.
     */
    public static function createFromIso(string $iso, ?int $options = null): static
    {
        $params = static::parseIso8601($iso);

        $instance = static::createFromArray($params);

        $instance->options = ($instance instanceof CarbonPeriodImmutable ? static::IMMUTABLE : 0) | $options;
        $instance->handleChangedParameters();

        return $instance;
    }

    public static function createFromISO8601String(string $iso, ?int $options = null): static
    {
        return self::createFromIso($iso, $options);
    }

    public static function monthly(
        DateTimeInterface|string|int|null $start = null,
        DateTimeInterface|string|int|null $end = null,
        ?int $recurrences = null,
        ?int $anchorDay = null,
        OverflowMode $mode = OverflowMode::AnchorDay,
        ?int $options = null,
    ): static {
        [$start, $end] = self::getStartAndEndForCyclePeriod($start, $end, $recurrences, $anchorDay, $mode);

        return (new static(
            $start,
            self::getMonthInterval($start, $anchorDay, $mode),
            $end ?? $recurrences,
        ))->setOptions($options ?? self::IMMUTABLE);
    }

    public static function quarterly(
        DateTimeInterface|string|int|null $start = null,
        DateTimeInterface|string|int|null $end = null,
        ?int $recurrences = null,
        ?int $anchorDay = null,
        OverflowMode $mode = OverflowMode::AnchorDay,
        ?int $options = null,
    ): static {
        [$start, $end] = self::getStartAndEndForCyclePeriod($start, $end, $recurrences, $anchorDay, $mode);
        $interval = self::getMonthInterval($start, $anchorDay, $mode);

        return (new static(
            $start,
            static function (CarbonInterface $date, bool $negated) use ($interval): CarbonInterface {
                for ($i = 0; $i < CarbonInterface::MONTHS_PER_QUARTER; $i++) {
                    $date = $negated
                        ? $date->sub($interval)
                        : $date->add($interval);
                }

                return $date;
            },
            $end ?? $recurrences,
        ))->setOptions($options ?? self::IMMUTABLE);
    }

    public static function yearly(
        DateTimeInterface|string|int|null $start = null,
        DateTimeInterface|string|int|null $end = null,
        ?int $recurrences = null,
        ?int $anchorDay = null,
        OverflowMode $mode = OverflowMode::AnchorDay,
        ?int $options = null,
    ): static {
        [$start, $end] = self::getStartAndEndForCyclePeriod($start, $end, $recurrences, $anchorDay, $mode);

        return (new static(
            $start,
            match ($mode) {
                OverflowMode::AnchorDay => CarbonInterval::yearWithAnchorDay(
                    $anchorDay ?? $start->day,
                ),
                OverflowMode::NoOverflow => CarbonInterval::yearNoOverflow(),
                OverflowMode::Overflow => CarbonInterval::month(),
            },
            $end ?? $recurrences,
        ))->setOptions($options ?? self::IMMUTABLE);
    }

    /**
     * Return whether the given interval contains non-zero value of any time unit.
     */
    protected static function intervalHasTime(DateInterval $interval): bool
    {
        return $interval->h || $interval->i || $interval->s || $interval->f;
    }

    /**
     * Return whether given variable is an ISO 8601 specification.
     *
     * Note: Check is very basic, as actual validation will be done later when parsing.
     * We just want to ensure that variable is not any other type of valid parameter.
     */
    protected static function isIso8601(mixed $var): bool
    {
        if (!\is_string($var)) {
            return false;
        }

        // Match slash but not within a timezone name.
        $part = '[a-z]+(?:[_-][a-z]+)*';

        preg_match("#\b$part/$part\b|(/)#i", $var, $match);

        return isset($match[1]);
    }

    /**
     * Parse given ISO 8601 string into an array of arguments.
     *
     * @SuppressWarnings(ElseExpression)
     */
    protected static function parseIso8601(string $iso): array
    {
        $result = [];

        $interval = null;
        $start = null;
        $end = null;
        $dateClass = static::DEFAULT_DATE_CLASS;

        foreach (explode('/', $iso) as $key => $part) {
            if ($key === 0 && preg_match('/^R(\d*|INF)$/', $part, $match)) {
                $parsed = \strlen($match[1]) ? (($match[1] !== 'INF') ? (int) $match[1] : INF) : null;
            } elseif ($interval === null && $parsed = self::makeInterval($part)) {
                $interval = $part;
            } elseif ($start === null && $parsed = $dateClass::make($part)) {
                $start = $part;
            } elseif ($end === null && $parsed = $dateClass::make(static::addMissingParts($start ?? '', $part))) {
                $end = $part;
            } else {
                throw new InvalidPeriodParameterException("Invalid ISO 8601 specification: $iso.");
            }

            $result[] = $parsed;
        }

        return $result;
    }

    /**
     * Add missing parts of the target date from the source date.
     */
    protected static function addMissingParts(string $source, string $target): string
    {
        $pattern = '/'.preg_replace('/\d+/', '[0-9]+', preg_quote($target, '/')).'$/';

        $result = preg_replace($pattern, $target, $source, 1, $count);

        return $count ? $result : $target;
    }

    private static function getStartAndEndForCyclePeriod(
        DateTimeInterface|string|int|null $start = null,
        DateTimeInterface|string|int|null $end = null,
        ?int $recurrences = null,
        ?int $anchorDay = null,
        OverflowMode $mode = OverflowMode::AnchorDay,
    ): array {
        if ($anchorDay !== null && $mode !== OverflowMode::AnchorDay) {
            throw new InvalidArgumentException(
                '$anchorDay parameter must not be set for $mode OverflowMode::'.$mode->name,
            );
        }

        if ($end !== null && $recurrences !== null) {
            throw new InvalidArgumentException(
                'You must specify $end or $recurrences but not both',
            );
        }

        if (\is_int($start)) {
            $start = CarbonImmutable::createFromTimestamp($start);
        } elseif (\is_string($start)) {
            $start = CarbonImmutable::parse($start);
        }

        $start ??= CarbonImmutable::now();

        if (\is_int($end)) {
            $end = CarbonImmutable::createFromTimestamp($end);
        }

        return [$start, $end];
    }

    private static function getMonthInterval(
        DateTimeInterface|string|int|null $start = null,
        ?int $anchorDay = null,
        OverflowMode $mode = OverflowMode::AnchorDay,
    ): CarbonInterval {
        return match ($mode) {
            OverflowMode::AnchorDay => CarbonInterval::monthWithAnchorDay(
                $anchorDay ?? $start->day,
            ),
            OverflowMode::NoOverflow => CarbonInterval::monthNoOverflow(),
            OverflowMode::Overflow => CarbonInterval::month(),
        };
    }

    private static function makeInterval(mixed $input): ?CarbonInterval
    {
        try {
            return CarbonInterval::make($input);
        } catch (Throwable) {
            return null;
        }
    }

    private static function makeTimezone(mixed $input): ?CarbonTimeZone
    {
        if (!\is_string($input)) {
            return null;
        }

        try {
            return CarbonTimeZone::create($input);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Register a custom macro.
     *
     * Pass null macro to remove it.
     *
     * @example
     * ```
     * CarbonPeriod::macro('middle', function () {
     *   return $this->getStartDate()->average($this->getEndDate());
     * });
     * echo CarbonPeriod::since('2011-05-12')->until('2011-06-03')->middle();
     * ```
     *
     * @param-closure-this  static  $macro
     */
    public static function macro(string $name, ?callable $macro): void
    {
        static::$macros[$name] = $macro;
    }

    /**
     * Register macros from a mixin object.
     *
     * @example
     * ```
     * CarbonPeriod::mixin(new class {
     *   public function addDays() {
     *     return function ($count = 1) {
     *       return $this->setStartDate(
     *         $this->getStartDate()->addDays($count)
     *       )->setEndDate(
     *         $this->getEndDate()->addDays($count)
     *       );
     *     };
     *   }
     *   public function subDays() {
     *     return function ($count = 1) {
     *       return $this->setStartDate(
     *         $this->getStartDate()->subDays($count)
     *       )->setEndDate(
     *         $this->getEndDate()->subDays($count)
     *       );
     *     };
     *   }
     * });
     * echo CarbonPeriod::create('2000-01-01', '2000-02-01')->addDays(5)->subDays(3);
     * ```
     *
     * @throws ReflectionException
     */
    public static function mixin(object|string $mixin): void
    {
        static::baseMixin($mixin);
    }

    /**
     * Check if macro is registered.
     */
    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    /**
     * Provide static proxy for instance aliases.
     */
    public static function __callStatic(string $method, array $parameters): mixed
    {
        $date = new static();

        if (static::hasMacro($method)) {
            return static::bindMacroContext(null, static fn () => $date->callMacro($method, $parameters));
        }

        return $date->$method(...$parameters);
    }

    /**
     * CarbonPeriod constructor.
     *
     * @SuppressWarnings(ElseExpression)
     *
     * @throws InvalidArgumentException
     */
    public function __construct(...$arguments)
    {
        $raw = null;

        if (isset($arguments['raw'])) {
            $raw = $arguments['raw'];
            $this->isDefaultInterval = $arguments['isDefaultInterval'] ?? false;

            if (isset($arguments['dateClass'])) {
                $this->dateClass = $arguments['dateClass'];
            }

            $arguments = $raw;
        }

        // Parse and assign arguments one by one. First argument may be an ISO 8601 spec,
        // which will be first parsed into parts and then processed the same way.

        $argumentsCount = \count($arguments);

        if ($argumentsCount && static::isIso8601($iso = $arguments[0])) {
            array_splice($arguments, 0, 1, static::parseIso8601($iso));
        }

        if ($argumentsCount === 1) {
            if ($arguments[0] instanceof self) {
                $arguments = [
                    $arguments[0]->getStartDate(),
                    $arguments[0]->getEndDate() ?? $arguments[0]->getRecurrences(),
                    $arguments[0]->getDateInterval(),
                    $arguments[0]->getOptions(),
                ];
            } elseif ($arguments[0] instanceof DatePeriod) {
                $arguments = [
                    $arguments[0]->start,
                    $arguments[0]->end ?: ($arguments[0]->recurrences - 1),
                    $arguments[0]->interval,
                    $arguments[0]->include_start_date ? 0 : static::EXCLUDE_START_DATE,
                ];
            }
        }

        if (is_a($this->dateClass, DateTimeImmutable::class, true)) {
            $this->options = static::IMMUTABLE;
        }

        $optionsSet = false;
        $originalArguments = [];
        $sortedArguments = [];

        foreach ($arguments as $argument) {
            $parsedDate = null;

            if ($argument instanceof DateTimeZone) {
                $sortedArguments = $this->configureTimezone($argument, $sortedArguments, $originalArguments);
            } elseif (!isset($sortedArguments['interval']) &&
                (
                    (\is_string($argument) && preg_match(
                        '/^(-?\d(\d(?![\/-])|[^\d\/-]([\/-])?)*|P[T\d].*|(?:\h*\d+(?:\.\d+)?\h*[a-z]+)+)$/i',
                        $argument,
                    )) ||
                    $argument instanceof DateInterval ||
                    $argument instanceof Closure ||
                    $argument instanceof Unit
                ) &&
                $parsedInterval = self::makeInterval($argument)
            ) {
                $sortedArguments['interval'] = $parsedInterval;
            } elseif (!isset($sortedArguments['start']) && $parsedDate = $this->makeDateTime($argument)) {
                $sortedArguments['start'] = $parsedDate;
                $originalArguments['start'] = $argument;
            } elseif (!isset($sortedArguments['end']) && ($parsedDate = $parsedDate ?? $this->makeDateTime($argument))) {
                $sortedArguments['end'] = $parsedDate;
                $originalArguments['end'] = $argument;
            } elseif (!isset($sortedArguments['recurrences']) &&
                !isset($sortedArguments['end']) &&
                (\is_int($argument) || \is_float($argument))
                && $argument >= 0
            ) {
                $sortedArguments['recurrences'] = $argument;
            } elseif (!$optionsSet && (\is_int($argument) || $argument === null)) {
                $optionsSet = true;
                $sortedArguments['options'] = (((int) $this->options) | ((int) $argument));
            } elseif ($parsedTimezone = self::makeTimezone($argument)) {
                $sortedArguments = $this->configureTimezone($parsedTimezone, $sortedArguments, $originalArguments);
            } else {
                throw new InvalidPeriodParameterException('Invalid constructor parameters.');
            }
        }

        $this->setFromAssociativeArray($sortedArguments);

        if ($this->startDate === null) {
            $dateClass = $this->dateClass;
            $this->setStartDate($dateClass::now());
        }

        if ($this->dateInterval === null) {
            $this->setDateInterval(CarbonInterval::day());

            $this->isDefaultInterval = true;
        }

        if ($this->options === null) {
            $this->setOptions(0);
        }

        parent::__construct(
            $this->startDate,
            $this->dateInterval,
            $this->endDate ?? max(1, min(2147483639, $this->recurrences ?? 1)),
            $this->options,
        );

        $this->constructed = true;
    }

    /**
     * Get a copy of the instance.
     */
    public function copy(): static
    {
        return clone $this;
    }

    /**
     * Prepare the instance to be set (self if mutable to be mutated,
     * copy if immutable to generate a new instance).
     */
    protected function copyIfImmutable(): static
    {
        return $this;
    }

    /**
     * Get the getter for a property allowing both `DatePeriod` snakeCase and camelCase names.
     */
    protected function getGetter(string $name): ?callable
    {
        return match (strtolower(preg_replace('/[A-Z]/', '_$0', $name))) {
            'start', 'start_date' => [$this, 'getStartDate'],
            'end', 'end_date' => [$this, 'getEndDate'],
            'interval', 'date_interval' => [$this, 'getDateInterval'],
            'recurrences' => [$this, 'getRecurrences'],
            'include_start_date' => [$this, 'isStartIncluded'],
            'include_end_date' => [$this, 'isEndIncluded'],
            'current' => [$this, 'current'],
            'locale' => [$this, 'locale'],
            'tzname', 'tz_name' => fn () => match (true) {
                $this->timezoneSetting === null => null,
                \is_string($this->timezoneSetting) => $this->timezoneSetting,
                $this->timezoneSetting instanceof DateTimeZone => $this->timezoneSetting->getName(),
                default => CarbonTimeZone::instance($this->timezoneSetting)->getName(),
            },
            default => null,
        };
    }

    /**
     * Get a property allowing both `DatePeriod` snakeCase and camelCase names.
     *
     * @param string $name
     *
     * @return bool|CarbonInterface|CarbonInterval|int|null
     */
    public function get(string $name)
    {
        $getter = $this->getGetter($name);

        if ($getter) {
            return $getter();
        }

        throw new UnknownGetterException($name);
    }

    /**
     * Get a property allowing both `DatePeriod` snakeCase and camelCase names.
     *
     * @param string $name
     *
     * @return bool|CarbonInterface|CarbonInterval|int|null
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * Check if an attribute exists on the object
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->getGetter($name) !== null;
    }

    /**
     * @alias copy
     *
     * Get a copy of the instance.
     *
     * @return static
     */
    public function clone()
    {
        return clone $this;
    }

    /**
     * Set the iteration item class.
     *
     * @param string $dateClass
     *
     * @return static
     */
    public function setDateClass(string $dateClass)
    {
        if (!is_a($dateClass, CarbonInterface::class, true)) {
            throw new NotACarbonClassException($dateClass);
        }

        $self = $this->copyIfImmutable();
        $self->dateClass = $dateClass;

        if (is_a($dateClass, Carbon::class, true)) {
            $self->options = $self->options & ~static::IMMUTABLE;
        } elseif (is_a($dateClass, CarbonImmutable::class, true)) {
            $self->options = $self->options | static::IMMUTABLE;
        }

        return $self;
    }

    /**
     * Returns iteration item date class.
     *
     * @return string
     */
    public function getDateClass(): string
    {
        return $this->dateClass;
    }

    /**
     * Change the period date interval.
     *
     * @param DateInterval|Unit|string|int $interval
     * @param Unit|string                  $unit     the unit of $interval if it's a number
     *
     * @throws InvalidIntervalException
     *
     * @return static
     */
    public function setDateInterval(mixed $interval, Unit|string|null $unit = null): static
    {
        if ($interval instanceof Unit) {
            $interval = $interval->interval();
        }

        if ($unit instanceof Unit) {
            $unit = $unit->name;
        }

        if (!$interval = CarbonInterval::make($interval, $unit)) {
            throw new InvalidIntervalException('Invalid interval.');
        }

        if ($interval->spec() === 'PT0S' && !$interval->f && !$interval->getStep()) {
            throw new InvalidIntervalException('Empty interval is not accepted.');
        }

        $self = $this->copyIfImmutable();
        $self->dateInterval = $interval;

        $self->isDefaultInterval = false;

        $self->handleChangedParameters();

        return $self;
    }

    /**
     * Reset the date interval to the default value.
     *
     * Difference with simply setting interval to 1-day is that P1D will not appear when calling toIso8601String()
     * and also next adding to the interval won't include the default 1-day.
     */
    public function resetDateInterval(): static
    {
        $self = $this->copyIfImmutable();
        $self->setDateInterval(CarbonInterval::day());

        $self->isDefaultInterval = true;

        return $self;
    }

    /**
     * Invert the period date interval.
     */
    public function invertDateInterval(): static
    {
        return $this->setDateInterval($this->dateInterval->invert());
    }

    /**
     * Set start and end date.
     *
     * @param DateTime|DateTimeInterface|string      $start
     * @param DateTime|DateTimeInterface|string|null $end
     *
     * @return static
     */
    public function setDates(mixed $start, mixed $end): static
    {
        return $this->setStartDate($start)->setEndDate($end);
    }

    /**
     * Change the period options.
     *
     * @param int|null $options
     *
     * @return static
     */
    public function setOptions(?int $options): static
    {
        $self = $this->copyIfImmutable();
        $self->options = $options ?? 0;

        $self->handleChangedParameters();

        return $self;
    }

    /**
     * Get the period options.
     */
    public function getOptions(): int
    {
        return $this->options ?? 0;
    }

    /**
     * Toggle given options on or off.
     *
     * @param int       $options
     * @param bool|null $state
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public function toggleOptions(int $options, ?bool $state = null): static
    {
        $self = $this->copyIfImmutable();

        if ($state === null) {
            $state = ($this->options & $options) !== $options;
        }

        return $self->setOptions(
            $state ?
            $this->options | $options :
            $this->options & ~$options,
        );
    }

    /**
     * Toggle EXCLUDE_START_DATE option.
     */
    public function excludeStartDate(bool $state = true): static
    {
        return $this->toggleOptions(static::EXCLUDE_START_DATE, $state);
    }

    /**
     * Toggle EXCLUDE_END_DATE option.
     */
    public function excludeEndDate(bool $state = true): static
    {
        return $this->toggleOptions(static::EXCLUDE_END_DATE, $state);
    }

    /**
     * Get the underlying date interval.
     */
    public function getDateInterval(): CarbonInterval
    {
        return $this->dateInterval->copy();
    }

    /**
     * Get start date of the period.
     *
     * @param string|null $rounding Optional rounding 'floor', 'ceil', 'round' using the period interval.
     */
    public function getStartDate(?string $rounding = null): CarbonInterface
    {
        $date = $this->startDate->avoidMutation();

        return $rounding ? $date->round($this->getDateInterval(), $rounding) : $date;
    }

    /**
     * Get end date of the period.
     *
     * @param string|null $rounding Optional rounding 'floor', 'ceil', 'round' using the period interval.
     */
    public function getEndDate(?string $rounding = null): ?CarbonInterface
    {
        if (!$this->endDate) {
            return null;
        }

        $date = $this->endDate->avoidMutation();

        return $rounding ? $date->round($this->getDateInterval(), $rounding) : $date;
    }

    /**
     * Get number of recurrences.
     */
    #[ReturnTypeWillChange]
    public function getRecurrences(): int|float|null
    {
        return $this->carbonRecurrences;
    }

    /**
     * Returns true if the start date should be excluded.
     */
    public function isStartExcluded(): bool
    {
        return ($this->options & static::EXCLUDE_START_DATE) !== 0;
    }

    /**
     * Returns true if the end date should be excluded.
     */
    public function isEndExcluded(): bool
    {
        return ($this->options & static::EXCLUDE_END_DATE) !== 0;
    }

    /**
     * Returns true if the start date should be included.
     */
    public function isStartIncluded(): bool
    {
        return !$this->isStartExcluded();
    }

    /**
     * Returns true if the end date should be included.
     */
    public function isEndIncluded(): bool
    {
        return !$this->isEndExcluded();
    }

    /**
     * Return the start if it's included by option, else return the start + 1 period interval.
     */
    public function getIncludedStartDate(): CarbonInterface
    {
        $start = $this->getStartDate();

        if ($this->isStartExcluded()) {
            return $start->add($this->getDateInterval());
        }

        return $start;
    }

    /**
     * Return the end if it's included by option, else return the end - 1 period interval.
     * Warning: if the period has no fixed end, this method will iterate the period to calculate it.
     */
    public function getIncludedEndDate(): CarbonInterface
    {
        $end = $this->getEndDate();

        if (!$end) {
            return $this->calculateEnd();
        }

        if ($this->isEndExcluded()) {
            return $end->sub($this->getDateInterval());
        }

        return $end;
    }

    /**
     * Add a filter to the stack.
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public function addFilter(callable|string $callback, ?string $name = null): static
    {
        $self = $this->copyIfImmutable();
        $tuple = $self->createFilterTuple(\func_get_args());

        $self->filters[] = $tuple;

        $self->handleChangedParameters();

        return $self;
    }

    /**
     * Prepend a filter to the stack.
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public function prependFilter(callable|string $callback, ?string $name = null): static
    {
        $self = $this->copyIfImmutable();
        $tuple = $self->createFilterTuple(\func_get_args());

        array_unshift($self->filters, $tuple);

        $self->handleChangedParameters();

        return $self;
    }

    /**
     * Remove a filter by instance or name.
     */
    public function removeFilter(callable|string $filter): static
    {
        $self = $this->copyIfImmutable();
        $key = \is_callable($filter) ? 0 : 1;

        $self->filters = array_values(array_filter(
            $this->filters,
            static fn ($tuple) => $tuple[$key] !== $filter,
        ));

        $self->updateInternalState();

        $self->handleChangedParameters();

        return $self;
    }

    /**
     * Return whether given instance or name is in the filter stack.
     */
    public function hasFilter(callable|string $filter): bool
    {
        $key = \is_callable($filter) ? 0 : 1;

        foreach ($this->filters as $tuple) {
            if ($tuple[$key] === $filter) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get filters stack.
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Set filters stack.
     */
    public function setFilters(array $filters): static
    {
        $self = $this->copyIfImmutable();
        $self->filters = $filters;

        $self->updateInternalState();

        $self->handleChangedParameters();

        return $self;
    }

    /**
     * Reset filters stack.
     */
    public function resetFilters(): static
    {
        $self = $this->copyIfImmutable();
        $self->filters = [];

        if ($self->endDate !== null) {
            $self->filters[] = [static::END_DATE_FILTER, null];
        }

        if ($self->carbonRecurrences !== null) {
            $self->filters[] = [static::RECURRENCES_FILTER, null];
        }

        $self->handleChangedParameters();

        return $self;
    }

    /**
     * Add a recurrences filter (set maximum number of recurrences).
     *
     * @throws InvalidArgumentException
     */
    public function setRecurrences(int|float|null $recurrences): static
    {
        if ($recurrences === null) {
            return $this->removeFilter(static::RECURRENCES_FILTER);
        }

        if ($recurrences < 0) {
            throw new InvalidPeriodParameterException('Invalid number of recurrences.');
        }

        /** @var self $self */
        $self = $this->copyIfImmutable();
        $self->carbonRecurrences = $recurrences === INF ? INF : (int) $recurrences;

        return self::addFilterOrHandleChangedParameters($self, static::RECURRENCES_FILTER);
    }

    /**
     * Change the period start date.
     *
     * @param DateTime|DateTimeInterface|string $date
     * @param bool|null                         $inclusive
     *
     * @throws InvalidPeriodDateException
     *
     * @return static
     */
    public function setStartDate(mixed $date, ?bool $inclusive = null): static
    {
        if (!$this->isInfiniteDate($date) && !($date = ([$this->dateClass, 'make'])($date, $this->timezone))) {
            throw new InvalidPeriodDateException('Invalid start date.');
        }

        $self = $this->copyIfImmutable();
        $self->startDate = $date;

        if ($inclusive !== null) {
            $self = $self->toggleOptions(static::EXCLUDE_START_DATE, !$inclusive);
        }

        $self->syncNativePeriod();

        return $self;
    }

    /**
     * Change the period end date.
     *
     * @param DateTime|DateTimeInterface|string|null $date
     * @param bool|null                              $inclusive
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function setEndDate(mixed $date, ?bool $inclusive = null): static
    {
        if ($date !== null && !$this->isInfiniteDate($date) && !$date = ([$this->dateClass, 'make'])($date, $this->timezone)) {
            throw new InvalidPeriodDateException('Invalid end date.');
        }

        // ::make() is responsible for converting strings to DateTimeInterface objects
        \assert(!\is_string($date));

        $self = $this->copyIfImmutable();

        if (!$date) {
            $self = $self->removeFilter(static::END_DATE_FILTER);
            $self->syncNativePeriod();

            return $self;
        }

        \assert($date instanceof DateTimeInterface);

        $self->endDate = $date;

        if (
            $self->startDate !== null
            && $self->dateInterval !== null
            && !$self->dateInterval->invert
            && $self->startDate > $self->endDate
        ) {
            $self->dateInterval->invert = 1;
        }

        if ($inclusive !== null) {
            $self = $self->toggleOptions(static::EXCLUDE_END_DATE, !$inclusive);
        }

        $self = self::addFilterOrHandleChangedParameters($self, static::END_DATE_FILTER);

        $self->syncNativePeriod();

        return $self;
    }

    /**
     * Check if the current position is valid.
     */
    public function valid(): bool
    {
        return $this->validateCurrentDate() === true;
    }

    /**
     * Return the current key.
     */
    public function key(): ?int
    {
        return $this->valid()
            ? $this->key
            : null;
    }

    /**
     * Return the current date.
     */
    public function current(): ?CarbonInterface
    {
        return $this->valid()
            ? $this->prepareForReturn($this->carbonCurrent)
            : null;
    }

    /**
     * Move forward to the next date.
     *
     * @throws RuntimeException
     */
    public function next(): void
    {
        if ($this->carbonCurrent === null) {
            $this->rewind();
        }

        if ($this->validationResult !== static::END_ITERATION) {
            $this->key++;

            $this->incrementCurrentDateUntilValid();
        }
    }

    /**
     * Rewind to the start date.
     *
     * Iterating over a date in the UTC timezone avoids bug during backward DST change.
     *
     * @see https://bugs.php.net/bug.php?id=72255
     * @see https://bugs.php.net/bug.php?id=74274
     * @see https://wiki.php.net/rfc/datetime_and_daylight_saving_time
     *
     * @throws RuntimeException
     */
    public function rewind(): void
    {
        $this->key = 0;
        $this->carbonCurrent = ([$this->dateClass, 'make'])($this->startDate);
        $settings = $this->getSettings();

        if ($this->hasLocalTranslator()) {
            $settings['locale'] = $this->getTranslatorLocale();
        }

        $this->carbonCurrent->settings($settings);
        $this->timezone = static::intervalHasTime($this->dateInterval) ? $this->carbonCurrent->getTimezone() : null;

        if ($this->timezone) {
            $this->carbonCurrent = $this->carbonCurrent->utc();
        }

        $this->validationResult = null;

        if ($this->isStartExcluded() || $this->validateCurrentDate() === false) {
            $this->incrementCurrentDateUntilValid();
        }
    }

    /**
     * Skip iterations and returns iteration state (false if ended, true if still valid).
     *
     * @param int $count steps number to skip (1 by default)
     *
     * @return bool
     */
    public function skip(int $count = 1): bool
    {
        for ($i = $count; $this->valid() && $i > 0; $i--) {
            $this->next();
        }

        return $this->valid();
    }

    /**
     * Format the date period as ISO 8601.
     */
    public function toIso8601String(): string
    {
        $parts = [];

        if ($this->carbonRecurrences !== null) {
            $parts[] = 'R'.$this->carbonRecurrences;
        }

        $parts[] = $this->startDate->toIso8601String();

        if (!$this->isDefaultInterval) {
            $parts[] = $this->dateInterval->spec();
        }

        if ($this->endDate !== null) {
            $parts[] = $this->endDate->toIso8601String();
        }

        return implode('/', $parts);
    }

    /**
     * Convert the date period into a string.
     */
    public function toString(): string
    {
        $format = $this->localToStringFormat
            ?? $this->getFactory()->getSettings()['toStringFormat']
            ?? null;

        if ($format instanceof Closure) {
            return $format($this);
        }

        $translator = ([$this->dateClass, 'getTranslator'])();

        $parts = [];

        $format = $format ?? (
            !$this->startDate->isStartOfDay() || ($this->endDate && !$this->endDate->isStartOfDay())
                ? 'Y-m-d H:i:s'
                : 'Y-m-d'
        );

        if ($this->carbonRecurrences !== null) {
            $parts[] = $this->translate('period_recurrences', [], $this->carbonRecurrences, $translator);
        }

        $parts[] = $this->translate('period_interval', [':interval' => $this->dateInterval->forHumans([
            'join' => true,
        ])], null, $translator);

        $parts[] = $this->translate('period_start_date', [':date' => $this->startDate->rawFormat($format)], null, $translator);

        if ($this->endDate !== null) {
            $parts[] = $this->translate('period_end_date', [':date' => $this->endDate->rawFormat($format)], null, $translator);
        }

        $result = implode(' ', $parts);

        return mb_strtoupper(mb_substr($result, 0, 1)).mb_substr($result, 1);
    }

    /**
     * Format the date period as ISO 8601.
     */
    public function spec(): string
    {
        return $this->toIso8601String();
    }

    /**
     * Cast the current instance into the given class.
     *
     * @param string $className The $className::instance() method will be called to cast the current object.
     *
     * @return DatePeriod|object
     */
    public function cast(string $className): object
    {
        if (!method_exists($className, 'instance')) {
            if (is_a($className, DatePeriod::class, true)) {
                return new $className(
                    $this->rawDate($this->getStartDate()),
                    $this->getDateInterval(),
                    $this->getEndDate() ? $this->rawDate($this->getIncludedEndDate()) : $this->getRecurrences(),
                    $this->isStartExcluded() ? DatePeriod::EXCLUDE_START_DATE : 0,
                );
            }

            throw new InvalidCastException("$className has not the instance() method needed to cast the date.");
        }

        return $className::instance($this);
    }

    /**
     * Return native DatePeriod PHP object matching the current instance.
     *
     * @example
     * ```
     * var_dump(CarbonPeriod::create('2021-01-05', '2021-02-15')->toDatePeriod());
     * ```
     */
    public function toDatePeriod(): DatePeriod
    {
        return $this->cast(DatePeriod::class);
    }

    /**
     * Return `true` if the period has no custom filter and is guaranteed to be endless.
     *
     * Note that we can't check if a period is endless as soon as it has custom filters
     * because filters can emit `CarbonPeriod::END_ITERATION` to stop the iteration in
     * a way we can't predict without actually iterating the period.
     */
    public function isUnfilteredAndEndLess(): bool
    {
        foreach ($this->filters as $filter) {
            switch ($filter) {
                case [static::RECURRENCES_FILTER, null]:
                    if ($this->carbonRecurrences !== null && is_finite($this->carbonRecurrences)) {
                        return false;
                    }

                    break;

                case [static::END_DATE_FILTER, null]:
                    if ($this->endDate !== null && !$this->endDate->isEndOfTime()) {
                        return false;
                    }

                    break;

                default:
                    return false;
            }
        }

        return true;
    }

    /**
     * Convert the date period into an array without changing current iteration state.
     *
     * @return CarbonInterface[]
     */
    public function toArray(): array
    {
        if ($this->isUnfilteredAndEndLess()) {
            throw new EndLessPeriodException("Endless period can't be converted to array nor counted.");
        }

        $state = [
            $this->key,
            $this->carbonCurrent ? $this->carbonCurrent->avoidMutation() : null,
            $this->validationResult,
        ];

        $result = iterator_to_array($this);

        [$this->key, $this->carbonCurrent, $this->validationResult] = $state;

        return $result;
    }

    /**
     * Count dates in the date period.
     */
    public function count(): int
    {
        return \count($this->toArray());
    }

    /**
     * Return the first date in the date period.
     */
    public function first(): ?CarbonInterface
    {
        if ($this->isUnfilteredAndEndLess()) {
            foreach ($this as $date) {
                $this->rewind();

                return $date;
            }

            return null;
        }

        return ($this->toArray() ?: [])[0] ?? null;
    }

    /**
     * Return the last date in the date period.
     */
    public function last(): ?CarbonInterface
    {
        $array = $this->toArray();

        return $array ? $array[\count($array) - 1] : null;
    }

    /**
     * Convert the date period into a string.
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Add aliases for setters.
     *
     * CarbonPeriod::days(3)->hours(5)->invert()
     *     ->sinceNow()->until('2010-01-10')
     *     ->filter(...)
     *     ->count()
     *
     * Note: We use magic method to let static and instance aliases with the same names.
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (static::hasMacro($method)) {
            return static::bindMacroContext($this, fn () => $this->callMacro($method, $parameters));
        }

        $roundedValue = $this->callRoundMethod($method, $parameters);

        if ($roundedValue !== null) {
            return $roundedValue;
        }

        $count = \count($parameters);

        switch ($method) {
            case 'start':
            case 'since':
                if ($count === 0) {
                    return $this->getStartDate();
                }

                self::setDefaultParameters($parameters, [
                    [0, 'date', null],
                ]);

                return $this->setStartDate(...$parameters);

            case 'sinceNow':
                return $this->setStartDate(new Carbon(), ...$parameters);

            case 'end':
            case 'until':
                if ($count === 0) {
                    return $this->getEndDate();
                }

                self::setDefaultParameters($parameters, [
                    [0, 'date', null],
                ]);

                return $this->setEndDate(...$parameters);

            case 'untilNow':
                return $this->setEndDate(new Carbon(), ...$parameters);

            case 'dates':
            case 'between':
                self::setDefaultParameters($parameters, [
                    [0, 'start', null],
                    [1, 'end', null],
                ]);

                return $this->setDates(...$parameters);

            case 'recurrences':
            case 'times':
                if ($count === 0) {
                    return $this->getRecurrences();
                }

                self::setDefaultParameters($parameters, [
                    [0, 'recurrences', null],
                ]);

                return $this->setRecurrences(...$parameters);

            case 'options':
                if ($count === 0) {
                    return $this->getOptions();
                }

                self::setDefaultParameters($parameters, [
                    [0, 'options', null],
                ]);

                return $this->setOptions(...$parameters);

            case 'toggle':
                self::setDefaultParameters($parameters, [
                    [0, 'options', null],
                ]);

                return $this->toggleOptions(...$parameters);

            case 'filter':
            case 'push':
                return $this->addFilter(...$parameters);

            case 'prepend':
                return $this->prependFilter(...$parameters);

            case 'filters':
                if ($count === 0) {
                    return $this->getFilters();
                }

                self::setDefaultParameters($parameters, [
                    [0, 'filters', []],
                ]);

                return $this->setFilters(...$parameters);

            case 'interval':
            case 'each':
            case 'every':
            case 'step':
            case 'stepBy':
                if ($count === 0) {
                    return $this->getDateInterval();
                }

                return $this->setDateInterval(...$parameters);

            case 'invert':
                return $this->invertDateInterval();

            case 'years':
            case 'year':
            case 'months':
            case 'month':
            case 'weeks':
            case 'week':
            case 'days':
            case 'dayz':
            case 'day':
            case 'hours':
            case 'hour':
            case 'minutes':
            case 'minute':
            case 'seconds':
            case 'second':
            case 'milliseconds':
            case 'millisecond':
            case 'microseconds':
            case 'microsecond':
                return $this->setDateInterval((
                    // Override default P1D when instantiating via fluent setters.
                    [$this->isDefaultInterval ? new CarbonInterval('PT0S') : $this->dateInterval, $method]
                )(...$parameters));
        }

        $dateClass = $this->dateClass;

        if ($this->localStrictModeEnabled ?? $dateClass::isStrictModeEnabled()) {
            throw new UnknownMethodException($method);
        }

        return $this;
    }

    /**
     * Set the instance's timezone from a string or object and apply it to start/end.
     */
    public function setTimezone(DateTimeZone|string|int $timezone): static
    {
        $self = $this->copyIfImmutable();
        $self->timezoneSetting = $timezone;
        $self->timezone = CarbonTimeZone::instance($timezone);

        if ($self->startDate) {
            $self = $self->setStartDate($self->startDate->setTimezone($timezone));
        }

        if ($self->endDate) {
            $self = $self->setEndDate($self->endDate->setTimezone($timezone));
        }

        return $self;
    }

    /**
     * Set the instance's timezone from a string or object and add/subtract the offset difference to start/end.
     */
    public function shiftTimezone(DateTimeZone|string|int $timezone): static
    {
        $self = $this->copyIfImmutable();
        $self->timezoneSetting = $timezone;
        $self->timezone = CarbonTimeZone::instance($timezone);

        if ($self->startDate) {
            $self = $self->setStartDate($self->startDate->shiftTimezone($timezone));
        }

        if ($self->endDate) {
            $self = $self->setEndDate($self->endDate->shiftTimezone($timezone));
        }

        return $self;
    }

    /**
     * Returns the end is set, else calculated from start and recurrences.
     *
     * @param string|null $rounding Optional rounding 'floor', 'ceil', 'round' using the period interval.
     *
     * @return CarbonInterface
     */
    public function calculateEnd(?string $rounding = null): CarbonInterface
    {
        if ($end = $this->getEndDate($rounding)) {
            return $end;
        }

        if ($this->dateInterval->isEmpty()) {
            return $this->getStartDate($rounding);
        }

        $date = $this->getEndFromRecurrences() ?? $this->iterateUntilEnd();

        if ($date && $rounding) {
            $date = $date->avoidMutation()->round($this->getDateInterval(), $rounding);
        }

        return $date;
    }

    private function getEndFromRecurrences(): ?CarbonInterface
    {
        if ($this->carbonRecurrences === null) {
            throw new UnreachableException(
                "Could not calculate period end without either explicit end or recurrences.\n".
                "If you're looking for a forever-period, use ->setRecurrences(INF).",
            );
        }

        if ($this->carbonRecurrences === INF) {
            $start = $this->getStartDate();

            return $start < $start->avoidMutation()->add($this->getDateInterval())
                ? CarbonImmutable::endOfTime()
                : CarbonImmutable::startOfTime();
        }

        if ($this->filters === [[static::RECURRENCES_FILTER, null]]) {
            return $this->getStartDate()->avoidMutation()->add(
                $this->getDateInterval()->times(
                    $this->carbonRecurrences - ($this->isStartExcluded() ? 0 : 1),
                ),
            );
        }

        return null;
    }

    private function iterateUntilEnd(): ?CarbonInterface
    {
        $attempts = 0;
        $date = null;

        foreach ($this as $date) {
            if (++$attempts > static::END_MAX_ATTEMPTS) {
                throw new UnreachableException(
                    'Could not calculate period end after iterating '.static::END_MAX_ATTEMPTS.' times.',
                );
            }
        }

        return $date;
    }

    /**
     * Returns true if the current period overlaps the given one (if 1 parameter passed)
     * or the period between 2 dates (if 2 parameters passed).
     *
     * @param CarbonPeriod|\DateTimeInterface|Carbon|CarbonImmutable|string $rangeOrRangeStart
     * @param \DateTimeInterface|Carbon|CarbonImmutable|string|null         $rangeEnd
     *
     * @return bool
     */
    public function overlaps(mixed $rangeOrRangeStart, mixed $rangeEnd = null): bool
    {
        $range = $rangeEnd ? static::create($rangeOrRangeStart, $rangeEnd) : $rangeOrRangeStart;

        if (!($range instanceof self)) {
            $range = static::create($range);
        }

        [$start, $end] = $this->orderCouple($this->getStartDate(), $this->calculateEnd());
        [$rangeStart, $rangeEnd] = $this->orderCouple($range->getStartDate(), $range->calculateEnd());

        return $end > $rangeStart && $rangeEnd > $start;
    }

    /**
     * Execute a given function on each date of the period.
     *
     * @example
     * ```
     * Carbon::create('2020-11-29')->daysUntil('2020-12-24')->forEach(function (Carbon $date) {
     *   echo $date->diffInDays('2020-12-25')." days before Christmas!\n";
     * });
     * ```
     */
    public function forEach(callable $callback): void
    {
        foreach ($this as $date) {
            $callback($date);
        }
    }

    /**
     * Execute a given function on each date of the period and yield the result of this function.
     *
     * @example
     * ```
     * $period = Carbon::create('2020-11-29')->daysUntil('2020-12-24');
     * echo implode("\n", iterator_to_array($period->map(function (Carbon $date) {
     *   return $date->diffInDays('2020-12-25').' days before Christmas!';
     * })));
     * ```
     */
    public function map(callable $callback): Generator
    {
        foreach ($this as $date) {
            yield $callback($date);
        }
    }

    /**
     * Determines if the instance is equal to another.
     * Warning: if options differ, instances will never be equal.
     *
     * @see equalTo()
     */
    public function eq(mixed $period): bool
    {
        return $this->equalTo($period);
    }

    /**
     * Determines if the instance is equal to another.
     * Warning: if options differ, instances will never be equal.
     */
    public function equalTo(mixed $period): bool
    {
        if (!($period instanceof self)) {
            $period = self::make($period);
        }

        $end = $this->getEndDate();

        return $period !== null
            && $this->getDateInterval()->eq($period->getDateInterval())
            && $this->getStartDate()->eq($period->getStartDate())
            && ($end ? $end->eq($period->getEndDate()) : $this->getRecurrences() === $period->getRecurrences())
            && ($this->getOptions() & (~static::IMMUTABLE)) === ($period->getOptions() & (~static::IMMUTABLE));
    }

    /**
     * Determines if the instance is not equal to another.
     * Warning: if options differ, instances will never be equal.
     *
     * @see notEqualTo()
     */
    public function ne(mixed $period): bool
    {
        return $this->notEqualTo($period);
    }

    /**
     * Determines if the instance is not equal to another.
     * Warning: if options differ, instances will never be equal.
     */
    public function notEqualTo(mixed $period): bool
    {
        return !$this->eq($period);
    }

    /**
     * Determines if the start date is before another given date.
     * (Rather start/end are included by options is ignored.)
     */
    public function startsBefore(mixed $date = null): bool
    {
        return $this->getStartDate()->lessThan($this->resolveCarbon($date));
    }

    /**
     * Determines if the start date is before or the same as a given date.
     * (Rather start/end are included by options is ignored.)
     */
    public function startsBeforeOrAt(mixed $date = null): bool
    {
        return $this->getStartDate()->lessThanOrEqualTo($this->resolveCarbon($date));
    }

    /**
     * Determines if the start date is after another given date.
     * (Rather start/end are included by options is ignored.)
     */
    public function startsAfter(mixed $date = null): bool
    {
        return $this->getStartDate()->greaterThan($this->resolveCarbon($date));
    }

    /**
     * Determines if the start date is after or the same as a given date.
     * (Rather start/end are included by options is ignored.)
     */
    public function startsAfterOrAt(mixed $date = null): bool
    {
        return $this->getStartDate()->greaterThanOrEqualTo($this->resolveCarbon($date));
    }

    /**
     * Determines if the start date is the same as a given date.
     * (Rather start/end are included by options is ignored.)
     */
    public function startsAt(mixed $date = null): bool
    {
        return $this->getStartDate()->equalTo($this->resolveCarbon($date));
    }

    /**
     * Determines if the end date is before another given date.
     * (Rather start/end are included by options is ignored.)
     */
    public function endsBefore(mixed $date = null): bool
    {
        return $this->calculateEnd()->lessThan($this->resolveCarbon($date));
    }

    /**
     * Determines if the end date is before or the same as a given date.
     * (Rather start/end are included by options is ignored.)
     */
    public function endsBeforeOrAt(mixed $date = null): bool
    {
        return $this->calculateEnd()->lessThanOrEqualTo($this->resolveCarbon($date));
    }

    /**
     * Determines if the end date is after another given date.
     * (Rather start/end are included by options is ignored.)
     */
    public function endsAfter(mixed $date = null): bool
    {
        return $this->calculateEnd()->greaterThan($this->resolveCarbon($date));
    }

    /**
     * Determines if the end date is after or the same as a given date.
     * (Rather start/end are included by options is ignored.)
     */
    public function endsAfterOrAt(mixed $date = null): bool
    {
        return $this->calculateEnd()->greaterThanOrEqualTo($this->resolveCarbon($date));
    }

    /**
     * Determines if the end date is the same as a given date.
     * (Rather start/end are included by options is ignored.)
     */
    public function endsAt(mixed $date = null): bool
    {
        return $this->calculateEnd()->equalTo($this->resolveCarbon($date));
    }

    /**
     * Return true if start date is now or later.
     * (Rather start/end are included by options is ignored.)
     */
    public function isStarted(): bool
    {
        return $this->startsBeforeOrAt();
    }

    /**
     * Return true if end date is now or later.
     * (Rather start/end are included by options is ignored.)
     */
    public function isEnded(): bool
    {
        return $this->endsBeforeOrAt();
    }

    /**
     * Return true if now is between start date (included) and end date (excluded).
     * (Rather start/end are included by options is ignored.)
     */
    public function isInProgress(): bool
    {
        return $this->isStarted() && !$this->isEnded();
    }

    /**
     * Round the current instance at the given unit with given precision if specified and the given function.
     */
    public function roundUnit(
        string $unit,
        DateInterval|float|int|string|null $precision = 1,
        callable|string $function = 'round',
    ): static {
        $self = $this->copyIfImmutable();
        $self = $self->setStartDate($self->getStartDate()->roundUnit($unit, $precision, $function));

        if ($self->endDate) {
            $self = $self->setEndDate($self->getEndDate()->roundUnit($unit, $precision, $function));
        }

        return $self->setDateInterval($self->getDateInterval()->roundUnit($unit, $precision, $function));
    }

    /**
     * Truncate the current instance at the given unit with given precision if specified.
     */
    public function floorUnit(string $unit, DateInterval|float|int|string|null $precision = 1): static
    {
        return $this->roundUnit($unit, $precision, 'floor');
    }

    /**
     * Ceil the current instance at the given unit with given precision if specified.
     */
    public function ceilUnit(string $unit, DateInterval|float|int|string|null $precision = 1): static
    {
        return $this->roundUnit($unit, $precision, 'ceil');
    }

    /**
     * Round the current instance second with given precision if specified (else period interval is used).
     */
    public function round(
        DateInterval|float|int|string|null $precision = null,
        callable|string $function = 'round',
    ): static {
        return $this->roundWith(
            $precision ?? $this->getDateInterval()->setLocalTranslator(TranslatorImmutable::get('en'))->forHumans(),
            $function
        );
    }

    /**
     * Round the current instance second with given precision if specified (else period interval is used).
     */
    public function floor(DateInterval|float|int|string|null $precision = null): static
    {
        return $this->round($precision, 'floor');
    }

    /**
     * Ceil the current instance second with given precision if specified (else period interval is used).
     */
    public function ceil(DateInterval|float|int|string|null $precision = null): static
    {
        return $this->round($precision, 'ceil');
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return CarbonInterface[]
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Return true if the given date is between start and end.
     */
    public function contains(mixed $date = null): bool
    {
        $startMethod = 'startsBefore'.($this->isStartIncluded() ? 'OrAt' : '');
        $endMethod = 'endsAfter'.($this->isEndIncluded() ? 'OrAt' : '');

        return $this->$startMethod($date) && $this->$endMethod($date);
    }

    /**
     * Return true if the current period follows a given other period (with no overlap).
     * For instance, [2019-08-01 -> 2019-08-12] follows [2019-07-29 -> 2019-07-31]
     * Note than in this example, follows() would be false if 2019-08-01 or 2019-07-31 was excluded by options.
     */
    public function follows(mixed $period, mixed ...$arguments): bool
    {
        $period = $this->resolveCarbonPeriod($period, ...$arguments);

        return $this->getIncludedStartDate()->equalTo($period->getIncludedEndDate()->add($period->getDateInterval()));
    }

    /**
     * Return true if the given other period follows the current one (with no overlap).
     * For instance, [2019-07-29 -> 2019-07-31] is followed by [2019-08-01 -> 2019-08-12]
     * Note than in this example, isFollowedBy() would be false if 2019-08-01 or 2019-07-31 was excluded by options.
     */
    public function isFollowedBy(mixed $period, mixed ...$arguments): bool
    {
        $period = $this->resolveCarbonPeriod($period, ...$arguments);

        return $period->follows($this);
    }

    /**
     * Return true if the given period either follows or is followed by the current one.
     *
     * @see follows()
     * @see isFollowedBy()
     */
    public function isConsecutiveWith(mixed $period, mixed ...$arguments): bool
    {
        return $this->follows($period, ...$arguments) || $this->isFollowedBy($period, ...$arguments);
    }

    public function __debugInfo(): array
    {
        $info = $this->baseDebugInfo();
        unset(
            $info['start'],
            $info['end'],
            $info['interval'],
            $info['include_start_date'],
            $info['include_end_date'],
            $info['constructed'],
            $info["\0*\0constructed"],
        );

        return $info;
    }

    public function __unserialize(array $data): void
    {
        try {
            $values = array_combine(
                array_map(
                    static fn (string $key): string => preg_replace('/^\0\*\0/', '', $key),
                    array_keys($data),
                ),
                $data,
            );

            $this->initializeSerialization($values);

            foreach ($values as $key => $value) {
                if ($value === null) {
                    continue;
                }

                $property = match ($key) {
                    'tzName' => $this->setTimezone(...),
                    'options' => $this->setOptions(...),
                    'recurrences' => $this->setRecurrences(...),
                    'current' => function (mixed $current): void {
                        $this->carbonCurrent = $this->carbonOrResolve($current);
                    },
                    'start' => 'startDate',
                    'interval' => $this->setDateInterval(...),
                    'end' => 'endDate',
                    'key' => null,
                    'include_start_date' => function (bool $included): void {
                        $this->excludeStartDate(!$included);
                    },
                    'include_end_date' => function (bool $included): void {
                        $this->excludeEndDate(!$included);
                    },
                    default => $key,
                };

                if ($property === null) {
                    continue;
                }

                if (\is_callable($property)) {
                    $property($value);

                    continue;
                }

                if ($value instanceof DateTimeInterface && !($value instanceof CarbonInterface)) {
                    $value = ($value instanceof DateTime)
                        ? Carbon::instance($value)
                        : CarbonImmutable::instance($value);
                }

                try {
                    $this->$property = $value;
                } catch (Throwable) {
                    // Must be ignored for backward-compatibility
                }
            }

            if (\array_key_exists('carbonRecurrences', $values)) {
                $this->carbonRecurrences = $values['carbonRecurrences'];
            } elseif (((int) ($values['recurrences'] ?? 0)) <= 1 && $this->endDate !== null) {
                $this->carbonRecurrences = null;
            }
        } catch (Throwable $e) {
            // @codeCoverageIgnoreStart
            if (!method_exists(parent::class, '__unserialize')) {
                throw $e;
            }

            parent::__unserialize($data);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Update properties after removing built-in filters.
     */
    protected function updateInternalState(): void
    {
        if (!$this->hasFilter(static::END_DATE_FILTER)) {
            $this->endDate = null;
        }

        if (!$this->hasFilter(static::RECURRENCES_FILTER)) {
            $this->carbonRecurrences = null;
        }
    }

    /**
     * Create a filter tuple from raw parameters.
     *
     * Will create an automatic filter callback for one of Carbon's is* methods.
     */
    protected function createFilterTuple(array $parameters): array
    {
        $method = array_shift($parameters);

        if (!$this->isCarbonPredicateMethod($method)) {
            return [$method, array_shift($parameters)];
        }

        return [static fn ($date) => ([$date, $method])(...$parameters), $method];
    }

    /**
     * Return whether given callable is a string pointing to one of Carbon's is* methods
     * and should be automatically converted to a filter callback.
     */
    protected function isCarbonPredicateMethod(callable|string $callable): bool
    {
        return \is_string($callable) && str_starts_with($callable, 'is') &&
            (method_exists($this->dateClass, $callable) || ([$this->dateClass, 'hasMacro'])($callable));
    }

    /**
     * Recurrences filter callback (limits number of recurrences).
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    protected function filterRecurrences(CarbonInterface $current, int $key): bool|callable
    {
        if ($key < $this->carbonRecurrences) {
            return true;
        }

        return static::END_ITERATION;
    }

    /**
     * End date filter callback.
     *
     * @return bool|static::END_ITERATION
     */
    protected function filterEndDate(CarbonInterface $current): bool|callable
    {
        if (!$this->isEndExcluded() && $current == $this->endDate) {
            return true;
        }

        if ($this->dateInterval->invert ? ($current > $this->endDate) : ($current < $this->endDate)) {
            return true;
        }

        return static::END_ITERATION;
    }

    /**
     * End iteration filter callback.
     *
     * @return static::END_ITERATION
     */
    protected function endIteration(): callable
    {
        return static::END_ITERATION;
    }

    /**
     * Handle change of the parameters.
     */
    protected function handleChangedParameters(): void
    {
        if (($this->getOptions() & static::IMMUTABLE) && $this->dateClass === Carbon::class) {
            $this->dateClass = CarbonImmutable::class;
        } elseif (!($this->getOptions() & static::IMMUTABLE) && $this->dateClass === CarbonImmutable::class) {
            $this->dateClass = Carbon::class;
        }

        $this->validationResult = null;
    }

    /**
     * Synchronize the native DatePeriod properties with the current state.
     */
    protected function syncNativePeriod(): void
    {
        if (\PHP_VERSION_ID < 80200) {
            return; // @codeCoverageIgnore
        }

        // Default interval if not set (matches __construct logic)
        $interval = $this->dateInterval ?? \Carbon\CarbonInterval::day();

        // Reinitialize the parent DatePeriod to update $start, $end, etc.
        // This mirrors the logic in __construct and initializeSerialization.
        parent::__construct(
            $this->startDate,
            $interval,
            $this->endDate ?? max(1, min(2147483639, $this->recurrences ?? 1)),
            $this->options ?? 0,
        );
    }

    /**
     * Validate current date and stop iteration when necessary.
     *
     * Returns true when current date is valid, false if it is not, or static::END_ITERATION
     * when iteration should be stopped.
     *
     * @return bool|static::END_ITERATION
     */
    protected function validateCurrentDate(): bool|callable
    {
        if ($this->carbonCurrent === null) {
            $this->rewind();
        }

        // Check after the first rewind to avoid repeating the initial validation.
        return $this->validationResult ?? ($this->validationResult = $this->checkFilters());
    }

    /**
     * Check whether current value and key pass all the filters.
     *
     * @return bool|static::END_ITERATION
     */
    protected function checkFilters(): bool|callable
    {
        $current = $this->prepareForReturn($this->carbonCurrent);

        foreach ($this->filters as $tuple) {
            $result = \call_user_func($tuple[0], $current->avoidMutation(), $this->key, $this);

            if ($result === static::END_ITERATION) {
                return static::END_ITERATION;
            }

            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * Prepare given date to be returned to the external logic.
     *
     * @param CarbonInterface $date
     *
     * @return CarbonInterface
     */
    protected function prepareForReturn(CarbonInterface $date)
    {
        $date = ([$this->dateClass, 'make'])($date);

        if ($this->timezone) {
            return $date->setTimezone($this->timezone);
        }

        return $date;
    }

    /**
     * Keep incrementing the current date until a valid date is found or the iteration is ended.
     *
     * @throws RuntimeException
     */
    protected function incrementCurrentDateUntilValid(): void
    {
        $attempts = 0;

        do {
            $this->carbonCurrent = $this->carbonCurrent->add(
                $this->dateInterval,
                $this->dateInterval->getStep() && $this->dateInterval->invert ? -1 : 1,
            );

            $this->validationResult = null;

            if (++$attempts > static::NEXT_MAX_ATTEMPTS) {
                throw new UnreachableException('Could not find next valid date.');
            }
        } while ($this->validateCurrentDate() === false);
    }

    /**
     * Call given macro.
     */
    protected function callMacro(string $name, array $parameters): mixed
    {
        $macro = static::$macros[$name];

        if ($macro instanceof Closure) {
            $boundMacro = @$macro->bindTo($this, static::class) ?: @$macro->bindTo(null, static::class);

            return ($boundMacro ?: $macro)(...$parameters);
        }

        return $macro(...$parameters);
    }

    /**
     * Return the Carbon instance passed through, a now instance in the same timezone
     * if null given or parse the input if string given.
     *
     * @param \Carbon\Carbon|\Carbon\CarbonPeriod|\Carbon\CarbonInterval|\DateInterval|\DatePeriod|\DateTimeInterface|string|null $date
     *
     * @return \Carbon\CarbonInterface
     */
    protected function resolveCarbon($date = null)
    {
        return $this->getStartDate()->nowWithSameTz()->carbonize($date);
    }

    /**
     * Resolve passed arguments or DatePeriod to a CarbonPeriod object.
     */
    protected function resolveCarbonPeriod(mixed $period, mixed ...$arguments): self
    {
        if ($period instanceof self) {
            return $period;
        }

        return $period instanceof DatePeriod
            ? static::instance($period)
            : static::create($period, ...$arguments);
    }

    private function carbonOrResolve(mixed $dateTime): CarbonInterface
    {
        return $dateTime instanceof CarbonInterface
            ? $dateTime
            : $this->resolveCarbon($dateTime);
    }

    private function orderCouple($first, $second): array
    {
        return $first > $second ? [$second, $first] : [$first, $second];
    }

    private function makeDateTime($value): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if ($value instanceof WeekDay || $value instanceof Month) {
            $dateClass = $this->dateClass;

            return new $dateClass($value, $this->timezoneSetting);
        }

        if (\is_string($value)) {
            $value = trim($value);

            if (!preg_match('/^P[\dT]/', $value) &&
                !preg_match('/^R\d/', $value) &&
                preg_match('/[a-z\d]/i', $value)
            ) {
                $dateClass = $this->dateClass;

                return $dateClass::parse($value, $this->timezoneSetting);
            }
        }

        return null;
    }

    private function isInfiniteDate($date): bool
    {
        return $date instanceof CarbonInterface && ($date->isEndOfTime() || $date->isStartOfTime());
    }

    private function rawDate($date): ?DateTimeInterface
    {
        if ($date === false || $date === null) {
            return null;
        }

        if ($date instanceof CarbonInterface) {
            return $date->isMutable()
                ? $date->toDateTime()
                : $date->toDateTimeImmutable();
        }

        if (\in_array(\get_class($date), [DateTime::class, DateTimeImmutable::class], true)) {
            return $date;
        }

        $class = $date instanceof DateTime ? DateTime::class : DateTimeImmutable::class;

        return new $class($date->format('Y-m-d H:i:s.u'), $date->getTimezone());
    }

    private static function setDefaultParameters(array &$parameters, array $defaults): void
    {
        foreach ($defaults as [$index, $name, $value]) {
            if (!\array_key_exists($index, $parameters) && !\array_key_exists($name, $parameters)) {
                $parameters[$index] = $value;
            }
        }
    }

    private function setFromAssociativeArray(array $parameters): void
    {
        if (isset($parameters['start'])) {
            $this->setStartDate($parameters['start']);
        }

        if (isset($parameters['start'])) {
            $this->setStartDate($parameters['start']);
        }

        if (isset($parameters['end'])) {
            $this->setEndDate($parameters['end']);
        }

        if (isset($parameters['recurrences'])) {
            $this->setRecurrences($parameters['recurrences']);
        }

        if (isset($parameters['interval'])) {
            $this->setDateInterval($parameters['interval']);
        }

        if (isset($parameters['options'])) {
            $this->setOptions($parameters['options']);
        }
    }

    private function configureTimezone(DateTimeZone $timezone, array $sortedArguments, array $originalArguments): array
    {
        $this->setTimezone($timezone);

        if (\is_string($originalArguments['start'] ?? null)) {
            $sortedArguments['start'] = $this->makeDateTime($originalArguments['start']);
        }

        if (\is_string($originalArguments['end'] ?? null)) {
            $sortedArguments['end'] = $this->makeDateTime($originalArguments['end']);
        }

        return $sortedArguments;
    }

    private function initializeSerialization(array $values): void
    {
        $serializationBase = [
            'start' => $values['start'] ?? $values['startDate'] ?? null,
            'current' => $values['current'] ?? $values['carbonCurrent'] ?? null,
            'end' => $values['end'] ?? $values['endDate'] ?? null,
            'interval' => $values['interval'] ?? $values['dateInterval'] ?? null,
            'recurrences' => max(1, (int) ($values['recurrences'] ?? $values['carbonRecurrences'] ?? 1)),
            'include_start_date' => $values['include_start_date'] ?? true,
            'include_end_date' => $values['include_end_date'] ?? false,
        ];

        foreach (['start', 'current', 'end'] as $dateProperty) {
            if ($serializationBase[$dateProperty] instanceof Carbon) {
                $serializationBase[$dateProperty] = $serializationBase[$dateProperty]->toDateTime();
            } elseif ($serializationBase[$dateProperty] instanceof CarbonInterface) {
                $serializationBase[$dateProperty] = $serializationBase[$dateProperty]->toDateTimeImmutable();
            }
        }

        if ($serializationBase['interval'] instanceof CarbonInterval) {
            $serializationBase['interval'] = $serializationBase['interval']->toDateInterval();
        }

        // @codeCoverageIgnoreStart
        if (method_exists(parent::class, '__unserialize')) {
            parent::__unserialize($serializationBase);

            return;
        }

        $excludeStart = !($values['include_start_date'] ?? true);
        $includeEnd = $values['include_end_date'] ?? true;

        parent::__construct(
            $serializationBase['start'],
            $serializationBase['interval'],
            $serializationBase['end'] ?? $serializationBase['recurrences'],
            ($excludeStart ? self::EXCLUDE_START_DATE : 0) | ($includeEnd && \defined('DatePeriod::INCLUDE_END_DATE') ? self::INCLUDE_END_DATE : 0),
        );
        // @codeCoverageIgnoreEnd
    }

    private static function addFilterOrHandleChangedParameters(
        self $period,
        array|callable|string $filter,
    ): self {
        if (!$period->hasFilter($filter)) {
            return $period->addFilter($filter);
        }

        $period->handleChangedParameters();

        return $period;
    }
}
