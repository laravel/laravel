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

use Carbon\CarbonConverterInterface;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\Exceptions\InvalidFormatException;
use Carbon\Exceptions\InvalidIntervalException;
use Carbon\Exceptions\UnitException;
use Carbon\Exceptions\UnsupportedUnitException;
use Carbon\OverflowMode;
use Carbon\Unit;
use Closure;
use DateInterval;
use DateMalformedStringException;
use InvalidArgumentException;
use ReturnTypeWillChange;

/**
 * Trait Units.
 *
 * Add, subtract and set units.
 */
trait Units
{
    /**
     * @deprecated Prefer to use add addUTCUnit() which more accurately defines what it's doing.
     *
     * Add seconds to the instance using timestamp. Positive $value travels
     * forward while negative $value travels into the past.
     *
     * @param string         $unit
     * @param int|float|null $value
     *
     * @return static
     */
    public function addRealUnit(string $unit, $value = 1): static
    {
        return $this->addUTCUnit($unit, $value);
    }

    /**
     * Add seconds to the instance using timestamp. Positive $value travels
     * forward while negative $value travels into the past.
     *
     * @param string         $unit
     * @param int|float|null $value
     *
     * @return static
     */
    public function addUTCUnit(string $unit, $value = 1): static
    {
        $value ??= 0;

        switch ($unit) {
            // @call addUTCUnit
            case 'micro':

            // @call addUTCUnit
            case 'microsecond':
                /* @var CarbonInterface $this */
                $diff = $this->microsecond + $value;
                $time = $this->getTimestamp();
                $seconds = (int) floor($diff / static::MICROSECONDS_PER_SECOND);
                $time += $seconds;
                $diff -= $seconds * static::MICROSECONDS_PER_SECOND;
                $microtime = str_pad((string) $diff, 6, '0', STR_PAD_LEFT);
                $timezone = $this->tz;

                return $this->tz('UTC')->modify("@$time.$microtime")->setTimezone($timezone);

            // @call addUTCUnit
            case 'milli':
            // @call addUTCUnit
            case 'millisecond':
                return $this->addUTCUnit('microsecond', $value * static::MICROSECONDS_PER_MILLISECOND);

            // @call addUTCUnit
            case 'second':
                break;

            // @call addUTCUnit
            case 'minute':
                $value *= static::SECONDS_PER_MINUTE;

                break;

            // @call addUTCUnit
            case 'hour':
                $value *= static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE;

                break;

            // @call addUTCUnit
            case 'day':
                $value *= static::HOURS_PER_DAY * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE;

                break;

            // @call addUTCUnit
            case 'week':
                $value *= static::DAYS_PER_WEEK * static::HOURS_PER_DAY * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE;

                break;

            // @call addUTCUnit
            case 'month':
                $value *= 30 * static::HOURS_PER_DAY * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE;

                break;

            // @call addUTCUnit
            case 'quarter':
                $value *= static::MONTHS_PER_QUARTER * 30 * static::HOURS_PER_DAY * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE;

                break;

            // @call addUTCUnit
            case 'year':
                $value *= 365 * static::HOURS_PER_DAY * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE;

                break;

            // @call addUTCUnit
            case 'decade':
                $value *= static::YEARS_PER_DECADE * 365 * static::HOURS_PER_DAY * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE;

                break;

            // @call addUTCUnit
            case 'century':
                $value *= static::YEARS_PER_CENTURY * 365 * static::HOURS_PER_DAY * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE;

                break;

            // @call addUTCUnit
            case 'millennium':
                $value *= static::YEARS_PER_MILLENNIUM * 365 * static::HOURS_PER_DAY * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE;

                break;

            default:
                if ($this->isLocalStrictModeEnabled()) {
                    throw new UnitException("Invalid unit for real timestamp add/sub: '$unit'");
                }

                return $this;
        }

        $seconds = (int) $value;
        $microseconds = (int) round(
            (abs($value) - abs($seconds)) * ($value < 0 ? -1 : 1) * static::MICROSECONDS_PER_SECOND,
        );
        $date = $this->setTimestamp($this->getTimestamp() + $seconds);

        return $microseconds ? $date->addUTCUnit('microsecond', $microseconds) : $date;
    }

    /**
     * @deprecated Prefer to use add subUTCUnit() which more accurately defines what it's doing.
     *
     * Subtract seconds to the instance using timestamp. Positive $value travels
     * into the past while negative $value travels forward.
     *
     * @param string $unit
     * @param int    $value
     *
     * @return static
     */
    public function subRealUnit($unit, $value = 1): static
    {
        return $this->addUTCUnit($unit, -$value);
    }

