<?php

/*
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon;

use Closure;
use DateTime;
use DateTimeZone;
use DateInterval;
use DatePeriod;
use InvalidArgumentException;

/**
 * A simple API extension for DateTime
 *
 * @property      integer $year
 * @property      integer $yearIso
 * @property      integer $month
 * @property      integer $day
 * @property      integer $hour
 * @property      integer $minute
 * @property      integer $second
 * @property      integer $timestamp seconds since the Unix Epoch
 * @property-read integer $micro
 * @property-read integer $dayOfWeek 0 (for Sunday) through 6 (for Saturday)
 * @property-read integer $dayOfYear 0 through 365
 * @property-read integer $weekOfMonth 1 through 5
 * @property-read integer $weekOfYear ISO-8601 week number of year, weeks starting on Monday
 * @property-read integer $daysInMonth number of days in the given month
 * @property-read integer $age does a diffInYears() with default parameters
 * @property-read integer $quarter the quarter of this instance, 1 - 4
 * @property-read integer $offset the timezone offset in seconds from UTC
 * @property-read integer $offsetHours the timezone offset in hours from UTC
 * @property-read boolean $dst daylight savings time indicator, true if DST, false otherwise
 * @property-read boolean $local checks if the timezone is local, true if local, false otherwise
 * @property-read boolean $utc checks if the timezone is UTC, true if UTC, false otherwise
 * @property-read string  $timezoneName
 * @property-read string  $tzName
 *
 * @property-read  DateTimeZone        $timezone the current timezone
 * @property-read  DateTimeZone        $tz alias of timezone
 * @property-write DateTimeZone|string $timezone the current timezone
 * @property-write DateTimeZone|string $tz alias of timezone
 *
 */
class Carbon extends DateTime
{
    /**
     * The day constants
     */
    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    /**
     * Names of days of the week.
     *
     * @var array
     */
    protected static $days = array(
        self::SUNDAY => 'Sunday',
        self::MONDAY => 'Monday',
        self::TUESDAY => 'Tuesday',
        self::WEDNESDAY => 'Wednesday',
        self::THURSDAY => 'Thursday',
        self::FRIDAY => 'Friday',
        self::SATURDAY => 'Saturday'
    );

    /**
     * Terms used to detect if a time passed is a relative date for testing purposes
     *
     * @var array
     */
    protected static $relativeKeywords = array(
        'this',
        'next',
        'last',
        'tomorrow',
        'yesterday',
        '+',
        '-',
        'first',
        'last',
        'ago'
    );

    /**
     * Number of X in Y
     */
    const YEARS_PER_CENTURY = 100;
    const YEARS_PER_DECADE = 10;
    const MONTHS_PER_YEAR = 12;
    const WEEKS_PER_YEAR = 52;
    const DAYS_PER_WEEK = 7;
    const HOURS_PER_DAY = 24;
    const MINUTES_PER_HOUR = 60;
    const SECONDS_PER_MINUTE = 60;

    /**
     * Default format to use for __toString method when type juggling occurs.
     *
     * @var string
     */
    const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s';

    /**
     * Format to use for __toString method when type juggling occurs.
     *
     * @var string
     */
    protected static $toStringFormat = self::DEFAULT_TO_STRING_FORMAT;

    /**
     * A test Carbon instance to be returned when now instances are created
     *
     * @var Carbon
     */
    protected static $testNow;

    /**
     * Creates a DateTimeZone from a string or a DateTimeZone
     *
     * @param DateTimeZone|string|null $object
     *
     * @return DateTimeZone
     *
     * @throws InvalidArgumentException
     */
    protected static function safeCreateDateTimeZone($object)
    {
        if ($object === null) {
            // Don't return null... avoid Bug #52063 in PHP <5.3.6
            return new DateTimeZone(date_default_timezone_get());
        }

        if ($object instanceof DateTimeZone) {
            return $object;
        }

        $tz = @timezone_open((string) $object);

        if ($tz === false) {
            throw new InvalidArgumentException('Unknown or bad timezone ('.$object.')');
        }

        return $tz;
    }

    ///////////////////////////////////////////////////////////////////
    //////////////////////////// CONSTRUCTORS /////////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Create a new Carbon instance.
     *
     * Please see the testing aids section (specifically static::setTestNow())
     * for more on the possibility of this constructor returning a test instance.
     *
     * @param string              $time
     * @param DateTimeZone|string $tz
     */
    public function __construct($time = null, $tz = null)
    {
        // If the class has a test now set and we are trying to create a now()
        // instance then override as required
        if (static::hasTestNow() && (empty($time) || $time === 'now' || static::hasRelativeKeywords($time))) {
            $testInstance = clone static::getTestNow();
            if (static::hasRelativeKeywords($time)) {
                $testInstance->modify($time);
            }

            //shift the time according to the given time zone
            if ($tz !== NULL && $tz != static::getTestNow()->tz) {
                $testInstance->setTimezone($tz);
            } else {
                $tz = $testInstance->tz;
            }

            $time = $testInstance->toDateTimeString();
        }

        parent::__construct($time, static::safeCreateDateTimeZone($tz));
    }

    /**
     * Create a Carbon instance from a DateTime one
     *
     * @param DateTime $dt
     *
     * @return static
     */
    public static function instance(DateTime $dt)
    {
        return new static($dt->format('Y-m-d H:i:s.u'), $dt->getTimeZone());
    }

    /**
     * Create a carbon instance from a string.  This is an alias for the
     * constructor that allows better fluent syntax as it allows you to do
     * Carbon::parse('Monday next week')->fn() rather than
     * (new Carbon('Monday next week'))->fn()
     *
     * @param string              $time
     * @param DateTimeZone|string $tz
     *
     * @return static
     */
    public static function parse($time = null, $tz = null)
    {
        return new static($time, $tz);
    }

