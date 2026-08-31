<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Clock;

/**
 * An immmutable DateTime with stricter error handling and return types than the native one.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class DatePoint extends \DateTimeImmutable
{
    /**
     * @throws \DateMalformedStringException When $datetime is invalid
     */
    public function __construct(string $datetime = 'now', ?\DateTimeZone $timezone = null, ?parent $reference = null)
    {
        $now = $reference ?? Clock::get()->now();

        if ('now' !== $datetime) {
            if (!$now instanceof static) {
                $now = static::createFromInterface($now);
            }

            if (\PHP_VERSION_ID < 80300) {
                try {
                    $builtInDate = new parent($datetime, $timezone ?? $now->getTimezone());
                    $timezone = $builtInDate->getTimezone();
                } catch (\Exception $e) {
                    throw new \DateMalformedStringException($e->getMessage(), $e->getCode(), $e);
                }
            } else {
                $builtInDate = new parent($datetime, $timezone ?? $now->getTimezone());
                $timezone = $builtInDate->getTimezone();
            }

            $now = $now->setTimezone($timezone)->modify($datetime);

            if ('00:00:00.000000' === $builtInDate->format('H:i:s.u')) {
                $now = $now->setTime(0, 0);
            }
        } elseif (null !== $timezone) {
            $now = $now->setTimezone($timezone);
        }

        $this->__unserialize((array) $now);
    }

    /**
     * @throws \DateMalformedStringException When $format or $datetime are invalid
     */
    public static function createFromFormat(string $format, string $datetime, ?\DateTimeZone $timezone = null): static
    {
        return parent::createFromFormat($format, $datetime, $timezone) ?: throw new \DateMalformedStringException(static::getLastErrors()['errors'][0] ?? 'Invalid date string or format.');
    }

    public static function createFromInterface(\DateTimeInterface $object): static
    {
        return parent::createFromInterface($object);
    }

    public static function createFromMutable(\DateTime $object): static
    {
        return parent::createFromMutable($object);
    }

    public static function createFromTimestamp(int|float $timestamp): static
    {
        if (\PHP_VERSION_ID >= 80400) {
            return parent::createFromTimestamp($timestamp);
        }

        if (\is_int($timestamp) || !$ms = (int) $timestamp - $timestamp) {
            return static::createFromFormat('U', (string) $timestamp);
        }

        if (!is_finite($timestamp) || \PHP_INT_MAX + 1.0 <= $timestamp || \PHP_INT_MIN > $timestamp) {
            throw new \DateRangeError(\sprintf('DateTimeImmutable::createFromTimestamp(): Argument #1 ($timestamp) must be a finite number between %s and %s.999999, %s given', \PHP_INT_MIN, \PHP_INT_MAX, $timestamp));
        }

        if ($timestamp < 0) {
            $timestamp = (int) $timestamp - 2.0 + $ms;
        }

        return static::createFromFormat('U.u', \sprintf('%.6F', $timestamp));
    }

    public function add(\DateInterval $interval): static
    {
        return parent::add($interval);
    }

    public function sub(\DateInterval $interval): static
    {
        return parent::sub($interval);
    }

    /**
     * @throws \DateMalformedStringException When $modifier is invalid
     */
    public function modify(string $modifier): static
    {
        if (\PHP_VERSION_ID < 80300) {
            return @parent::modify($modifier) ?: throw new \DateMalformedStringException(error_get_last()['message'] ?? \sprintf('Invalid modifier: "%s".', $modifier));
        }

        return parent::modify($modifier);
    }

    public function setTimestamp(int $value): static
    {
        return parent::setTimestamp($value);
    }

    public function setDate(int $year, int $month, int $day): static
    {
        return parent::setDate($year, $month, $day);
    }

    public function setISODate(int $year, int $week, int $day = 1): static
    {
        return parent::setISODate($year, $week, $day);
    }

    public function setTime(int $hour, int $minute, int $second = 0, int $microsecond = 0): static
    {
        return parent::setTime($hour, $minute, $second, $microsecond);
    }

    public function setTimezone(\DateTimeZone $timezone): static
    {
        return parent::setTimezone($timezone);
    }

    public function getTimezone(): \DateTimeZone
    {
        return parent::getTimezone() ?: throw new \DateInvalidTimeZoneException('The DatePoint object has no timezone.');
    }

    public function setMicrosecond(int $microsecond): static
    {
        if ($microsecond < 0 || $microsecond > 999999) {
            throw new \DateRangeError('DatePoint::setMicrosecond(): Argument #1 ($microsecond) must be between 0 and 999999, '.$microsecond.' given');
        }

        if (\PHP_VERSION_ID < 80400) {
            return $this->setTime(...explode('.', $this->format('H.i.s.'.$microsecond)));
        }

        return parent::setMicrosecond($microsecond);
    }

    public function getMicrosecond(): int
    {
        if (\PHP_VERSION_ID >= 80400) {
            return parent::getMicrosecond();
        }

        return $this->format('u');
    }
}