    /**
     * Subtract seconds to the instance using timestamp. Positive $value travels
     * into the past while negative $value travels forward.
     *
     * @param string $unit
     * @param int    $value
     *
     * @return static
     */
    public function subUTCUnit($unit, $value = 1): static
    {
        return $this->addUTCUnit($unit, -$value);
    }

    /**
     * Returns true if a property can be changed via setter.
     *
     * @param string $unit
     *
     * @return bool
     */
    public static function isModifiableUnit($unit): bool
    {
        static $modifiableUnits = [
            // @call addUnit
            'millennium',
            // @call addUnit
            'century',
            // @call addUnit
            'decade',
            // @call addUnit
            'quarter',
            // @call addUnit
            'week',
            // @call addUnit
            'weekday',
        ];

        return \in_array($unit, $modifiableUnits, true) || \in_array($unit, static::$units, true);
    }

    /**
     * Call native PHP DateTime/DateTimeImmutable add() method.
     *
     * @param DateInterval $interval
     *
     * @return static
     */
    public function rawAdd(DateInterval $interval): static
    {
        return parent::add($interval);
    }

    /**
     * Add given units or interval to the current instance.
     *
     * @example $date->add('hour', 3)
     * @example $date->add(15, 'days')
     * @example $date->add(CarbonInterval::days(4))
     *
     * @param Unit|int|string|DateInterval|Closure|CarbonConverterInterface $unit
     * @param Unit|int|float|string                                         $value
     * @param OverflowMode|bool|null                                        $overflow
     * @param int|null                                                      $anchorDay
     *
     * @return static
     */
    #[ReturnTypeWillChange]
    public function add($unit, $value = 1, OverflowMode|bool|null $overflow = null, ?int $anchorDay = null): static
    {
        $unit = Unit::toNameIfUnit($unit);
        $value = Unit::toNameIfUnit($value);

        if (\is_string($unit) && \func_num_args() === 1) {
            $unit = CarbonInterval::make($unit, [], true);
        }

        if ($unit instanceof CarbonConverterInterface) {
            $unit = Closure::fromCallable([$unit, 'convertDate']);
        }

        if ($unit instanceof Closure) {
            $inverted = ($value < 0);
            $result = $this;

            self::disallowDecimalPart($value);

            for ($i = abs($value); $i > 0; $i--) {
                $result = $result->resolveCarbon($unit($result, $inverted));
            }

            if ($this !== $result && $this->isMutable()) {
                return $this->modify($result->rawFormat('Y-m-d H:i:s.u e O'));
            }

            return $result;
        }

        if ($unit instanceof DateInterval) {
            return parent::add($unit);
        }

        if (is_numeric($unit)) {
            [$value, $unit] = [$unit, $value];
        }

        return $this->addUnit((string) $unit, $value, $overflow, $anchorDay);
    }

