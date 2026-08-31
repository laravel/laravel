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

namespace Carbon\Traits;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Carbon\Exceptions\UnknownUnitException;
use Carbon\Unit;
use Closure;
use DateInterval;
use DateTimeInterface;

/**
 * Trait Difference.
 *
 * Depends on the following methods:
 *
 * @method bool lessThan($date)
 * @method static copy()
 * @method static resolveCarbon($date = null)
 */
trait Difference
{
    /**
     * Get the difference as a DateInterval instance.
     * Return relative interval (negative if $absolute flag is not set to true and the given date is before
     * current one).
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return DateInterval
     */
    public function diffAsDateInterval($date = null, bool $absolute = false): DateInterval
    {
        $other = $this->resolveCarbon($date);

        // Work-around for https://bugs.php.net/bug.php?id=81458
        // It was initially introduced for https://bugs.php.net/bug.php?id=80998
        // The very specific case of 80998 was fixed in PHP 8.1beta3, but it introduced 81458
        // So we still need to keep this for now
        if ($other->tz !== $this->tz) {
            $other = $other->avoidMutation()->setTimezone($this->tz);
        }

        return parent::diff($other, $absolute);
    }

    /**
     * Get the difference as a CarbonInterval instance.
     * Return relative interval (negative if $absolute flag is not set to true and the given date is before
     * current one).
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return CarbonInterval
     */
    public function diffAsCarbonInterval($date = null, bool $absolute = false, array $skip = []): CarbonInterval
    {
        return CarbonInterval::diff($this, $this->resolveCarbon($date), $absolute, $skip)
            ->setLocalTranslator($this->getLocalTranslator());
    }

    /**
     * @alias diffAsCarbonInterval
     *
     * Get the difference as a DateInterval instance.
     * Return relative interval (negative if $absolute flag is not set to true and the given date is before
     * current one).
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return CarbonInterval
     */
    public function diff($date = null, bool $absolute = false, array $skip = []): CarbonInterval
    {
        return $this->diffAsCarbonInterval($date, $absolute, $skip);
    }

    /**
     * @param Unit|string                                            $unit     microsecond, millisecond, second, minute,
     *                                                                         hour, day, week, month, quarter, year,
     *                                                                         century, millennium
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     * @param bool                                                   $utc      Always convert dates to UTC before comparing (if not set, it will do it only if timezones are different)
     *
     * @return float
     */
    public function diffInUnit(Unit|string $unit, $date = null, bool $absolute = false, bool $utc = false): float
    {
        $unit = static::pluralUnit($unit instanceof Unit ? $unit->value : rtrim($unit, 'z'));
        $method = 'diffIn'.$unit;

        if (!method_exists($this, $method)) {
            throw new UnknownUnitException($unit);
        }

        return $this->$method($date, $absolute, $utc);
    }

    /**
     * Get the difference in years
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     * @param bool                                                   $utc      Always convert dates to UTC before comparing (if not set, it will do it only if timezones are different)
     *
     * @return float
     */
    public function diffInYears($date = null, bool $absolute = false, bool $utc = false): float
    {
        $start = $this;
        $end = $this->resolveCarbon($date);

        if ($utc) {
            $start = $start->avoidMutation()->utc();
            $end = $end->avoidMutation()->utc();
        }

        $ascending = ($start <= $end);
        $sign = $absolute || $ascending ? 1 : -1;

        if (!$ascending) {
            [$start, $end] = [$end, $start];
        }

        $yearsDiff = (int) $start->diff($end, $absolute)->format('%r%y');
        /** @var Carbon|CarbonImmutable $floorEnd */
        $floorEnd = $start->avoidMutation()->addYears($yearsDiff);

        if ($floorEnd >= $end) {
            return $sign * $yearsDiff;
        }

        /** @var Carbon|CarbonImmutable $ceilEnd */
        $ceilEnd = $start->avoidMutation()->addYears($yearsDiff + 1);

        $daysToFloor = $floorEnd->diffInDays($end);
        $daysToCeil = $end->diffInDays($ceilEnd);

        return $sign * ($yearsDiff + $daysToFloor / ($daysToCeil + $daysToFloor));
    }

