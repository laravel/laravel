<?php

declare(strict_types=1);

namespace Cron;

use DateTimeInterface;
use InvalidArgumentException;

/**
 * Day of week field.  Allows: * / , - ? L #.
 *
 * Days of the week can be represented as a number 0-7 (0|7 = Sunday)
 * or as a three letter string: SUN, MON, TUE, WED, THU, FRI, SAT.
 *
 * 'L' stands for "last". It allows you to specify constructs such as
 * "the last Friday" of a given month.
 *
 * '#' is allowed for the day-of-week field, and must be followed by a
 * number between one and five. It allows you to specify constructs such as
 * "the second Friday" of a given month.
 */
class DayOfWeekField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    protected $rangeStart = 0;

    /**
     * {@inheritdoc}
     */
    protected $rangeEnd = 7;

    /**
     * @var array<int, int> Weekday range
     */
    protected $nthRange;

    /**
     * {@inheritdoc}
     */
    protected $literals = [1 => 'MON', 2 => 'TUE', 3 => 'WED', 4 => 'THU', 5 => 'FRI', 6 => 'SAT', 7 => 'SUN'];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nthRange = range(1, 5);
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(DateTimeInterface $date, $value, bool $invert): bool
    {
        if ('?' === $value) {
            return true;
        }

        // Convert text day of the week values to integers
        $value = $this->convertLiterals($value);

        $currentYear = (int) $date->format('Y');
        $currentMonth = (int) $date->format('m');
        $lastDayOfMonth = (int) $date->format('t');

        // Find out if this is the last specific weekday of the month
        if ($lPosition = strpos($value, 'L')) {
            $weekday = (int) $this->convertLiterals(substr($value, 0, $lPosition));
            $weekday %= 7;

            $daysInMonth = (int) $date->format('t');
            $remainingDaysInMonth = $daysInMonth - (int) $date->format('d');
            return (($weekday === (int) $date->format('w')) && ($remainingDaysInMonth < 7));
        }

        // Handle # hash tokens
        if (strpos($value, '#')) {
            [$weekday, $nth] = explode('#', $value);

            if (!is_numeric($nth)) {
                throw new InvalidArgumentException("Hashed weekdays must be numeric, {$nth} given");
            } else {
                $nth = (int) $nth;
            }

            // 0 and 7 are both Sunday, however 7 matches date('N') format ISO-8601
            if ('0' === $weekday) {
                $weekday = 7;
            }

            $weekday = (int) $this->convertLiterals((string) $weekday);

            // Validate the hash fields
            if ($weekday < 0 || $weekday > 7) {
                throw new InvalidArgumentException("Weekday must be a value between 0 and 7. {$weekday} given");
            }

            if (!\in_array($nth, $this->nthRange, true)) {
                throw new InvalidArgumentException("There are never more than 5 or less than 1 of a given weekday in a month, {$nth} given");
            }

            // The current weekday must match the targeted weekday to proceed
            if ((int) $date->format('N') !== $weekday) {
                return false;
            }

            $tdate = clone $date;
            $tdate = $tdate->setDate($currentYear, $currentMonth, 1);
            $dayCount = 0;
            $currentDay = 1;
            while ($currentDay < $lastDayOfMonth + 1) {
                if ((int) $tdate->format('N') === $weekday) {
                    if (++$dayCount >= $nth) {
                        break;
                    }
                }
                $tdate = $tdate->setDate($currentYear, $currentMonth, ++$currentDay);
            }

            return (int) $date->format('j') === $currentDay;
        }

        // Handle day of the week values
        if (false !== strpos($value, '-')) {
            $parts = explode('-', $value);
            if ('7' === $parts[0]) {
                $parts[0] = 0;
            } elseif ('0' === $parts[1]) {
                $parts[1] = 7;
            }
            $value = implode('-', $parts);
        }

        // Test to see which Sunday to use -- 0 == 7 == Sunday
        $format = \in_array(7, array_map(function ($value) {
            return (int) $value;
        }, str_split($value)), true) ? 'N' : 'w';
        $fieldValue = (int) $date->format($format);

        return $this->isSatisfied($fieldValue, $value);
    }

    /**
     * @inheritDoc
     */
    public function increment(DateTimeInterface &$date, $invert = false, $parts = null): FieldInterface
    {
        if (! $invert) {
            $date = $date->add(new \DateInterval('P1D'));
            $date = $date->setTime(0, 0);
        } else {
            $date = $date->sub(new \DateInterval('P1D'));
            $date = $date->setTime(23, 59);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(string $value): bool
    {
        $basicChecks = parent::validate($value);

        if (!$basicChecks) {
            if ('?' === $value) {
                return true;
            }

            // Handle the # value
            if (false !== strpos($value, '#')) {
                $chunks = explode('#', $value);
                $chunks[0] = $this->convertLiterals($chunks[0]);

                if (parent::validate($chunks[0]) && is_numeric($chunks[1]) && \in_array((int) $chunks[1], $this->nthRange, true)) {
                    return true;
                }
            }

            if (preg_match('/^(.*)L$/', $value, $matches)) {
                return $this->validate($matches[1]);
            }

            return false;
        }

        return $basicChecks;
    }
}