    /**
     * Add given units to the current instance.
     */
    public function addUnit(
        Unit|string $unit,
        $value = 1,
        OverflowMode|bool|null $overflow = null,
        ?int $anchorDay = null,
    ): static {
        $unit = Unit::toName($unit);

        $overflow = $this->getOverflowMode($overflow, $anchorDay);

        $originalArgs = \func_get_args();

        $date = $this;

        if (!is_numeric($value) || !(float) $value) {
            return $date->isMutable() ? $date : $date->copy();
        }

        $unit = self::singularUnit($unit);
        $metaUnits = [
            'millennium' => [static::YEARS_PER_MILLENNIUM, 'year'],
            'century' => [static::YEARS_PER_CENTURY, 'year'],
            'decade' => [static::YEARS_PER_DECADE, 'year'],
            'quarter' => [static::MONTHS_PER_QUARTER, 'month'],
        ];

        if (isset($metaUnits[$unit])) {
            [$factor, $unit] = $metaUnits[$unit];
            $value *= $factor;
        }

        if ($overflow === OverflowMode::AnchorDay) {
            $anchorDay ??= $date->day;
            $overflow = OverflowMode::NoOverflow;
        }

        if ($unit === 'weekday') {
            $weekendDays = $this->transmitFactory(static::getWeekendDays(...));

            if ($weekendDays !== [static::SATURDAY, static::SUNDAY]) {
                $absoluteValue = abs($value);
                $sign = $value / max(1, $absoluteValue);
                $weekDaysCount = static::DAYS_PER_WEEK - min(static::DAYS_PER_WEEK - 1, \count(array_unique($weekendDays)));
                $weeks = floor($absoluteValue / $weekDaysCount);

                for ($diff = $absoluteValue % $weekDaysCount; $diff; $diff--) {
                    /** @var static $date */
                    $date = $date->addDays($sign);

                    while (\in_array($date->dayOfWeek, $weekendDays, true)) {
                        $date = $date->addDays($sign);
                    }
                }

                $value = $weeks * $sign;
                $unit = 'week';
            }

            $timeString = $date->toTimeString();
        } elseif ($canOverflow = (\in_array($unit, [
                'month',
                'year',
            ]) && ($overflow === OverflowMode::NoOverflow || (
                $overflow === null &&
                !$this->shouldUnitOverflow($unit)
            )))) {
            $day = $date->day;
        }

        if ($unit === 'milli' || $unit === 'millisecond') {
            $unit = 'microsecond';
            $value *= static::MICROSECONDS_PER_MILLISECOND;
        }

        $previousException = null;

        try {
            $date = self::rawAddUnit($date, $unit, $value);

            if ($date !== null) {
                if (isset($timeString)) {
                    $date = $date->setTimeFromTimeString($timeString);
                } elseif (isset($canOverflow, $day) && $canOverflow && $day !== $date->day) {
                    $date = $date->modify('last day of previous month');
                }

                if ($anchorDay !== null) {
                    $date = $date->setAnchorDay($anchorDay);
                }
            }
        } catch (DateMalformedStringException|InvalidFormatException|UnsupportedUnitException $exception) {
            $date = null;
            $previousException = $exception;
        }

        return $date ?? throw new UnitException(
            'Unable to add unit '.var_export($originalArgs, true),
            previous: $previousException,
        );
    }

    /**
     * Subtract given units to the current instance.
     */
    public function subUnit(
        Unit|string $unit,
        $value = 1,
        OverflowMode|bool|null $overflow = null,
        ?int $anchorDay = null,
    ): static {
        return $this->addUnit($unit, -$value, $overflow, $anchorDay);
    }

    /**
     * Call native PHP DateTime/DateTimeImmutable sub() method.
     */
    public function rawSub(DateInterval $interval): static
    {
        return parent::sub($interval);
    }

    /**
     * Subtract given units or interval to the current instance.
     *
     * @example $date->sub('hour', 3)
     * @example $date->sub(15, 'days')
     * @example $date->sub(CarbonInterval::days(4))
     *
     * @param Unit|int|string|DateInterval|Closure|CarbonConverterInterface $unit
     * @param Unit|int|float|string                                         $value
     * @param OverflowMode|bool|null                                        $overflow
     * @param int|null                                                      $anchorDay
     *
     * @return static
     */
    #[ReturnTypeWillChange]
    public function sub($unit, $value = 1, OverflowMode|bool|null $overflow = null, ?int $anchorDay = null): static
    {
        $unit = Unit::toNameIfUnit($unit);
        $value = Unit::toNameIfUnit($value);

        if (\is_string($unit) && \func_num_args() === 1) {
            $unit = CarbonInterval::make($unit, [], true);
        }

        if ($unit instanceof CarbonConverterInterface) {
            $unit = Closure::fromCallable([$unit, 'convertDate']);
        }

        if ($unit instanceof Closure) {
            $inverted = ($value < 0);
            $result = $this;

            self::disallowDecimalPart($value);

            for ($i = abs($value); $i > 0; $i--) {
                $result = $result->resolveCarbon($unit($result, !$inverted));
            }

            if ($this !== $result && $this->isMutable()) {
                return $this->modify($result->rawFormat('Y-m-d H:i:s.u e O'));
            }

            return $result;
        }

        if ($unit instanceof DateInterval) {
            return parent::sub($unit);
        }

        if (is_numeric($unit)) {
            [$value, $unit] = [$unit, $value];
        }

        return $this->addUnit((string) $unit, -(float) $value, $overflow, $anchorDay);
    }

    /**
     * Subtract given units or interval to the current instance.
     *
     * @see sub()
     *
     * @param Unit|int|string|DateInterval $unit
     * @param Unit|int|float|string        $value
     * @param OverflowMode|bool|null       $overflow
     * @param int|null                     $anchorDay
     *
     * @return static
     */
    public function subtract($unit, $value = 1, OverflowMode|bool|null $overflow = null, ?int $anchorDay = null): static
    {
        if (\is_string($unit) && \func_num_args() === 1) {
            $unit = CarbonInterval::make($unit, [], true);
        }

        return $this->sub($unit, $value, $overflow, $anchorDay);
    }