    /**
     * Get the difference in quarters.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     * @param bool                                                   $utc      Always convert dates to UTC before comparing (if not set, it will do it only if timezones are different)
     *
     * @return float
     */
    public function diffInQuarters($date = null, bool $absolute = false, bool $utc = false): float
    {
        return $this->diffInMonths($date, $absolute, $utc) / static::MONTHS_PER_QUARTER;
    }

    /**
     * Get the difference in months.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     * @param bool                                                   $utc      Always convert dates to UTC before comparing (if not set, it will do it only if timezones are different)
     *
     * @return float
     */
    public function diffInMonths($date = null, bool $absolute = false, bool $utc = false): float
    {
        $start = $this;
        $end = $this->resolveCarbon($date);

        // Compare using UTC
        if ($utc || ($end->timezoneName !== $start->timezoneName)) {
            $start = $start->avoidMutation()->utc();
            $end = $end->avoidMutation()->utc();
        }

        [$yearStart, $monthStart, $dayStart] = explode('-', $start->format('Y-m-dHisu'));
        [$yearEnd, $monthEnd, $dayEnd] = explode('-', $end->format('Y-m-dHisu'));

        $monthsDiff = (((int) $yearEnd) - ((int) $yearStart)) * static::MONTHS_PER_YEAR +
            ((int) $monthEnd) - ((int) $monthStart);

        if ($monthsDiff > 0) {
            $monthsDiff -= ($dayStart > $dayEnd ? 1 : 0);
        } elseif ($monthsDiff < 0) {
            $monthsDiff += ($dayStart < $dayEnd ? 1 : 0);
        }

        $ascending = ($start <= $end);
        $sign = $absolute || $ascending ? 1 : -1;
        $monthsDiff = abs($monthsDiff);

        if (!$ascending) {
            [$start, $end] = [$end, $start];
        }

        /** @var Carbon|CarbonImmutable $floorEnd */
        $floorEnd = $start->avoidMutation()->addMonths($monthsDiff);

        if ($floorEnd >= $end) {
            return $sign * $monthsDiff;
        }

        /** @var Carbon|CarbonImmutable $ceilEnd */
        $ceilEnd = $start->avoidMutation()->addMonths($monthsDiff + 1);

        $daysToFloor = $floorEnd->diffInDays($end);
        $daysToCeil = $end->diffInDays($ceilEnd);

        return $sign * ($monthsDiff + $daysToFloor / ($daysToCeil + $daysToFloor));
    }

    /**
     * Get the difference in weeks.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     * @param bool                                                   $utc      Always convert dates to UTC before comparing (if not set, it will do it only if timezones are different)
     *
     * @return float
     */
    public function diffInWeeks($date = null, bool $absolute = false, bool $utc = false): float
    {
        return $this->diffInDays($date, $absolute, $utc) / static::DAYS_PER_WEEK;
    }

    /**
     * Get the difference in days.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     * @param bool                                                   $utc      Always convert dates to UTC before comparing (if not set, it will do it only if timezones are different)
     *
     * @return float
     */
    public function diffInDays($date = null, bool $absolute = false, bool $utc = false): float
    {
        $date = $this->resolveCarbon($date);
        $current = $this;

        // Compare using UTC
        if ($utc || ($date->timezoneName !== $current->timezoneName)) {
            $date = $date->avoidMutation()->utc();
            $current = $current->avoidMutation()->utc();
        }

        $negative = ($date < $current);
        [$start, $end] = $negative ? [$date, $current] : [$current, $date];
        $interval = $start->diffAsDateInterval($end);
        $daysA = $this->getIntervalDayDiff($interval);
        $floorEnd = $start->avoidMutation()->addDays($daysA);
        $daysB = $daysA + ($floorEnd <= $end ? 1 : -1);
        $ceilEnd = $start->avoidMutation()->addDays($daysB);
        $microsecondsBetween = $floorEnd->diffInMicroseconds($ceilEnd);
        $microsecondsToEnd = $floorEnd->diffInMicroseconds($end);

        return ($negative && !$absolute ? -1 : 1)
            * ($daysA * ($microsecondsBetween - $microsecondsToEnd) + $daysB * $microsecondsToEnd)
            / $microsecondsBetween;
    }