    /**
     * Get a Carbon instance for the current date and time
     *
     * @param DateTimeZone|string $tz
     *
     * @return static
     */
    public static function now($tz = null)
    {
        return new static(null, $tz);
    }

    /**
     * Create a Carbon instance for today
     *
     * @param DateTimeZone|string $tz
     *
     * @return static
     */
    public static function today($tz = null)
    {
        return static::now($tz)->startOfDay();
    }

    /**
     * Create a Carbon instance for tomorrow
     *
     * @param DateTimeZone|string $tz
     *
     * @return static
     */
    public static function tomorrow($tz = null)
    {
        return static::today($tz)->addDay();
    }

    /**
     * Create a Carbon instance for yesterday
     *
     * @param DateTimeZone|string $tz
     *
     * @return static
     */
    public static function yesterday($tz = null)
    {
        return static::today($tz)->subDay();
    }

    /**
     * Create a Carbon instance for the greatest supported date.
     *
     * @return Carbon
     */
    public static function maxValue()
    {
        return static::createFromTimestamp(PHP_INT_MAX);
    }

    /**
     * Create a Carbon instance for the lowest supported date.
     *
     * @return Carbon
     */
    public static function minValue()
    {
        return static::createFromTimestamp(~PHP_INT_MAX);
    }

    /**
     * Create a new Carbon instance from a specific date and time.
     *
     * If any of $year, $month or $day are set to null their now() values
     * will be used.
     *
     * If $hour is null it will be set to its now() value and the default values
     * for $minute and $second will be their now() values.
     * If $hour is not null then the default values for $minute and $second
     * will be 0.
     *
     * @param integer             $year
     * @param integer             $month
     * @param integer             $day
     * @param integer             $hour
     * @param integer             $minute
     * @param integer             $second
     * @param DateTimeZone|string $tz
     *
     * @return static
     */
    public static function create($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null, $tz = null)
    {
        $year = ($year === null) ? date('Y') : $year;
        $month = ($month === null) ? date('n') : $month;
        $day = ($day === null) ? date('j') : $day;

        if ($hour === null) {
            $hour = date('G');
            $minute = ($minute === null) ? date('i') : $minute;
            $second = ($second === null) ? date('s') : $second;
        } else {
            $minute = ($minute === null) ? 0 : $minute;
            $second = ($second === null) ? 0 : $second;
        }

        return static::createFromFormat('Y-n-j G:i:s', sprintf('%s-%s-%s %s:%02s:%02s', $year, $month, $day, $hour, $minute, $second), $tz);
    }

    /**
     * Create a Carbon instance from just a date. The time portion is set to now.
     *
     * @param integer             $year
     * @param integer             $month
     * @param integer             $day
     * @param DateTimeZone|string $tz
     *
     * @return static
     */
    public static function createFromDate($year = null, $month = null, $day = null, $tz = null)
    {
        return static::create($year, $month, $day, null, null, null, $tz);
    }

    /**
     * Create a Carbon instance from just a time. The date portion is set to today.
     *
     * @param integer             $hour
     * @param integer             $minute
     * @param integer             $second
     * @param DateTimeZone|string $tz
     *
     * @return static
     */
    public static function createFromTime($hour = null, $minute = null, $second = null, $tz = null)
    {
        return static::create(null, null, null, $hour, $minute, $second, $tz);
    }

    /**
     * Create a Carbon instance from a specific format
     *
     * @param string              $format
     * @param string              $time
     * @param DateTimeZone|string $tz
     *
     * @return static
     *
     * @throws InvalidArgumentException
     */
    public static function createFromFormat($format, $time, $tz = null)
    {
        if ($tz !== null) {
            $dt = parent::createFromFormat($format, $time, static::safeCreateDateTimeZone($tz));
        } else {
            $dt = parent::createFromFormat($format, $time);
        }

        if ($dt instanceof DateTime) {
            return static::instance($dt);
        }

        $errors = static::getLastErrors();
        throw new InvalidArgumentException(implode(PHP_EOL, $errors['errors']));
    }

    /**
     * Create a Carbon instance from a timestamp
     *
     * @param integer             $timestamp
     * @param DateTimeZone|string $tz
     *
     * @return static
     */
    public static function createFromTimestamp($timestamp, $tz = null)
    {
        return static::now($tz)->setTimestamp($timestamp);
    }

    /**
     * Create a Carbon instance from an UTC timestamp
     *
     * @param integer $timestamp
     *
     * @return static
     */
    public static function createFromTimestampUTC($timestamp)
    {
        return new static('@'.$timestamp);
    }

    /**
     * Get a copy of the instance
     *
     * @return static
     */
    public function copy()
    {
        return static::instance($this);
    }

    ///////////////////////////////////////////////////////////////////
    ///////////////////////// GETTERS AND SETTERS /////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Get a part of the Carbon object
     *
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return string|integer|DateTimeZone
     */
    public function __get($name)
    {
        switch (true) {
            case array_key_exists($name, $formats = array(
                'year' => 'Y',
                'yearIso' => 'o',
                'month' => 'n',
                'day' => 'j',
                'hour' => 'G',
                'minute' => 'i',
                'second' => 's',
                'micro' => 'u',
                'dayOfWeek' => 'w',
                'dayOfYear' => 'z',
                'weekOfYear' => 'W',
                'daysInMonth' => 't',
                'timestamp' => 'U',
            )):
                return (int) $this->format($formats[$name]);

            case $name === 'weekOfMonth':
                return (int) ceil($this->day / static::DAYS_PER_WEEK);

            case $name === 'age':
                return (int) $this->diffInYears();

            case $name === 'quarter':
                return (int) ceil($this->month / 3);

            case $name === 'offset':
                return $this->getOffset();

            case $name === 'offsetHours':
                return $this->getOffset() / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR;

            case $name === 'dst':
                return $this->format('I') == '1';

            case $name === 'local':
                return $this->offset == $this->copy()->setTimezone(date_default_timezone_get())->offset;

            case $name === 'utc':
                return $this->offset == 0;

            case $name === 'timezone' || $name === 'tz':
                return $this->getTimezone();

            case $name === 'timezoneName' || $name === 'tzName':
                return $this->getTimezone()->getName();

            default:
                throw new InvalidArgumentException(sprintf("Unknown getter '%s'", $name));
        }
    }

