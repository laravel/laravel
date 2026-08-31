<?php

namespace Faker\Core;

use Faker\Extension\DateTimeExtension;
use Faker\Extension\GeneratorAwareExtension;
use Faker\Extension\GeneratorAwareExtensionTrait;
use Faker\Extension\Helper;

/**
 * @experimental This class is experimental and does not fall under our BC promise
 *
 * @since 1.20.0
 */
final class DateTime implements DateTimeExtension, GeneratorAwareExtension
{
    use GeneratorAwareExtensionTrait;

    /**
     * @var string[]
     */
    private array $centuries = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII', 'XIV', 'XV', 'XVI', 'XVII', 'XVIII', 'XIX', 'XX', 'XXI'];

    private ?string $defaultTimezone = null;

    /**
     * Get the POSIX-timestamp of a DateTime, int or string.
     *
     * @param \DateTime|float|int|string $until
     *
     * @return false|int
     */
    private function getTimestamp($until = 'now')
    {
        if (is_numeric($until)) {
            return (int) $until;
        }

        if ($until instanceof \DateTime) {
            return $until->getTimestamp();
        }

        return strtotime(empty($until) ? 'now' : $until);
    }

    /**
     * Get a DateTime created based on a POSIX-timestamp.
     *
     * @param int $timestamp the UNIX / POSIX-compatible timestamp
     */
    private function getTimestampDateTime(int $timestamp): \DateTime
    {
        return new \DateTime('@' . $timestamp);
    }

    private function resolveTimezone(?string $timezone): string
    {
        if ($timezone !== null) {
            return $timezone;
        }

        return null === $this->defaultTimezone ? date_default_timezone_get() : $this->defaultTimezone;
    }

    /**
     * Internal method to set the timezone on a DateTime object.
     */
    private function setTimezone(\DateTime $dateTime, ?string $timezone): \DateTime
    {
        $timezone = $this->resolveTimezone($timezone);

        return $dateTime->setTimezone(new \DateTimeZone($timezone));
    }

    public function dateTime($until = 'now', ?string $timezone = null): \DateTime
    {
        return $this->setTimezone(
            $this->getTimestampDateTime($this->unixTime($until)),
            $timezone,
        );
    }

    public function dateTimeAD($until = 'now', ?string $timezone = null): \DateTime
    {
        $min = (PHP_INT_SIZE > 4) ? -62135597361 : -PHP_INT_MAX;

        return $this->setTimezone(
            $this->getTimestampDateTime($this->generator->numberBetween($min, $this->getTimestamp($until))),
            $timezone,
        );
    }

    public function dateTimeBetween($from = '-30 years', $until = 'now', ?string $timezone = null): \DateTime
    {
        $start = $this->getTimestamp($from);
        $end = $this->getTimestamp($until);

        if ($start > $end) {
            throw new \InvalidArgumentException('"$from" must be anterior to "$until".');
        }

        $timestamp = $this->generator->numberBetween($start, $end);

        return $this->setTimezone(
            $this->getTimestampDateTime($timestamp),
            $timezone,
        );
    }

    public function dateTimeInInterval($from = '-30 years', string $interval = '+5 days', ?string $timezone = null): \DateTime
    {
        $intervalObject = \DateInterval::createFromDateString($interval);
        $datetime = $from instanceof \DateTime ? $from : new \DateTime($from);

        $other = (clone $datetime)->add($intervalObject);

        $begin = min($datetime, $other);
        $end = $datetime === $begin ? $other : $datetime;

        return $this->dateTimeBetween($begin, $end, $timezone);
    }

    public function dateTimeThisWeek($until = 'sunday this week', ?string $timezone = null): \DateTime
    {
        return $this->dateTimeBetween('monday this week', $until, $timezone);
    }

    public function dateTimeThisMonth($until = 'last day of this month', ?string $timezone = null): \DateTime
    {
        return $this->dateTimeBetween('first day of this month', $until, $timezone);
    }

    public function dateTimeThisYear($until = 'last day of december', ?string $timezone = null): \DateTime
    {
        return $this->dateTimeBetween('first day of january', $until, $timezone);
    }

    public function dateTimeThisDecade($until = 'now', ?string $timezone = null): \DateTime
    {
        $year = floor(date('Y') / 10) * 10;

        return $this->dateTimeBetween("first day of january $year", $until, $timezone);
    }

    public function dateTimeThisCentury($until = 'now', ?string $timezone = null): \DateTime
    {
        $year = floor(date('Y') / 100) * 100;

        return $this->dateTimeBetween("first day of january $year", $until, $timezone);
    }

    public function date(string $format = 'Y-m-d', $until = 'now'): string
    {
        return $this->dateTime($until)->format($format);
    }

    public function time(string $format = 'H:i:s', $until = 'now'): string
    {
        return $this->date($format, $until);
    }

    public function unixTime($until = 'now'): int
    {
        return $this->generator->numberBetween(0, $this->getTimestamp($until));
    }

    public function iso8601($until = 'now'): string
    {
        return $this->date(\DateTime::ISO8601, $until);
    }

    public function amPm($until = 'now'): string
    {
        return $this->date('a', $until);
    }

    public function dayOfMonth($until = 'now'): string
    {
        return $this->date('d', $until);
    }

    public function dayOfWeek($until = 'now'): string
    {
        return $this->date('l', $until);
    }

    public function month($until = 'now'): string
    {
        return $this->date('m', $until);
    }

    public function monthName($until = 'now'): string
    {
        return $this->date('F', $until);
    }

    public function year($until = 'now'): string
    {
        return $this->date('Y', $until);
    }

    public function century(): string
    {
        return Helper::randomElement($this->centuries);
    }

    public function timezone(?string $countryCode = null): string
    {
        if ($countryCode) {
            $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $countryCode);
        } else {
            $timezones = \DateTimeZone::listIdentifiers();
        }

        return Helper::randomElement($timezones);
    }
}