    /**
     * Get the difference in days using a filter closure.
     *
     * @param Closure                                                $callback
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return int
     */
    public function diffInDaysFiltered(Closure $callback, $date = null, bool $absolute = false): int
    {
        return $this->diffFiltered(CarbonInterval::day(), $callback, $date, $absolute);
    }

    /**
     * Get the difference in hours using a filter closure.
     *
     * @param Closure                                                $callback
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return int
     */
    public function diffInHoursFiltered(Closure $callback, $date = null, bool $absolute = false): int
    {
        return $this->diffFiltered(CarbonInterval::hour(), $callback, $date, $absolute);
    }

    /**
     * Get the difference by the given interval using a filter closure.
     *
     * @param CarbonInterval                                         $ci       An interval to traverse by
     * @param Closure                                                $callback
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return int
     */
    public function diffFiltered(CarbonInterval $ci, Closure $callback, $date = null, bool $absolute = false): int
    {
        $start = $this;
        $end = $this->resolveCarbon($date);
        $inverse = false;

        if ($end < $start) {
            $start = $end;
            $end = $this;
            $inverse = true;
        }

        $options = CarbonPeriod::EXCLUDE_END_DATE | ($this->isMutable() ? 0 : CarbonPeriod::IMMUTABLE);
        $diff = $ci->toPeriod($start, $end, $options)->filter($callback)->count();

        return $inverse && !$absolute ? -$diff : $diff;
    }

    /**
     * Get the difference in weekdays.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return int
     */
    public function diffInWeekdays($date = null, bool $absolute = false): int
    {
        return $this->diffInDaysFiltered(
            static fn (CarbonInterface $date) => $date->isWeekday(),
            $this->resolveCarbon($date)->avoidMutation()->modify($this->format('H:i:s.u')),
            $absolute,
        );
    }

    /**
     * Get the difference in weekend days using a filter.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return int
     */
    public function diffInWeekendDays($date = null, bool $absolute = false): int
    {
        return $this->diffInDaysFiltered(
            static fn (CarbonInterface $date) => $date->isWeekend(),
            $this->resolveCarbon($date)->avoidMutation()->modify($this->format('H:i:s.u')),
            $absolute,
        );
    }

    /**
     * Get the difference in hours.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return float
     */
    public function diffInHours($date = null, bool $absolute = false): float
    {
        return $this->diffInMinutes($date, $absolute) / static::MINUTES_PER_HOUR;
    }

    /**
     * Get the difference in minutes.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return float
     */
    public function diffInMinutes($date = null, bool $absolute = false): float
    {
        return $this->diffInSeconds($date, $absolute) / static::SECONDS_PER_MINUTE;
    }

    /**
     * Get the difference in seconds.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return float
     */
    public function diffInSeconds($date = null, bool $absolute = false): float
    {
        return $this->diffInMilliseconds($date, $absolute) / static::MILLISECONDS_PER_SECOND;
    }

    /**
     * Get the difference in microseconds.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return float
     */
    public function diffInMicroseconds($date = null, bool $absolute = false): float
    {
        /** @var CarbonInterface $date */
        $date = $this->resolveCarbon($date);
        $value = ($date->timestamp - $this->timestamp) * static::MICROSECONDS_PER_SECOND +
            $date->micro - $this->micro;

        return $absolute ? abs($value) : $value;
    }

