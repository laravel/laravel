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

use Carbon\Exceptions\InvalidCastException;
use Carbon\Exceptions\InvalidTimeZoneException;
use Carbon\Traits\LocalFactory;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Throwable;

class CarbonTimeZone extends DateTimeZone
{
    use LocalFactory;

    public const MAXIMUM_TIMEZONE_OFFSET = 99;

    public function __construct(string|int|float $timezone)
    {
        $this->initLocalFactory();

        parent::__construct(static::getDateTimeZoneNameFromMixed($timezone));
    }

    protected static function parseNumericTimezone(string|int|float $timezone): string
    {
        if (abs((float) $timezone) > static::MAXIMUM_TIMEZONE_OFFSET) {
            throw new InvalidTimeZoneException(
                'Absolute timezone offset cannot be greater than '.
                static::MAXIMUM_TIMEZONE_OFFSET.'.',
            );
        }

        return ($timezone >= 0 ? '+' : '').ltrim((string) $timezone, '+').':00';
    }

    protected static function getDateTimeZoneNameFromMixed(string|int|float $timezone): string
    {
        if (\is_string($timezone)) {
            $timezone = preg_replace('/^\s*([+-]\d+)(\d{2})\s*$/', '$1:$2', $timezone);
        }

        if (is_numeric($timezone)) {
            return static::parseNumericTimezone($timezone);
        }

        return $timezone;
    }

    /**
     * Cast the current instance into the given class.
     *
     * @param class-string<DateTimeZone> $className The $className::instance() method will be called to cast the current object.
     *
     * @return DateTimeZone|mixed
     */
    public function cast(string $className): mixed
    {
        if (!method_exists($className, 'instance')) {
            if (is_a($className, DateTimeZone::class, true)) {
                return new $className($this->getName());
            }

            throw new InvalidCastException("$className has not the instance() method needed to cast the date.");
        }

        return $className::instance($this);
    }

    /**
     * Create a CarbonTimeZone from mixed input.
     *
     * @param DateTimeZone|string|int|false|null $object     original value to get CarbonTimeZone from it.
     * @param DateTimeZone|string|int|false|null $objectDump dump of the object for error messages.
     *
     * @throws InvalidTimeZoneException
     *
     * @return static|null
     */
    public static function instance(
        DateTimeZone|string|int|false|null $object,
        DateTimeZone|string|int|false|null $objectDump = null,
    ): ?self {
        $timezone = $object;

        if ($timezone instanceof static) {
            return $timezone;
        }

        if ($timezone === null || $timezone === false) {
            return null;
        }

        try {
            if (!($timezone instanceof DateTimeZone)) {
                $name = static::getDateTimeZoneNameFromMixed($object);
                $timezone = new static($name);
            }

            return $timezone instanceof static ? $timezone : new static($timezone->getName());
        } catch (Exception $exception) {
            throw new InvalidTimeZoneException(
                'Unknown or bad timezone ('.($objectDump ?: $object).')',
                previous: $exception,
            );
        }
    }

    /**
     * Returns abbreviated name of the current timezone according to DST setting.
     *
     * @param bool $dst
     *
     * @return string
     */
    public function getAbbreviatedName(bool $dst = false): string
    {
        $name = $this->getName();

        $date = new DateTimeImmutable($dst ? 'July 1' : 'January 1', $this);
        $timezone = $date->format('T');
        $abbreviations = $this->listAbbreviations();
        $matchingZones = array_merge($abbreviations[$timezone] ?? [], $abbreviations[strtolower($timezone)] ?? []);

        if ($matchingZones !== []) {
            foreach ($matchingZones as $zone) {
                if ($zone['timezone_id'] === $name && $zone['dst'] == $dst) {
                    return $timezone;
                }
            }
        }

        foreach ($abbreviations as $abbreviation => $zones) {
            foreach ($zones as $zone) {
                if ($zone['timezone_id'] === $name && $zone['dst'] == $dst) {
                    return strtoupper($abbreviation);
                }
            }
        }

        return 'unknown';
    }

    /**
     * @alias getAbbreviatedName
     *
     * Returns abbreviated name of the current timezone according to DST setting.
     *
     * @param bool $dst
     *
     * @return string
     */
    public function getAbbr(bool $dst = false): string
    {
        return $this->getAbbreviatedName($dst);
    }