    /**
     * Check if an attribute exists on the object
     *
     * @param string $name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        try {
            $this->__get($name);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * Set a part of the Carbon object
     *
     * @param string                      $name
     * @param string|integer|DateTimeZone $value
     *
     * @throws InvalidArgumentException
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'year':
                $this->setDate($value, $this->month, $this->day);
                break;

            case 'month':
                $this->setDate($this->year, $value, $this->day);
                break;

            case 'day':
                $this->setDate($this->year, $this->month, $value);
                break;

            case 'hour':
                $this->setTime($value, $this->minute, $this->second);
                break;

            case 'minute':
                $this->setTime($this->hour, $value, $this->second);
                break;

            case 'second':
                $this->setTime($this->hour, $this->minute, $value);
                break;

            case 'timestamp':
                parent::setTimestamp($value);
                break;

            case 'timezone':
            case 'tz':
                $this->setTimezone($value);
                break;

            default:
                throw new InvalidArgumentException(sprintf("Unknown setter '%s'", $name));
        }
    }

    /**
     * Set the instance's year
     *
     * @param integer $value
     *
     * @return static
     */
    public function year($value)
    {
        $this->year = $value;

        return $this;
    }

    /**
     * Set the instance's month
     *
     * @param integer $value
     *
     * @return static
     */
    public function month($value)
    {
        $this->month = $value;

        return $this;
    }

    /**
     * Set the instance's day
     *
     * @param integer $value
     *
     * @return static
     */
    public function day($value)
    {
        $this->day = $value;

        return $this;
    }

    /**
     * Set the instance's hour
     *
     * @param integer $value
     *
     * @return static
     */
    public function hour($value)
    {
        $this->hour = $value;

        return $this;
    }

    /**
     * Set the instance's minute
     *
     * @param integer $value
     *
     * @return static
     */
    public function minute($value)
    {
        $this->minute = $value;

        return $this;
    }

    /**
     * Set the instance's second
     *
     * @param integer $value
     *
     * @return static
     */
    public function second($value)
    {
        $this->second = $value;

        return $this;
    }

    /**
     * Set the date and time all together
     *
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @param integer $hour
     * @param integer $minute
     * @param integer $second
     *
     * @return static
     */
    public function setDateTime($year, $month, $day, $hour, $minute, $second = 0)
    {
        return $this->setDate($year, $month, $day)->setTime($hour, $minute, $second);
    }

    /**
     * Set the instance's timestamp
     *
     * @param integer $value
     *
     * @return static
     */
    public function timestamp($value)
    {
        $this->timestamp = $value;

        return $this;
    }

    /**
     * Alias for setTimezone()
     *
     * @param DateTimeZone|string $value
     *
     * @return static
     */
    public function timezone($value)
    {
        return $this->setTimezone($value);
    }

    /**
     * Alias for setTimezone()
     *
     * @param DateTimeZone|string $value
     *
     * @return static
     */
    public function tz($value)
    {
        return $this->setTimezone($value);
    }

    /**
     * Set the instance's timezone from a string or object
     *
     * @param DateTimeZone|string $value
     *
     * @return static
     */
    public function setTimezone($value)
    {
        parent::setTimezone(static::safeCreateDateTimeZone($value));

        return $this;
    }

    ///////////////////////////////////////////////////////////////////
    ///////////////////////// TESTING AIDS ////////////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Set a Carbon instance (real or mock) to be returned when a "now"
     * instance is created.  The provided instance will be returned
     * specifically under the following conditions:
     *   - A call to the static now() method, ex. Carbon::now()
     *   - When a null (or blank string) is passed to the constructor or parse(), ex. new Carbon(null)
     *   - When the string "now" is passed to the constructor or parse(), ex. new Carbon('now')
     *
     * Note the timezone parameter was left out of the examples above and
     * has no affect as the mock value will be returned regardless of its value.
     *
     * To clear the test instance call this method using the default
     * parameter of null.
     *
     * @param Carbon $testNow
     */
    public static function setTestNow(Carbon $testNow = null)
    {
        static::$testNow = $testNow;
    }

    /**
     * Get the Carbon instance (real or mock) to be returned when a "now"
     * instance is created.
     *
     * @return static the current instance used for testing
     */
    public static function getTestNow()
    {
        return static::$testNow;
    }

    /**
     * Determine if there is a valid test instance set. A valid test instance
     * is anything that is not null.
     *
     * @return boolean true if there is a test instance, otherwise false
     */
    public static function hasTestNow()
    {
        return static::getTestNow() !== null;
    }