    /**
     * Get the difference in milliseconds.
     *
     * @param \Carbon\CarbonInterface|\DateTimeInterface|string|null $date
     * @param bool                                                   $absolute Get the absolute of the difference
     *
     * @return float
     */
    public function diffInMilliseconds($date = null, bool $absolute = false): float
    {
        return $this->diffInMicroseconds($date, $absolute) / static::MICROSECONDS_PER_MILLISECOND;
    }

    /**
     * The number of seconds since midnight.
     *
     * @return float
     */
    public function secondsSinceMidnight(): float
    {
        return $this->diffInSeconds($this->copy()->startOfDay(), true);
    }

    /**
     * The number of seconds until 23:59:59.
     *
     * @return float
     */
    public function secondsUntilEndOfDay(): float
    {
        return $this->diffInSeconds($this->copy()->endOfDay(), true);
    }

    /**
     * Get the difference in a human readable format in the current locale from current instance to an other
     * instance given (or now if null given).
     *
     * @example
     * ```
     * echo Carbon::tomorrow()->diffForHumans() . "\n";
     * echo Carbon::tomorrow()->diffForHumans(['parts' => 2]) . "\n";
     * echo Carbon::tomorrow()->diffForHumans(['parts' => 3, 'join' => true]) . "\n";
     * echo Carbon::tomorrow()->diffForHumans(Carbon::yesterday()) . "\n";
     * echo Carbon::tomorrow()->diffForHumans(Carbon::yesterday(), ['short' => true]) . "\n";
     * ```
     *
     * @param Carbon|DateTimeInterface|string|array|null $other   if array passed, will be used as parameters array, see $syntax below;
     *                                                            if null passed, now will be used as comparison reference;
     *                                                            if any other type, it will be converted to date and used as reference.
     * @param int|array                                  $syntax  if array passed, parameters will be extracted from it, the array may contains:
     *                                                            ⦿ 'syntax' entry (see below)
     *                                                            ⦿ 'short' entry (see below)
     *                                                            ⦿ 'parts' entry (see below)
     *                                                            ⦿ 'options' entry (see below)
     *                                                            ⦿ 'skip' entry, list of units to skip (array of strings or a single string,
     *                                                            ` it can be the unit name (singular or plural) or its shortcut
     *                                                            ` (y, m, w, d, h, min, s, ms, µs).
     *                                                            ⦿ 'aUnit' entry, prefer "an hour" over "1 hour" if true
     *                                                            ⦿ 'altNumbers' entry, use alternative numbers if available
     *                                                            ` (from the current language if true is passed, from the given language(s)
     *                                                            ` if array or string is passed)
     *                                                            ⦿ 'join' entry determines how to join multiple parts of the string
     *                                                            `  - if $join is a string, it's used as a joiner glue
     *                                                            `  - if $join is a callable/closure, it get the list of string and should return a string
     *                                                            `  - if $join is an array, the first item will be the default glue, and the second item
     *                                                            `    will be used instead of the glue for the last item
     *                                                            `  - if $join is true, it will be guessed from the locale ('list' translation file entry)
     *                                                            `  - if $join is missing, a space will be used as glue
     *                                                            ⦿ 'other' entry (see above)
     *                                                            ⦿ 'minimumUnit' entry determines the smallest unit of time to display can be long or
     *                                                            `  short form of the units, e.g. 'hour' or 'h' (default value: s)
     *                                                            ⦿ 'locale' language in which the diff should be output (has no effect if 'translator' key is set)
     *                                                            ⦿ 'translator' a custom translator to use to translator the output.
     *                                                            if int passed, it adds modifiers:
     *                                                            Possible values:
     *                                                            - CarbonInterface::DIFF_ABSOLUTE          no modifiers
     *                                                            - CarbonInterface::DIFF_RELATIVE_TO_NOW   add ago/from now modifier
     *                                                            - CarbonInterface::DIFF_RELATIVE_TO_OTHER add before/after modifier
     *                                                            Default value: CarbonInterface::DIFF_ABSOLUTE
     * @param bool                                       $short   displays short format of time units
     * @param int                                        $parts   maximum number of parts to display (default value: 1: single unit)
     * @param int                                        $options human diff options
     */
    public function diffForHumans($other = null, $syntax = null, $short = false, $parts = 1, $options = null): string
    {
        /* @var CarbonInterface $this */
        if (\is_array($other)) {
            $other['syntax'] = \array_key_exists('syntax', $other) ? $other['syntax'] : $syntax;
            $syntax = $other;
            $other = $syntax['other'] ?? null;
        }

        $intSyntax = &$syntax;

        if (\is_array($syntax)) {
            $syntax['syntax'] = $syntax['syntax'] ?? null;
            $intSyntax = &$syntax['syntax'];
        }

        $intSyntax = (int) ($intSyntax ?? static::DIFF_RELATIVE_AUTO);
        $intSyntax = $intSyntax === static::DIFF_RELATIVE_AUTO && $other === null ? static::DIFF_RELATIVE_TO_NOW : $intSyntax;

        $parts = min(7, max(1, (int) $parts));
        $skip = \is_array($syntax) ? ($syntax['skip'] ?? []) : [];
        $options ??= $this->localHumanDiffOptions ?? $this->transmitFactory(
            static fn () => static::getHumanDiffOptions(),
        );

        return $this->diff($other, skip: (array) $skip)->forHumans($syntax, (bool) $short, $parts, $options);
    }

