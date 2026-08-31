<?php

namespace Faker\Extension;

/**
 * FakerPHP extension for Date-related randomization.
 *
 * Functions accepting a date string use the `strtotime()` function internally.
 *
 * @experimental
 *
 * @since 1.20.0
 */
interface DateTimeExtension
{
    /**
     * Get a DateTime object between January 1, 1970, and `$until` (defaults to "now").
     *
     * @param \DateTime|int|string $until    maximum timestamp, defaults to "now"
     * @param string|null          $timezone zone timezone for generated date, fallback to `DateTime::$defaultTimezone` and `date_default_timezone_get()`.
     *
     * @see \DateTimeZone
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     *
     * @example DateTime('2005-08-16 20:39:21')
     */
    public function dateTime($until = 'now', ?string $timezone = null): \DateTime;

    /**
     * Get a DateTime object for a date between January 1, 0001, and now.
     *
     * @param \DateTime|int|string $until    maximum timestamp, defaults to "now"
     * @param string|null          $timezone zone timezone for generated date, fallback to `DateTime::$defaultTimezone` and `date_default_timezone_get()`.
     *
     * @example DateTime('1265-03-22 21:15:52')
     *
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function dateTimeAD($until = 'now', ?string $timezone = null): \DateTime;

    /**
     * Get a DateTime object a random date between `$from` and `$until`.
     * Accepts date strings that can be recognized by `strtotime()`.
     *
     * @param \DateTime|string     $from     defaults to 30 years ago
     * @param \DateTime|int|string $until    maximum timestamp, defaults to "now"
     * @param string|null          $timezone zone timezone for generated date, fallback to `DateTime::$defaultTimezone` and `date_default_timezone_get()`.
     *
     * @see \DateTimeZone
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function dateTimeBetween($from = '-30 years', $until = 'now', ?string $timezone = null): \DateTime;

    /**
     * Get a DateTime object based on a random date between `$from` and an interval.
     * Accepts date string that can be recognized by `strtotime()`.
     *
     * @param \DateTime|int|string $from     defaults to 30 years ago
     * @param string               $interval defaults to 5 days after
     * @param string|null          $timezone zone timezone for generated date, fallback to `DateTime::$defaultTimezone` and `date_default_timezone_get()`.
     *
     * @see \DateTimeZone
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function dateTimeInInterval($from = '-30 years', string $interval = '+5 days', ?string $timezone = null): \DateTime;

    /**
     * Get a date time object somewhere inside the current week.
     *
     * @param \DateTime|int|string $until    maximum timestamp, defaults to "now"
     * @param string|null          $timezone zone timezone for generated date, fallback to `DateTime::$defaultTimezone` and `date_default_timezone_get()`.
     *
     * @see \DateTimeZone
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function dateTimeThisWeek($until = 'now', ?string $timezone = null): \DateTime;

    /**
     * Get a date time object somewhere inside the current month.
     *
     * @param \DateTime|int|string $until    maximum timestamp, defaults to "now"
     * @param string|null          $timezone timezone for generated date, fallback to `DateTime::$defaultTimezone` and `date_default_timezone_get()`.
     *
     * @see \DateTimeZone
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function dateTimeThisMonth($until = 'now', ?string $timezone = null): \DateTime;

    /**
     * Get a date time object somewhere inside the current year.
     *
     * @param \DateTime|int|string $until    maximum timestamp, defaults to "now"
     * @param string|null          $timezone timezone for generated date, fallback to `DateTime::$defaultTimezone` and `date_default_timezone_get()`.
     *
     * @see \DateTimeZone
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function dateTimeThisYear($until = 'now', ?string $timezone = null): \DateTime;

    /**
     * Get a date time object somewhere inside the current decade.
     *
     * @param \DateTime|int|string $until    maximum timestamp, defaults to "now"
     * @param string|null          $timezone timezone for generated date, fallback to `DateTime::$defaultTimezone` and `date_default_timezone_get()`.
     *
     * @see \DateTimeZone
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function dateTimeThisDecade($until = 'now', ?string $timezone = null): \DateTime;

    /**
     * Get a date time object somewhere inside the current century.
     *
     * @param \DateTime|int|string $until    maximum timestamp, defaults to "now"
     * @param string|null          $timezone timezone for generated date, fallback to `DateTime::$defaultTimezone` and `date_default_timezone_get()`.
     *
     * @see \DateTimeZone
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function dateTimeThisCentury($until = 'now', ?string $timezone = null): \DateTime;

    /**
     * Get a date string between January 1, 1970, and `$until`.
     *
     * @param string               $format DateTime format
     * @param \DateTime|int|string $until  maximum timestamp, defaults to "now"
     *
     * @see https://www.php.net/manual/en/datetime.format.php
     */
    public function date(string $format = 'Y-m-d', $until = 'now'): string;

    /**
     * Get a time string (24h format by default).
     *
     * @param string               $format DateTime format
     * @param \DateTime|int|string $until  maximum timestamp, defaults to "now"
     *
     * @see https://www.php.net/manual/en/datetime.format.php
     */
    public function time(string $format = 'H:i:s', $until = 'now'): string;

    /**
     * Get a UNIX (POSIX-compatible) timestamp between January 1, 1970, and `$until`.
     *
     * @param \DateTime|int|string $until maximum timestamp, defaults to "now"
     */
    public function unixTime($until = 'now'): int;

    /**
     * Get a date string according to the ISO-8601 standard.
     *
     * @param \DateTime|int|string $until maximum timestamp, defaults to "now"
     */
    public function iso8601($until = 'now'): string;

    /**
     * Get a string containing either "am" or "pm".
     *
     * @param \DateTime|int|string $until maximum timestamp, defaults to "now"
     *
     * @example 'am'
     */
    public function amPm($until = 'now'): string;

    /**
     * Get a localized random day of the month.
     *
     * @param \DateTime|int|string $until maximum timestamp, defaults to "now"
     *
     * @example '16'
     */
    public function dayOfMonth($until = 'now'): string;

    /**
     * Get a localized random day of the week.
     *
     * Uses internal DateTime formatting, hence PHP's internal locale will be used (change using `setlocale()`).
     *
     * @param \DateTime|int|string $until maximum timestamp, defaults to "now"
     *
     * @example 'Tuesday'
     *
     * @see setlocale
     * @see https://www.php.net/manual/en/function.setlocale.php Set a different output language
     */
    public function dayOfWeek($until = 'now'): string;

    /**
     * Get a random month (numbered).
     *
     * @param \DateTime|int|string $until maximum timestamp, defaults to "now"
     *
     * @example '7'
     */
    public function month($until = 'now'): string;

    /**
     * Get a random month.
     *
     * @param \DateTime|int|string $until maximum timestamp, defaults to "now"
     *
     * @see setlocale
     * @see https://www.php.net/manual/en/function.setlocale.php Set a different output language
     *
     * @example 'September'
     */
    public function monthName($until = 'now'): string;

    /**
     * Get a random year between 1970 and `$until`.
     *
     * @param \DateTime|int|string $until maximum timestamp, defaults to "now"
     *
     * @example '1987'
     */
    public function year($until = 'now'): string;

    /**
     * Get a random century, formatted as Roman numerals.
     *
     * @example 'XVII'
     */
    public function century(): string;

    /**
     * Get a random timezone, uses `\DateTimeZone::listIdentifiers()` internally.
     *
     * @param string|null $countryCode two-letter ISO 3166-1 compatible country code
     *
     * @example 'Europe/Rome'
     */
    public function timezone(?string $countryCode = null): string;
}