    /**
     * Determine if there is a relative keyword in the time string, this is to
     * create dates relative to now for test instances. e.g.: next tuesday
     *
     * @param string $time
     *
     * @return boolean true if there is a keyword, otherwise false
     */
    public static function hasRelativeKeywords($time)
    {
        // skip common format with a '-' in it
        if (preg_match('/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}/', $time) !== 1) {
            foreach (static::$relativeKeywords as $keyword) {
                if (stripos($time, $keyword) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    ///////////////////////////////////////////////////////////////////
    /////////////////////// STRING FORMATTING /////////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Format the instance with the current locale.  You can set the current
     * locale using setlocale() http://php.net/setlocale.
     *
     * @param string $format
     *
     * @return string
     */
    public function formatLocalized($format)
    {
        // Check for Windows to find and replace the %e
        // modifier correctly
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
        }

        return strftime($format, strtotime($this));
    }

    /**
     * Reset the format used to the default when type juggling a Carbon instance to a string
     *
     */
    public static function resetToStringFormat()
    {
        static::setToStringFormat(static::DEFAULT_TO_STRING_FORMAT);
    }

    /**
     * Set the default format used when type juggling a Carbon instance to a string
     *
     * @param string $format
     */
    public static function setToStringFormat($format)
    {
        static::$toStringFormat = $format;
    }

    /**
     * Format the instance as a string using the set format
     *
     * @return string
     */
    public function __toString()
    {
        return $this->format(static::$toStringFormat);
    }

    /**
     * Format the instance as date
     *
     * @return string
     */
    public function toDateString()
    {
        return $this->format('Y-m-d');
    }

    /**
     * Format the instance as a readable date
     *
     * @return string
     */
    public function toFormattedDateString()
    {
        return $this->format('M j, Y');
    }

    /**
     * Format the instance as time
     *
     * @return string
     */
    public function toTimeString()
    {
        return $this->format('H:i:s');
    }

    /**
     * Format the instance as date and time
     *
     * @return string
     */
    public function toDateTimeString()
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * Format the instance with day, date and time
     *
     * @return string
     */
    public function toDayDateTimeString()
    {
        return $this->format('D, M j, Y g:i A');
    }

    /**
     * Format the instance as ATOM
     *
     * @return string
     */
    public function toAtomString()
    {
        return $this->format(static::ATOM);
    }

    /**
     * Format the instance as COOKIE
     *
     * @return string
     */
    public function toCookieString()
    {
        return $this->format(static::COOKIE);
    }

    /**
     * Format the instance as ISO8601
     *
     * @return string
     */
    public function toIso8601String()
    {
        return $this->format(static::ISO8601);
    }

    /**
     * Format the instance as RFC822
     *
     * @return string
     */
    public function toRfc822String()
    {
        return $this->format(static::RFC822);
    }

    /**
     * Format the instance as RFC850
     *
     * @return string
     */
    public function toRfc850String()
    {
        return $this->format(static::RFC850);
    }

    /**
     * Format the instance as RFC1036
     *
     * @return string
     */
    public function toRfc1036String()
    {
        return $this->format(static::RFC1036);
    }

    /**
     * Format the instance as RFC1123
     *
     * @return string
     */
    public function toRfc1123String()
    {
        return $this->format(static::RFC1123);
    }

    /**
     * Format the instance as RFC2822
     *
     * @return string
     */
    public function toRfc2822String()
    {
        return $this->format(static::RFC2822);
    }

    /**
     * Format the instance as RFC3339
     *
     * @return string
     */
    public function toRfc3339String()
    {
        return $this->format(static::RFC3339);
    }

    /**
     * Format the instance as RSS
     *
     * @return string
     */
    public function toRssString()
    {
        return $this->format(static::RSS);
    }

    /**
     * Format the instance as W3C
     *
     * @return string
     */
    public function toW3cString()
    {
        return $this->format(static::W3C);
    }

    ///////////////////////////////////////////////////////////////////
    ////////////////////////// COMPARISONS ////////////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Determines if the instance is equal to another
     *
     * @param Carbon $dt
     *
     * @return boolean
     */
    public function eq(Carbon $dt)
    {
        return $this == $dt;
    }

    /**
     * Determines if the instance is not equal to another
     *
     * @param Carbon $dt
     *
     * @return boolean
     */
    public function ne(Carbon $dt)
    {
        return !$this->eq($dt);
    }

    /**
     * Determines if the instance is greater (after) than another
     *
     * @param Carbon $dt
     *
     * @return boolean
     */
    public function gt(Carbon $dt)
    {
        return $this > $dt;
    }

    /**
     * Determines if the instance is greater (after) than or equal to another
     *
     * @param Carbon $dt
     *
     * @return boolean
     */
    public function gte(Carbon $dt)
    {
        return $this >= $dt;
    }

    /**
     * Determines if the instance is less (before) than another
     *
     * @param Carbon $dt
     *
     * @return boolean
     */
    public function lt(Carbon $dt)
    {
        return $this < $dt;
    }

    /**
     * Determines if the instance is less (before) or equal to another
     *
     * @param Carbon $dt
     *
     * @return boolean
     */
    public function lte(Carbon $dt)
    {
        return $this <= $dt;
    }

  /**
   * Determines if the instance is between two others
   *
   * @param  Carbon  $dt1
   * @param  Carbon  $dt2
   * @param  boolean $equal  Indicates if a > and < comparison should be used or <= or >=
   *
   * @return boolean
   */
    public function between(Carbon $dt1, Carbon $dt2, $equal = true)
    {
        if ($dt1->gt($dt2)) {
            $temp = $dt1;
            $dt1 = $dt2;
            $dt2 = $temp;
        }

        if ($equal) {
            return $this->gte($dt1) && $this->lte($dt2);
        } else {
            return $this->gt($dt1) && $this->lt($dt2);
        }
    }

    /**
     * Get the minimum instance between a given instance (default now) and the current instance.
     *
     * @param Carbon $dt
     *
     * @return static
     */
    public function min(Carbon $dt = null)
    {
        $dt = ($dt === null) ? static::now($this->tz) : $dt;

        return $this->lt($dt) ? $this : $dt;
    }

    /**
     * Get the maximum instance between a given instance (default now) and the current instance.
     *
     * @param Carbon $dt
     *
     * @return static
     */
    public function max(Carbon $dt = null)
    {
        $dt = ($dt === null) ? static::now($this->tz) : $dt;

        return $this->gt($dt) ? $this : $dt;
    }

    /**
     * Determines if the instance is a weekday
     *
     * @return boolean
     */
    public function isWeekday()
    {
        return ($this->dayOfWeek != static::SUNDAY && $this->dayOfWeek != static::SATURDAY);
    }

    /**
     * Determines if the instance is a weekend day
     *
     * @return boolean
     */
    public function isWeekend()
    {
        return !$this->isWeekDay();
    }

    /**
     * Determines if the instance is yesterday
     *
     * @return boolean
     */
    public function isYesterday()
    {
        return $this->toDateString() === static::yesterday($this->tz)->toDateString();
    }

    /**
     * Determines if the instance is today
     *
     * @return boolean
     */
    public function isToday()
    {
        return $this->toDateString() === static::now($this->tz)->toDateString();
    }

    /**
     * Determines if the instance is tomorrow
     *
     * @return boolean
     */
    public function isTomorrow()
    {
        return $this->toDateString() === static::tomorrow($this->tz)->toDateString();
    }

    /**
     * Determines if the instance is in the future, ie. greater (after) than now
     *
     * @return boolean
     */
    public function isFuture()
    {
        return $this->gt(static::now($this->tz));
    }

    /**
     * Determines if the instance is in the past, ie. less (before) than now
     *
     * @return boolean
     */
    public function isPast()
    {
        return $this->lt(static::now($this->tz));
    }

    /**
     * Determines if the instance is a leap year
     *
     * @return boolean
     */
    public function isLeapYear()
    {
        return $this->format('L') == '1';
    }

    /**
     * Checks if the passed in date is the same day as the instance current day.
     *
     * @param  Carbon  $dt
     * @return boolean
     */
    public function isSameDay(Carbon $dt)
    {
        return $this->toDateString() === $dt->toDateString();
    }

    ///////////////////////////////////////////////////////////////////
    /////////////////// ADDITIONS AND SUBSTRACTIONS ///////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Add years to the instance. Positive $value travel forward while
     * negative $value travel into the past.
     *
     * @param integer $value
     *
     * @return static
     */
    public function addYears($value)
    {
        return $this->modify((int) $value . ' year');
    }

    /**
     * Add a year to the instance
     *
     * @return static
     */
    public function addYear()
    {
        return $this->addYears(1);
    }

    /**
     * Remove a year from the instance
     *
     * @return static
     */
    public function subYear()
    {
        return $this->addYears(-1);
    }

    /**
     * Remove years from the instance.
     *
     * @param integer $value
     *
     * @return static
     */
    public function subYears($value)
    {
        return $this->addYears(-1 * $value);
    }

    /**
     * Add months to the instance. Positive $value travels forward while
     * negative $value travels into the past.
     *
     * @param integer $value
     *
     * @return static
     */
    public function addMonths($value)
    {
        return $this->modify((int) $value . ' month');
    }

    /**
     * Add a month to the instance
     *
     * @return static
     */
    public function addMonth()
    {
        return $this->addMonths(1);
    }

    /**
     * Remove a month from the instance
     *
     * @return static
     */
    public function subMonth()
    {
        return $this->addMonths(-1);
    }

    /**
     * Remove months from the instance
     *
     * @param integer $value
     *
     * @return static
     */
    public function subMonths($value)
    {
        return $this->addMonths(-1 * $value);
    }

    /**
     * Add months without overflowing to the instance. Positive $value
     * travels forward while negative $value travels into the past.
     *
     * @param integer $value
     *
     * @return static
     */
    public function addMonthsNoOverflow($value)
    {
        $date = $this->copy()->addMonths($value);

        if ($date->day != $this->day) {
            $date->day(1)->subMonth()->day($date->daysInMonth);
        }

        return $date;
    }

    /**
     * Add a month with no overflow to the instance
     *
     * @return static
     */
    public function addMonthNoOverflow()
    {
        return $this->addMonthsNoOverflow(1);
    }

    /**
     * Remove a month with no overflow from the instance
     *
     * @return static
     */
    public function subMonthNoOverflow()
    {
        return $this->addMonthsNoOverflow(-1);
    }

    /**
     * Remove months with no overflow from the instance
     *
     * @param integer $value
     *
     * @return static
     */
    public function subMonthsNoOverflow($value)
    {
        return $this->addMonthsNoOverflow(-1 * $value);
    }

    /**
     * Add days to the instance. Positive $value travels forward while
     * negative $value travels into the past.
     *
     * @param integer $value
     *
     * @return static
     */
    public function addDays($value)
    {
        return $this->modify((int) $value . ' day');
    }

    /**
     * Add a day to the instance
     *
     * @return static
     */
    public function addDay()
    {
        return $this->addDays(1);
    }

    /**
     * Remove a day from the instance
     *
     * @return static
     */
    public function subDay()
    {
        return $this->addDays(-1);
    }

    /**
     * Remove days from the instance
     *
     * @param integer $value
     *
     * @return static
     */
    public function subDays($value)
    {
        return $this->addDays(-1 * $value);
    }

    /**
     * Add weekdays to the instance. Positive $value travels forward while
     * negative $value travels into the past.
     *
     * @param integer $value
     *
     * @return static
     */
    public function addWeekdays($value)
    {
        return $this->modify((int) $value . ' weekday');
    }

    /**
     * Add a weekday to the instance
     *
     * @return static
     */
    public function addWeekday()
    {
        return $this->addWeekdays(1);
    }

    /**
     * Remove a weekday from the instance
     *
     * @return static
     */
    public function subWeekday()
    {
        return $this->addWeekdays(-1);
    }

    /**
     * Remove weekdays from the instance
     *
     * @param integer $value
     *
     * @return static
     */
    public function subWeekdays($value)
    {
        return $this->addWeekdays(-1 * $value);
    }

    /**
     * Add weeks to the instance. Positive $value travels forward while
     * negative $value travels into the past.
     *
     * @param integer $value
     *
     * @return static
     */
    public function addWeeks($value)
    {
        return $this->modify((int) $value . ' week');
    }

    /**
     * Add a week to the instance
     *
     * @return static
     */
    public function addWeek()
    {
        return $this->addWeeks(1);
    }

    /**
     * Remove a week from the instance
     *
     * @return static
     */
    public function subWeek()
    {
        return $this->addWeeks(-1);
    }

    /**
     * Remove weeks to the instance
     *
     * @param integer $value
     *
     * @return static
     */
    public function subWeeks($value)
    {
        return $this->addWeeks(-1 * $value);
    }

    /**
     * Add hours to the instance. Positive $value travels forward while
     * negative $value travels into the past.
     *
     * @param integer $value
     *
     * @return static
     */
    public function addHours($value)
    {
        return $this->modify((int) $value . ' hour');
    }

    /**
     * Add an hour to the instance
     *
     * @return static
     */
    public function addHour()
    {
        return $this->addHours(1);
    }

    /**
     * Remove an hour from the instance
     *
     * @return static
     */
    public function subHour()
    {
        return $this->addHours(-1);
    }

    /**
     * Remove hours from the instance
     *
     * @param integer $value
     *
     * @return static
     */
    public function subHours($value)
    {
        return $this->addHours(-1 * $value);
    }

    /**
     * Add minutes to the instance. Positive $value travels forward while
     * negative $value travels into the past.
     *
     * @param integer $value
     *
     * @return static
     */
    public function addMinutes($value)
    {
        return $this->modify((int) $value . ' minute');
    }

    /**
     * Add a minute to the instance
     *
     * @return static
     */
    public function addMinute()
    {
        return $this->addMinutes(1);
    }

    /**
     * Remove a minute from the instance
     *
     * @return static
     */
    public function subMinute()
    {
        return $this->addMinutes(-1);
    }

    /**
     * Remove minutes from the instance
     *
     * @param integer $value
     *
     * @return static
     */
    public function subMinutes($value)
    {
        return $this->addMinutes(-1 * $value);
    }

    /**
     * Add seconds to the instance. Positive $value travels forward while
     * negative $value travels into the past.
     *
     * @param integer $value
     *
     * @return static
     */
    public function addSeconds($value)
    {
        return $this->modify((int) $value . ' second');
    }

    /**
     * Add a second to the instance
     *
     * @return static
     */
    public function addSecond()
    {
        return $this->addSeconds(1);
    }

    /**
     * Remove a second from the instance
     *
     * @return static
     */
    public function subSecond()
    {
        return $this->addSeconds(-1);
    }

    /**
     * Remove seconds from the instance
     *
     * @param integer $value
     *
     * @return static
     */
    public function subSeconds($value)
    {
        return $this->addSeconds(-1 * $value);
    }

    ///////////////////////////////////////////////////////////////////
    /////////////////////////// DIFFERENCES ///////////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Get the difference in years
     *
     * @param Carbon  $dt
     * @param boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function diffInYears(Carbon $dt = null, $abs = true)
    {
        $dt = ($dt === null) ? static::now($this->tz) : $dt;

        return (int) $this->diff($dt, $abs)->format('%r%y');
    }

    /**
     * Get the difference in months
     *
     * @param Carbon  $dt
     * @param boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function diffInMonths(Carbon $dt = null, $abs = true)
    {
        $dt = ($dt === null) ? static::now($this->tz) : $dt;

        return $this->diffInYears($dt, $abs) * static::MONTHS_PER_YEAR + $this->diff($dt, $abs)->format('%r%m');
    }

    /**
     * Get the difference in weeks
     *
     * @param Carbon  $dt
     * @param boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function diffInWeeks(Carbon $dt = null, $abs = true)
    {
        return (int) ($this->diffInDays($dt, $abs) / static::DAYS_PER_WEEK);
    }

    /**
     * Get the difference in days
     *
     * @param Carbon  $dt
     * @param boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function diffInDays(Carbon $dt = null, $abs = true)
    {
        $dt = ($dt === null) ? static::now($this->tz) : $dt;

        return (int) $this->diff($dt, $abs)->format('%r%a');
    }

     /**
      * Get the difference in days using a filter closure
      *
      * @param Closure $callback
      * @param Carbon  $dt
      * @param boolean $abs      Get the absolute of the difference
      *
      * @return int
      */
     public function diffInDaysFiltered(Closure $callback, Carbon $dt = null, $abs = true)
     {
         $start = $this;
         $end = ($dt === null) ? static::now($this->tz) : $dt;
         $inverse = false;

         if ($end < $start) {
             $start = $end;
             $end = $this;
             $inverse = true;
         }

         $period = new DatePeriod($start, new DateInterval('P1D'), $end);
         $days = array_filter(iterator_to_array($period), function (DateTime $date) use ($callback) {
                return call_user_func($callback, Carbon::instance($date));
          });

         $diff = count($days);

         return $inverse && !$abs ? -$diff : $diff;
     }

     /**
      * Get the difference in weekdays
      *
      * @param Carbon  $dt
      * @param boolean $abs Get the absolute of the difference
      *
      * @return int
      */
     public function diffInWeekdays(Carbon $dt = null, $abs = true)
     {
         return $this->diffInDaysFiltered(function (Carbon $date) {
                return $date->isWeekday();
          }, $dt, $abs);
     }

     /**
      * Get the difference in weekend days using a filter
      *
      * @param Carbon  $dt
      * @param boolean $abs Get the absolute of the difference
      *
      * @return int
      */
     public function diffInWeekendDays(Carbon $dt = null, $abs = true)
     {
         return $this->diffInDaysFiltered(function (Carbon $date) {
                return $date->isWeekend();
          }, $dt, $abs);
     }

    /**
     * Get the difference in hours
     *
     * @param Carbon  $dt
     * @param boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function diffInHours(Carbon $dt = null, $abs = true)
    {
        return (int) ($this->diffInSeconds($dt, $abs) / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR);
    }

    /**
     * Get the difference in minutes
     *
     * @param Carbon  $dt
     * @param boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function diffInMinutes(Carbon $dt = null, $abs = true)
    {
        return (int) ($this->diffInSeconds($dt, $abs) / static::SECONDS_PER_MINUTE);
    }

    /**
     * Get the difference in seconds
     *
     * @param Carbon  $dt
     * @param boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function diffInSeconds(Carbon $dt = null, $abs = true)
    {
        $dt = ($dt === null) ? static::now($this->tz) : $dt;
        $value = $dt->getTimestamp() - $this->getTimestamp();

        return $abs ? abs($value) : $value;
    }

    /**
     * The number of seconds since midnight.
     *
     * @return integer
     */
    public function secondsSinceMidnight()
    {
        return $this->diffInSeconds($this->copy()->startOfDay());
    }

    /**
     * The number of seconds until 23:23:59.
     *
     * @return integer
     */
    public function secondsUntilEndOfDay()
    {
        return $this->diffInSeconds($this->copy()->endOfDay());
    }

    /**
     * Get the difference in a human readable format.
     *
     * When comparing a value in the past to default now:
     * 1 hour ago
     * 5 months ago
     *
     * When comparing a value in the future to default now:
     * 1 hour from now
     * 5 months from now
     *
     * When comparing a value in the past to another value:
     * 1 hour before
     * 5 months before
     *
     * When comparing a value in the future to another value:
     * 1 hour after
     * 5 months after
     *
     * @param Carbon $other
     * @param bool   $absolute removes time difference modifiers ago, after, etc
     *
     * @return string
     */
    public function diffForHumans(Carbon $other = null, $absolute = false)
    {
        $isNow = $other === null;

        if ($isNow) {
            $other = static::now($this->tz);
        }

        $diffInterval = $this->diff($other);

        switch (true) {
            case ($diffInterval->y > 0):
                $unit = 'year';
                $delta = $diffInterval->y;
                break;

            case ($diffInterval->m > 0):
                $unit = 'month';
                $delta = $diffInterval->m;
                break;

            case ($diffInterval->d > 0):
                $unit = 'day';
                $delta = $diffInterval->d;
                if ($delta >= self::DAYS_PER_WEEK) {
                    $unit = 'week';
                    $delta = floor($delta / self::DAYS_PER_WEEK);
                }
                break;

            case ($diffInterval->h > 0):
                $unit = 'hour';
                $delta = $diffInterval->h;
                break;

            case ($diffInterval->i > 0):
                $unit = 'minute';
                $delta = $diffInterval->i;
                break;

            default:
                $delta = $diffInterval->s;
                $unit = 'second';
                break;
        }

        if ($delta == 0) {
            $delta = 1;
        }

        $txt = $delta . ' ' . $unit;
        $txt .= $delta == 1 ? '' : 's';

        if ($absolute) {
            return $txt;
        }

        $isFuture = $diffInterval->invert === 1;

        if ($isNow) {
            if ($isFuture) {
                return $txt . ' from now';
            }

            return $txt . ' ago';
        }

        if ($isFuture) {
            return $txt . ' after';
        }

        return $txt . ' before';
    }

    ///////////////////////////////////////////////////////////////////
    //////////////////////////// MODIFIERS ////////////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Resets the time to 00:00:00
     *
     * @return static
     */
    public function startOfDay()
    {
        return $this->hour(0)->minute(0)->second(0);
    }

    /**
     * Resets the time to 23:59:59
     *
     * @return static
     */
    public function endOfDay()
    {
        return $this->hour(23)->minute(59)->second(59);
    }

    /**
     * Resets the date to the first day of the month and the time to 00:00:00
     *
     * @return static
     */
    public function startOfMonth()
    {
        return $this->startOfDay()->day(1);
    }

    /**
     * Resets the date to end of the month and time to 23:59:59
     *
     * @return static
     */
    public function endOfMonth()
    {
        return $this->day($this->daysInMonth)->endOfDay();
    }

     /**
      * Resets the date to the first day of the year and the time to 00:00:00
      *
      * @return static
      */
    public function startOfYear()
    {
        return $this->month(1)->startOfMonth();
    }

     /**
      * Resets the date to end of the year and time to 23:59:59
      *
      * @return static
      */
     public function endOfYear()
     {
         return $this->month(static::MONTHS_PER_YEAR)->endOfMonth();
     }

     /**
      * Resets the date to the first day of the decade and the time to 00:00:00
      *
      * @return static
      */
     public function startOfDecade()
     {
         return $this->startOfYear()->year($this->year - $this->year % static::YEARS_PER_DECADE);
     }

     /**
      * Resets the date to end of the decade and time to 23:59:59
      *
      * @return static
      */
     public function endOfDecade()
     {
         return $this->endOfYear()->year($this->year - $this->year % static::YEARS_PER_DECADE + static::YEARS_PER_DECADE - 1);
     }

     /**
      * Resets the date to the first day of the century and the time to 00:00:00
      *
      * @return static
      */
     public function startOfCentury()
     {
         return $this->startOfYear()->year($this->year - $this->year % static::YEARS_PER_CENTURY);
     }

     /**
      * Resets the date to end of the century and time to 23:59:59
      *
      * @return static
      */
     public function endOfCentury()
     {
         return $this->endOfYear()->year($this->year - $this->year % static::YEARS_PER_CENTURY + static::YEARS_PER_CENTURY - 1);
     }

    /**
     * Resets the date to the first day of the ISO-8601 week (Monday) and the time to 00:00:00
     *
     * @return static
     */
     public function startOfWeek()
     {
         if ($this->dayOfWeek != static::MONDAY) {
             $this->previous(static::MONDAY);
         }

         return $this->startOfDay();
     }

     /**
      * Resets the date to end of the ISO-8601 week (Sunday) and time to 23:59:59
      *
      * @return static
      */
     public function endOfWeek()
     {
         if ($this->dayOfWeek != static::SUNDAY) {
             $this->next(static::SUNDAY);
         }

         return $this->endOfDay();
     }

    /**
     * Modify to the next occurance of a given day of the week.
     * If no dayOfWeek is provided, modify to the next occurance
     * of the current day of the week.  Use the supplied consts
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function next($dayOfWeek = null)
    {
        if ($dayOfWeek === null) {
            $dayOfWeek = $this->dayOfWeek;
        }

        return $this->startOfDay()->modify('next ' . static::$days[$dayOfWeek]);
    }

    /**
     * Modify to the previous occurance of a given day of the week.
     * If no dayOfWeek is provided, modify to the previous occurance
     * of the current day of the week.  Use the supplied consts
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function previous($dayOfWeek = null)
    {
        if ($dayOfWeek === null) {
            $dayOfWeek = $this->dayOfWeek;
        }

        return $this->startOfDay()->modify('last ' . static::$days[$dayOfWeek]);
    }

    /**
     * Modify to the first occurance of a given day of the week
     * in the current month. If no dayOfWeek is provided, modify to the
     * first day of the current month.  Use the supplied consts
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function firstOfMonth($dayOfWeek = null)
    {
        $this->startOfDay();

        if ($dayOfWeek === null) {
            return $this->day(1);
        }

        return $this->modify('first ' . static::$days[$dayOfWeek] . ' of ' . $this->format('F') . ' ' . $this->year);
    }

    /**
     * Modify to the last occurance of a given day of the week
     * in the current month. If no dayOfWeek is provided, modify to the
     * last day of the current month.  Use the supplied consts
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function lastOfMonth($dayOfWeek = null)
    {
        $this->startOfDay();

        if ($dayOfWeek === null) {
            return $this->day($this->daysInMonth);
        }

        return $this->modify('last ' . static::$days[$dayOfWeek] . ' of ' . $this->format('F') . ' ' . $this->year);
    }

    /**
     * Modify to the given occurance of a given day of the week
     * in the current month. If the calculated occurance is outside the scope
     * of the current month, then return false and no modifications are made.
     * Use the supplied consts to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $nth
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function nthOfMonth($nth, $dayOfWeek)
    {
        $dt = $this->copy()->firstOfMonth();
        $check = $dt->format('Y-m');
        $dt->modify('+' . $nth . ' ' . static::$days[$dayOfWeek]);

        return ($dt->format('Y-m') === $check) ? $this->modify($dt) : false;
    }

    /**
     * Modify to the first occurance of a given day of the week
     * in the current quarter. If no dayOfWeek is provided, modify to the
     * first day of the current quarter.  Use the supplied consts
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function firstOfQuarter($dayOfWeek = null)
    {
        return $this->day(1)->month($this->quarter * 3 - 2)->firstOfMonth($dayOfWeek);
    }

    /**
     * Modify to the last occurance of a given day of the week
     * in the current quarter. If no dayOfWeek is provided, modify to the
     * last day of the current quarter.  Use the supplied consts
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function lastOfQuarter($dayOfWeek = null)
    {
        return $this->day(1)->month($this->quarter * 3)->lastOfMonth($dayOfWeek);
    }

    /**
     * Modify to the given occurance of a given day of the week
     * in the current quarter. If the calculated occurance is outside the scope
     * of the current quarter, then return false and no modifications are made.
     * Use the supplied consts to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $nth
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function nthOfQuarter($nth, $dayOfWeek)
    {
        $dt = $this->copy()->day(1)->month($this->quarter * 3);
        $last_month = $dt->month;
        $year = $dt->year;
        $dt->firstOfQuarter()->modify('+' . $nth . ' ' . static::$days[$dayOfWeek]);

        return ($last_month < $dt->month || $year !== $dt->year) ? false : $this->modify($dt);
    }

    /**
     * Modify to the first occurance of a given day of the week
     * in the current year. If no dayOfWeek is provided, modify to the
     * first day of the current year.  Use the supplied consts
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function firstOfYear($dayOfWeek = null)
    {
        return $this->month(1)->firstOfMonth($dayOfWeek);
    }

    /**
     * Modify to the last occurance of a given day of the week
     * in the current year. If no dayOfWeek is provided, modify to the
     * last day of the current year.  Use the supplied consts
     * to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function lastOfYear($dayOfWeek = null)
    {
        return $this->month(static::MONTHS_PER_YEAR)->lastOfMonth($dayOfWeek);
    }

    /**
     * Modify to the given occurance of a given day of the week
     * in the current year. If the calculated occurance is outside the scope
     * of the current year, then return false and no modifications are made.
     * Use the supplied consts to indicate the desired dayOfWeek, ex. static::MONDAY.
     *
     * @param int $nth
     * @param int $dayOfWeek
     *
     * @return mixed
     */
    public function nthOfYear($nth, $dayOfWeek)
    {
        $dt = $this->copy()->firstOfYear()->modify('+' . $nth . ' ' . static::$days[$dayOfWeek]);

        return $this->year == $dt->year ? $this->modify($dt) : false;
    }

    /**
     * Modify the current instance to the average of a given instance (default now) and the current instance.
     *
     * @param Carbon $dt
     *
     * @return static
     */
    public function average(Carbon $dt = null)
    {
        $dt = ($dt === null) ? static::now($this->tz) : $dt;

        return $this->addSeconds((int) ($this->diffInSeconds($dt, false) / 2));
    }

    /**
     * Check if its the birthday. Compares the date/month values of the two dates.
     *
     * @param Carbon $dt
     *
     * @return boolean
     */
    public function isBirthday(Carbon $dt)
    {
        return $this->format('md') === $dt->format('md');
    }
}