    /**
     * @alias diffForHumans
     *
     * Get the difference in a human readable format in the current locale from current instance to an other
     * instance given (or now if null given).
     *
     * @param Carbon|\DateTimeInterface|string|array|null $other   if array passed, will be used as parameters array, see $syntax below;
     *                                                             if null passed, now will be used as comparison reference;
     *                                                             if any other type, it will be converted to date and used as reference.
     * @param int|array                                   $syntax  if array passed, parameters will be extracted from it, the array may contains:
     *                                                             - 'syntax' entry (see below)
     *                                                             - 'short' entry (see below)
     *                                                             - 'parts' entry (see below)
     *                                                             - 'options' entry (see below)
     *                                                             - 'join' entry determines how to join multiple parts of the string
     *                                                             `  - if $join is a string, it's used as a joiner glue
     *                                                             `  - if $join is a callable/closure, it get the list of string and should return a string
     *                                                             `  - if $join is an array, the first item will be the default glue, and the second item
     *                                                             `    will be used instead of the glue for the last item
     *                                                             `  - if $join is true, it will be guessed from the locale ('list' translation file entry)
     *                                                             `  - if $join is missing, a space will be used as glue
     *                                                             - 'other' entry (see above)
     *                                                             if int passed, it add modifiers:
     *                                                             Possible values:
     *                                                             - CarbonInterface::DIFF_ABSOLUTE          no modifiers
     *                                                             - CarbonInterface::DIFF_RELATIVE_TO_NOW   add ago/from now modifier
     *                                                             - CarbonInterface::DIFF_RELATIVE_TO_OTHER add before/after modifier
     *                                                             Default value: CarbonInterface::DIFF_ABSOLUTE
     * @param bool                                        $short   displays short format of time units
     * @param int                                         $parts   maximum number of parts to display (default value: 1: single unit)
     * @param int                                         $options human diff options
     *
     * @return string
     */
    public function from($other = null, $syntax = null, $short = false, $parts = 1, $options = null)
    {
        return $this->diffForHumans($other, $syntax, $short, $parts, $options);
    }

    /**
     * @alias diffForHumans
     *
     * Get the difference in a human readable format in the current locale from current instance to an other
     * instance given (or now if null given).
     */
    public function since($other = null, $syntax = null, $short = false, $parts = 1, $options = null)
    {
        return $this->diffForHumans($other, $syntax, $short, $parts, $options);
    }