    /**
     * Add given amount of time to the current date.
     *
     * @SuppressWarnings(ExcessiveParameterList)
     */
    private function doPlus(
        int $years = 0,
        int $months = 0,
        int|float $weeks = 0,
        int|float $days = 0,
        int|float $hours = 0,
        int|float $minutes = 0,
        int|float $seconds = 0,
        int|float $microseconds = 0,
        OverflowMode|bool|null $overflow = null,
        ?int $anchorDay = null,
    ): static {
        return $this->addUnit(Unit::Year, $years, $overflow, $anchorDay)
            ->addUnit(Unit::Month, $months, $overflow, $anchorDay)
            ->add("
                $weeks weeks $days days
                $hours hours $minutes minutes $seconds seconds $microseconds microseconds
            ");
    }

    /**
     * Subtract given amount of time to the current date.
     *
     * @SuppressWarnings(ExcessiveParameterList)
     */
    private function doMinus(
        int $years = 0,
        int $months = 0,
        int|float $weeks = 0,
        int|float $days = 0,
        int|float $hours = 0,
        int|float $minutes = 0,
        int|float $seconds = 0,
        int|float $microseconds = 0,
        OverflowMode|bool|null $overflow = null,
        ?int $anchorDay = null,
    ): static {
        return $this->subUnit(Unit::Year, $years, $overflow, $anchorDay)
            ->subUnit(Unit::Month, $months, $overflow, $anchorDay)
            ->sub("
                $weeks weeks $days days
                $hours hours $minutes minutes $seconds seconds $microseconds microseconds
            ");
    }

    private function callPlusOrMinus(string $method, array $parameters): ?static
    {
        return match ($method) {
            'plus' => $this->doPlus(...$parameters),
            'minus' => $this->doMinus(...$parameters),
            default => null,
        };
    }

    private static function rawAddUnit(self $date, string $unit, int|float $value): ?static
    {
        try {
            $absoluteValue = abs($value);

            return $date->rawAdd(
                CarbonInterval::fromString(self::getNumberAsString($absoluteValue)." $unit")
                    ->invert($value < 0),
            );
        } catch (InvalidIntervalException $exception) {
            try {
                return $date->modify(self::getNumberAsString($value)." $unit");
            } catch (InvalidFormatException) {
                throw new UnsupportedUnitException($unit, previous: $exception);
            }
        }
    }

    private static function getNumberAsString(int|float $value): string
    {
        $stringValue = (string) $value;

        if ($value < -1 || $value > 1) {
            if (str_contains($stringValue, 'E')) {
                return number_format($value, 0, '.', '');
            }

            return $stringValue;
        }

        if (str_contains($stringValue, 'E')) {
            return number_format($value, 14, '.', '');
        }

        return $stringValue;
    }

    /**
     * Set current day of the instance to the passed value if it exits in the
     * current month, else set current day to the last day of the month.
     */
    public function setAnchorDay(int $anchorDay): static
    {
        if ($anchorDay < 1) {
            throw new InvalidArgumentException('$anchorDay must be greater than 0');
        }

        return $this->day(min($anchorDay, $this->daysInMonth));
    }

    private static function disallowDecimalPart(mixed $value): void
    {
        if (((float) $value) !== ((float) (int) $value)) {
            throw new InvalidArgumentException(
                'Interval objects cannot be multiplied by a non-integer value.',
            );
        }
    }

    private function getOverflowMode(
        OverflowMode|bool|null $overflow = null,
        ?int $anchorDay = null,
    ): ?OverflowMode {
        if ($anchorDay !== null) {
            $overflow ??= OverflowMode::AnchorDay;

            if ($overflow !== OverflowMode::AnchorDay) {
                throw new InvalidArgumentException(
                    '$anchorDay can be set only $overflow = OverflowMode::AnchorDay',
                );
            }
        }

        return match ($overflow) {
            true => OverflowMode::Overflow,
            false => OverflowMode::NoOverflow,
            default => $overflow,
        };
    }

    private function shouldUnitOverflow(string $unit): bool
    {
        $ucUnit = ucfirst($unit).'s';

        return $this->{'local'.$ucUnit.'Overflow'} ?? static::{'shouldOverflow'.$ucUnit}();
    }
}