    /**
     * Get the offset as string "sHH:MM" (such as "+00:00" or "-12:30").
     */
    public function toOffsetName(?DateTimeInterface $date = null): string
    {
        return static::getOffsetNameFromMinuteOffset(
            $this->getOffset($this->resolveCarbon($date)) / 60,
        );
    }

    /**
     * Returns a new CarbonTimeZone object using the offset string instead of region string.
     */
    public function toOffsetTimeZone(?DateTimeInterface $date = null): static
    {
        return new static($this->toOffsetName($date));
    }

    /**
     * Returns the first region string (such as "America/Toronto") that matches the current timezone or
     * false if no match is found.
     *
     * @see timezone_name_from_abbr native PHP function.
     */
    public function toRegionName(?DateTimeInterface $date = null, int $isDST = 1): ?string
    {
        $name = $this->getName();
        $firstChar = substr($name, 0, 1);

        if ($firstChar !== '+' && $firstChar !== '-') {
            return $name;
        }

        $date = $this->resolveCarbon($date);

        // Integer construction no longer supported since PHP 8
        // @codeCoverageIgnoreStart
        try {
            $offset = @$this->getOffset($date) ?: 0;
        } catch (Throwable) {
            $offset = 0;
        }
        // @codeCoverageIgnoreEnd

        $name = @timezone_name_from_abbr('', $offset, $isDST);

        if ($name) {
            return $name;
        }

        foreach (timezone_identifiers_list() as $timezone) {
            if (Carbon::instance($date)->setTimezone($timezone)->getOffset() === $offset) {
                return $timezone;
            }
        }

        return null;
    }

    /**
     * Returns a new CarbonTimeZone object using the region string instead of offset string.
     */
    public function toRegionTimeZone(?DateTimeInterface $date = null): ?self
    {
        $timezone = $this->toRegionName($date);

        if ($timezone !== null) {
            return new static($timezone);
        }

        if (Carbon::isStrictModeEnabled()) {
            throw new InvalidTimeZoneException('Unknown timezone for offset '.$this->getOffset($this->resolveCarbon($date)).' seconds.');
        }

        return null;
    }

    /**
     * Cast to string (get timezone name).
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Return the type number:
     *
     * Type 1; A UTC offset, such as -0300
     * Type 2; A timezone abbreviation, such as GMT
     * Type 3: A timezone identifier, such as Europe/London
     */
    public function getType(): int
    {
        return preg_match('/"timezone_type";i:(\d)/', serialize($this), $match) ? (int) $match[1] : 3;
    }

    /**
     * Create a CarbonTimeZone from mixed input.
     *
     * @param DateTimeZone|string|int|null $object
     *
     * @return false|static
     */
    public static function create($object = null)
    {
        return static::instance($object);
    }

    /**
     * Create a CarbonTimeZone from int/float hour offset.
     *
     * @param float $hourOffset number of hour of the timezone shift (can be decimal).
     *
     * @return false|static
     */
    public static function createFromHourOffset(float $hourOffset)
    {
        return static::createFromMinuteOffset($hourOffset * Carbon::MINUTES_PER_HOUR);
    }

    /**
     * Create a CarbonTimeZone from int/float minute offset.
     *
     * @param float $minuteOffset number of total minutes of the timezone shift.
     *
     * @return false|static
     */
    public static function createFromMinuteOffset(float $minuteOffset)
    {
        return static::instance(static::getOffsetNameFromMinuteOffset($minuteOffset));
    }

    /**
     * Convert a total minutes offset into a standardized timezone offset string.
     *
     * @param float $minutes number of total minutes of the timezone shift.
     *
     * @return string
     */
    public static function getOffsetNameFromMinuteOffset(float $minutes): string
    {
        $minutes = round($minutes);
        $unsignedMinutes = abs($minutes);

        return ($minutes < 0 ? '-' : '+').
            str_pad((string) floor($unsignedMinutes / 60), 2, '0', STR_PAD_LEFT).
            ':'.
            str_pad((string) ($unsignedMinutes % 60), 2, '0', STR_PAD_LEFT);
    }

    private function resolveCarbon(?DateTimeInterface $date): DateTimeInterface
    {
        if ($date) {
            return $date;
        }

        if (isset($this->clock)) {
            return $this->clock->now()->setTimezone($this);
        }

        return Carbon::now($this);
    }
}