    /**
     * Get the difference in a human readable format in the current locale from an other
     * instance given (or now if null given) to current instance.
     *
     * When comparing a value in the past to default now:
     * 1 hour from now
     * 5 months from now
     *
     * When comparing a value in the future to default now:
     * 1 hour ago
     * 5 months ago
     *
     * When comparing a value in the past to another value:
     * 1 hour after
     * 5 months after
     *
     * When comparing a value in the future to another value:
     * 1 hour before
     * 5 months before
     *
     * @param Carbon|\DateTimeInterface|string|array|null $other   if array passed, will be used as parameters array, see $syntax below;
     *                                                             if null passed, now will be used as comparison reference;
     *                                                             if any other type, it will be converted to date and used as reference.
     * @param int|array                                   $syntax  if array passed, parameters will be extracted from it, the array may contains:
     *                                                             - 'syntax' entry (see below)
     *                                                             - 'short' entry (see below)
     *                                                             - 'parts' entry (see below)
     *                                                             - 'options' entry (see below)
     *                                                             - 'join' entry determines how to join multiple parts of the string
     *                                                             `  - if $join is a string, it's used as a joiner glue
     *                                                             `  - if $join is a callable/closure, it get the list of string and should return a string
     *                                                             `  - if $join is an array, the first item will be the default glue, and the second item
     *                                                             `    will be used instead of the glue for the last item
     *                                                             `  - if $join is true, it will be guessed from the locale ('list' translation file entry)
     *                                                             `  - if $join is missing, a space will be used as glue
     *                                                             - 'other' entry (see above)
     *                                                             if int passed, it add modifiers:
     *                                                             Possible values:
     *                                                             - CarbonInterface::DIFF_ABSOLUTE          no modifiers
     *                                                             - CarbonInterface::DIFF_RELATIVE_TO_NOW   add ago/from now modifier
     *                                                             - CarbonInterface::DIFF_RELATIVE_TO_OTHER add before/after modifier
     *                                                             Default value: CarbonInterface::DIFF_ABSOLUTE
     * @param bool                                        $short   displays short format of time units
     * @param int                                         $parts   maximum number of parts to display (default value: 1: single unit)
     * @param int                                         $options human diff options
     *
     * @return string
     */
    public function to($other = null, $syntax = null, $short = false, $parts = 1, $options = null)
    {
        if (!$syntax && !$other) {
            $syntax = CarbonInterface::DIFF_RELATIVE_TO_NOW;
        }

        return $this->resolveCarbon($other)->diffForHumans($this, $syntax, $short, $parts, $options);
    }

    /**
     * @alias to
     *
     * Get the difference in a human readable format in the current locale from an other
     * instance given (or now if null given) to current instance.
     *
     * @param Carbon|\DateTimeInterface|string|array|null $other   if array passed, will be used as parameters array, see $syntax below;
     *                                                             if null passed, now will be used as comparison reference;
     *                                                             if any other type, it will be converted to date and used as reference.
     * @param int|array                                   $syntax  if array passed, parameters will be extracted from it, the array may contains:
     *                                                             - 'syntax' entry (see below)
     *                                                             - 'short' entry (see below)
     *                                                             - 'parts' entry (see below)
     *                                                             - 'options' entry (see below)
     *                                                             - 'join' entry determines how to join multiple parts of the string
     *                                                             `  - if $join is a string, it's used as a joiner glue
     *                                                             `  - if $join is a callable/closure, it get the list of string and should return a string
     *                                                             `  - if $join is an array, the first item will be the default glue, and the second item
     *                                                             `    will be used instead of the glue for the last item
     *                                                             `  - if $join is true, it will be guessed from the locale ('list' translation file entry)
     *                                                             `  - if $join is missing, a space will be used as glue
     *                                                             - 'other' entry (see above)
     *                                                             if int passed, it add modifiers:
     *                                                             Possible values:
     *                                                             - CarbonInterface::DIFF_ABSOLUTE          no modifiers
     *                                                             - CarbonInterface::DIFF_RELATIVE_TO_NOW   add ago/from now modifier
     *                                                             - CarbonInterface::DIFF_RELATIVE_TO_OTHER add before/after modifier
     *                                                             Default value: CarbonInterface::DIFF_ABSOLUTE
     * @param bool                                        $short   displays short format of time units
     * @param int                                         $parts   maximum number of parts to display (default value: 1: single unit)
     * @param int                                         $options human diff options
     *
     * @return string
     */
    public function until($other = null, $syntax = null, $short = false, $parts = 1, $options = null)
    {
        return $this->to($other, $syntax, $short, $parts, $options);
    }

