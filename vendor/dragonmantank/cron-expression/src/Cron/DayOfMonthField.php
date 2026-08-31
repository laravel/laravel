<?php

declare(strict_types=1);

namespace Cron;

use DateTime;
use DateTimeInterface;

/**
 * Day of month field.  Allows: * , / - ? L W.
 *
 * 'L' stands for "last" and specifies the last day of the month.
 *
 * The 'W' character is used to specify the weekday (Monday-Friday) nearest the
 * given day. As an example, if you were to specify "15W" as the value for the
 * day-of-month field, the meaning is: "the nearest weekday to the 15th of the
 * month". So if the 15th is a Saturday, the trigger will fire on Friday the
 * 14th. If the 15th is a Sunday, the trigger will fire on Monday the 16th. If
 * the 15th is a Tuesday, then it will fire on Tuesday the 15th. However if you
 * specify "1W" as the value for day-of-month, and the 1st is a Saturday, the
 * trigger will fire on Monday the 3rd, as it will not 'jump' over the boundary
 * of a month's days. The 'W' character can only be specified when the
 * day-of-month is a single day, not a range or list of days.
 *
 * @author Michael Dowling <mtdowling@gmail.com>
 */
class DayOfMonthField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    protected $rangeStart = 1;

    /**
     * {@inheritdoc}
     */
    protected $rangeEnd = 31;

    /**
     * Get the nearest day of the week for a given day in a month.
     *
     * @param int $currentYear Current year
     * @param int $currentMonth Current month
     * @param int $targetDay Target day of the month
     *
     * @return \DateTime|null Returns the nearest date
     */
    private static function getNearestWeekday(int $currentYear, int $currentMonth, int $targetDay): ?DateTime
    {
        $tday = str_pad((string) $targetDay, 2, '0', STR_PAD_LEFT);
        $target = DateTime::createFromFormat('Y-m-d', "{$currentYear}-{$currentMonth}-{$tday}");

        if ($target === false) {
            return null;
        }

        $currentWeekday = (int) $target->format('N');

        if ($currentWeekday < 6) {
            return $target;
        }

        $lastDayOfMonth = $target->format('t');
        foreach ([-1, 1, -2, 2] as $i) {
            $adjusted = $targetDay + $i;
            if ($adjusted > 0 && $adjusted <= $lastDayOfMonth) {
                $target->setDate($currentYear, $currentMonth, $adjusted);

                if ((int) $target->format('N') < 6 && (int) $target->format('m') === $currentMonth) {
                    return $target;
                }
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy(DateTimeInterface $date, $value, bool $invert): bool
    {
        // ? states that the field value is to be skipped
        if ('?' === $value) {
            return true;
        }

        $fieldValue = $date->format('d');

        // Check to see if this is the last day of the month
        if ('L' === $value) {
            return $fieldValue === $date->format('t');
        }

        // Check to see if this is the nearest weekday to a particular value
        if ($wPosition = strpos($value, 'W')) {
            // Parse the target day
            $targetDay = (int) substr($value, 0, $wPosition);
            // Find out if the current day is the nearest day of the week
            $nearest = self::getNearestWeekday(
                (int) $date->format('Y'),
                (int) $date->format('m'),
                $targetDay
            );
            if ($nearest) {
                return $date->format('j') === $nearest->format('j');
            }

            throw new \RuntimeException('Unable to return nearest weekday');
        }

        return $this->isSatisfied((int) $date->format('d'), $value);
    }

    /**
     * @inheritDoc
     *
     * @param \DateTime|\DateTimeImmutable $date
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

        // Validate that a list don't have W or L
        if (false !== strpos($value, ',') && (false !== strpos($value, 'W') || false !== strpos($value, 'L'))) {
            return false;
        }

        if (!$basicChecks) {
            if ('?' === $value) {
                return true;
            }

            if ('L' === $value) {
                return true;
            }

            if (preg_match('/^(.*)W$/', $value, $matches)) {
                return $this->validate($matches[1]);
            }

            return false;
        }

        return $basicChecks;
    }
}