    /**
     * Get the difference in a human readable format in the current locale from current
     * instance to now.
     *
     * @param int|array $syntax  if array passed, parameters will be extracted from it, the array may contains:
     *                           - 'syntax' entry (see below)
     *                           - 'short' entry (see below)
     *                           - 'parts' entry (see below)
     *                           - 'options' entry (see below)
     *                           - 'join' entry determines how to join multiple parts of the string
     *                           `  - if $join is a string, it's used as a joiner glue
     *                           `  - if $join is a callable/closure, it get the list of string and should return a string
     *                           `  - if $join is an array, the first item will be the default glue, and the second item
     *                           `    will be used instead of the glue for the last item
     *                           `  - if $join is true, it will be guessed from the locale ('list' translation file entry)
     *                           `  - if $join is missing, a space will be used as glue
     *                           if int passed, it add modifiers:
     *                           Possible values:
     *                           - CarbonInterface::DIFF_ABSOLUTE          no modifiers
     *                           - CarbonInterface::DIFF_RELATIVE_TO_NOW   add ago/from now modifier
     *                           - CarbonInterface::DIFF_RELATIVE_TO_OTHER add before/after modifier
     *                           Default value: CarbonInterface::DIFF_ABSOLUTE
     * @param bool      $short   displays short format of time units
     * @param int       $parts   maximum number of parts to display (default value: 1: single unit)
     * @param int       $options human diff options
     *
     * @return string
     */
    public function fromNow($syntax = null, $short = false, $parts = 1, $options = null)
    {
        $other = null;

        if ($syntax instanceof DateTimeInterface) {
            [$other, $syntax, $short, $parts, $options] = array_pad(\func_get_args(), 5, null);
        }

        return $this->from($other, $syntax, $short, $parts, $options);
    }

    /**
     * Get the difference in a human readable format in the current locale from an other
     * instance given to now
     *
     * @param int|array $syntax  if array passed, parameters will be extracted from it, the array may contains:
     *                           - 'syntax' entry (see below)
     *                           - 'short' entry (see below)
     *                           - 'parts' entry (see below)
     *                           - 'options' entry (see below)
     *                           - 'join' entry determines how to join multiple parts of the string
     *                           `  - if $join is a string, it's used as a joiner glue
     *                           `  - if $join is a callable/closure, it get the list of string and should return a string
     *                           `  - if $join is an array, the first item will be the default glue, and the second item
     *                           `    will be used instead of the glue for the last item
     *                           `  - if $join is true, it will be guessed from the locale ('list' translation file entry)
     *                           `  - if $join is missing, a space will be used as glue
     *                           if int passed, it add modifiers:
     *                           Possible values:
     *                           - CarbonInterface::DIFF_ABSOLUTE          no modifiers
     *                           - CarbonInterface::DIFF_RELATIVE_TO_NOW   add ago/from now modifier
     *                           - CarbonInterface::DIFF_RELATIVE_TO_OTHER add before/after modifier
     *                           Default value: CarbonInterface::DIFF_ABSOLUTE
     * @param bool      $short   displays short format of time units
     * @param int       $parts   maximum number of parts to display (default value: 1: single part)
     * @param int       $options human diff options
     *
     * @return string
     */
    public function toNow($syntax = null, $short = false, $parts = 1, $options = null)
    {
        return $this->to(null, $syntax, $short, $parts, $options);
    }

    /**
     * Get the difference in a human readable format in the current locale from an other
     * instance given to now
     *
     * @param int|array $syntax  if array passed, parameters will be extracted from it, the array may contains:
     *                           - 'syntax' entry (see below)
     *                           - 'short' entry (see below)
     *                           - 'parts' entry (see below)
     *                           - 'options' entry (see below)
     *                           - 'join' entry determines how to join multiple parts of the string
     *                           `  - if $join is a string, it's used as a joiner glue
     *                           `  - if $join is a callable/closure, it get the list of string and should return a string
     *                           `  - if $join is an array, the first item will be the default glue, and the second item
     *                           `    will be used instead of the glue for the last item
     *                           `  - if $join is true, it will be guessed from the locale ('list' translation file entry)
     *                           `  - if $join is missing, a space will be used as glue
     *                           if int passed, it add modifiers:
     *                           Possible values:
     *                           - CarbonInterface::DIFF_ABSOLUTE          no modifiers
     *                           - CarbonInterface::DIFF_RELATIVE_TO_NOW   add ago/from now modifier
     *                           - CarbonInterface::DIFF_RELATIVE_TO_OTHER add before/after modifier
     *                           Default value: CarbonInterface::DIFF_ABSOLUTE
     * @param bool      $short   displays short format of time units
     * @param int       $parts   maximum number of parts to display (default value: 1: single part)
     * @param int       $options human diff options
     *
     * @return string
     */
    public function ago($syntax = null, $short = false, $parts = 1, $options = null)
    {
        $other = null;

        if ($syntax instanceof DateTimeInterface) {
            [$other, $syntax, $short, $parts, $options] = array_pad(\func_get_args(), 5, null);
        }

        return $this->from($other, $syntax, $short, $parts, $options);
    }

    /**
     * Get the difference in a human-readable format in the current locale from current instance to another
     * instance given (or now if null given).
     *
     * @return string
     */
    public function timespan($other = null, $timezone = null): string
    {
        if (\is_string($other)) {
            $other = $this->transmitFactory(static fn () => static::parse($other, $timezone));
        }

        return $this->diffForHumans($other, [
            'join' => ', ',
            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
            'parts' => INF,
        ]);
    }

    /**
     * Returns either day of week + time (e.g. "Last Friday at 3:30 PM") if reference time is within 7 days,
     * or a calendar date (e.g. "10/29/2017") otherwise.
     *
     * Language, date and time formats will change according to the current locale.
     *
     * @param Carbon|\DateTimeInterface|string|null $referenceTime
     * @param array                                 $formats
     *
     * @return string
     */
    public function calendar($referenceTime = null, array $formats = [])
    {
        /** @var CarbonInterface $current */
        $current = $this->avoidMutation()->startOfDay();
        /** @var CarbonInterface $other */
        $other = $this->resolveCarbon($referenceTime)->avoidMutation()->setTimezone($this->getTimezone())->startOfDay();
        $diff = $other->diffInDays($current, false);
        $format = $diff <= -static::DAYS_PER_WEEK ? 'sameElse' : (
            $diff < -1 ? 'lastWeek' : (
                $diff < 0 ? 'lastDay' : (
                    $diff < 1 ? 'sameDay' : (
                        $diff < 2 ? 'nextDay' : (
                            $diff < static::DAYS_PER_WEEK ? 'nextWeek' : 'sameElse'
                        )
                    )
                )
            )
        );
        $format = array_merge($this->getCalendarFormats(), $formats)[$format];
        if ($format instanceof Closure) {
            $format = $format($current, $other) ?? '';
        }

        return $this->isoFormat((string) $format);
    }

    private function getIntervalDayDiff(DateInterval $interval): int
    {
        return (int) $interval->format('%r%a');
    }
}
