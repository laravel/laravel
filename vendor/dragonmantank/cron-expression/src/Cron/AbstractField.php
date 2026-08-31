<?php

declare(strict_types=1);

namespace Cron;

use DateTimeInterface;

/**
 * Abstract CRON expression field.
 */
abstract class AbstractField implements FieldInterface
{
    /**
     * Full range of values that are allowed for this field type.
     *
     * @var array<int, int>
     */
    protected $fullRange = [];

    /**
     * Literal values we need to convert to integers.
     *
     * @var array<int, string>
     */
    protected $literals = [];

    /**
     * Start value of the full range.
     *
     * @var int
     */
    protected $rangeStart;

    /**
     * End value of the full range.
     *
     * @var int
     */
    protected $rangeEnd;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fullRange = range($this->rangeStart, $this->rangeEnd);
    }

    /**
     * Check to see if a field is satisfied by a value.
     *
     * @internal
     * @param int $dateValue Date value to check
     * @param string $value Value to test
     *
     * @return bool
     */
    public function isSatisfied(int $dateValue, string $value): bool
    {
        if ($this->isIncrementsOfRanges($value)) {
            return $this->isInIncrementsOfRanges($dateValue, $value);
        }

        if ($this->isRange($value)) {
            return $this->isInRange($dateValue, $value);
        }

        return '*' === $value || $dateValue === (int) $value;
    }

    /**
     * Check if a value is a range.
     *
     * @internal
     * @param string $value Value to test
     *
     * @return bool
     */
    public function isRange(string $value): bool
    {
        return false !== strpos($value, '-');
    }

    /**
     * Check if a value is an increments of ranges.
     *
     * @internal
     * @param string $value Value to test
     *
     * @return bool
     */
    public function isIncrementsOfRanges(string $value): bool
    {
        return false !== strpos($value, '/');
    }

    /**
     * Test if a value is within a range.
     *
     * @internal
     * @param int $dateValue Set date value
     * @param string $value Value to test
     *
     * @return bool
     */
    public function isInRange(int $dateValue, $value): bool
    {
        $parts = array_map(
            function ($value) {
                $value = trim($value);

                return $this->convertLiterals($value);
            },
            explode('-', $value, 2)
        );

        return $dateValue >= $parts[0] && $dateValue <= $parts[1];
    }

    /**
     * Test if a value is within an increments of ranges (offset[-to]/step size).
     *
     * @internal
     * @param int $dateValue Set date value
     * @param string $value Value to test
     *
     * @return bool
     */
    public function isInIncrementsOfRanges(int $dateValue, string $value): bool
    {
        $chunks = array_map('trim', explode('/', $value, 2));
        $range = $chunks[0];
        $step = $chunks[1] ?? 0;

        // No step or 0 steps aren't cool
        if (null === $step || '0' === $step || 0 === $step) {
            return false;
        }

        // Expand the * to a full range
        if ('*' === $range) {
            $range = $this->rangeStart . '-' . $this->rangeEnd;
        }

        // Generate the requested small range
        $rangeChunks = explode('-', $range, 2);
        $rangeStart = (int) $rangeChunks[0];
        $rangeEnd = $rangeChunks[1] ?? $rangeStart;
        $rangeEnd = (int) $rangeEnd;

        if ($rangeStart < $this->rangeStart || $rangeStart > $this->rangeEnd || $rangeStart > $rangeEnd) {
            throw new \OutOfRangeException('Invalid range start requested');
        }

        if ($rangeEnd < $this->rangeStart || $rangeEnd > $this->rangeEnd || $rangeEnd < $rangeStart) {
            throw new \OutOfRangeException('Invalid range end requested');
        }

        // Steps larger than the range need to wrap around and be handled
        // slightly differently than smaller steps

        // UPDATE - This is actually false. The C implementation will allow a
        // larger step as valid syntax, it never wraps around. It will stop
        // once it hits the end. Unfortunately this means in future versions
        // we will not wrap around. However, because the logic exists today
        // per the above documentation, fixing the bug from #89
        if ($step > $this->rangeEnd) {
            $thisRange = [$this->fullRange[(int) $step % \count($this->fullRange)]];
        } else {
            if ($step > ($rangeEnd - $rangeStart)) {
                $thisRange[$rangeStart] = (int) $rangeStart;
            } else {
                $thisRange = range($rangeStart, $rangeEnd, (int) $step);
            }
        }

        return \in_array($dateValue, $thisRange, true);
    }

    /**
     * Returns a range of values for the given cron expression.
     *
     * @param string $expression The expression to evaluate
     * @param int $max Maximum offset for range
     *
     * @return array<int, int>
     */
    public function getRangeForExpression(string $expression, int $max): array
    {
        $values = [];
        $expression = $this->convertLiterals($expression);

        if (false !== strpos($expression, ',')) {
            $ranges = explode(',', $expression);
            $values = [];
            foreach ($ranges as $range) {
                $expanded = $this->getRangeForExpression($range, $this->rangeEnd);
                $values = array_merge($values, $expanded);
            }

            return $values;
        }

        if ($this->isRange($expression) || $this->isIncrementsOfRanges($expression)) {
            if (!$this->isIncrementsOfRanges($expression)) {
                [$offset, $to] = explode('-', $expression);
                $offset = $this->convertLiterals($offset);
                $to = $this->convertLiterals($to);
                $stepSize = 1;
            } else {
                $range = array_map('trim', explode('/', $expression, 2));
                $stepSize = $range[1] ?? 0;
                $range = $range[0];
                $range = explode('-', $range, 2);
                $offset = $range[0];
                $to = $range[1] ?? $max;
            }
            $offset = '*' === $offset ? $this->rangeStart : $offset;
            if ($stepSize >= $this->rangeEnd) {
                $values = [$this->fullRange[(int) $stepSize % \count($this->fullRange)]];
            } else {
                for ($i = $offset; $i <= $to; $i += $stepSize) {
                    $values[] = (int) $i;
                }
            }
            sort($values);
        } else {
            $values = [$expression];
        }

        return $values;
    }

    /**
     * Convert literal.
     *
     * @param string $value
     *
     * @return string
     */
    protected function convertLiterals(string $value): string
    {
        if (\count($this->literals)) {
            $key = array_search(strtoupper($value), $this->literals, true);
            if (false !== $key) {
                return (string) $key;
            }
        }

        return $value;
    }

    /**
     * Checks to see if a value is valid for the field.
     *
     * @param string $value
     *
     * @return bool
     */
    public function validate(string $value): bool
    {
        $value = $this->convertLiterals($value);

        // All fields allow * as a valid value
        if ('*' === $value) {
            return true;
        }

        // Validate each chunk of a list individually
        if (false !== strpos($value, ',')) {
            foreach (explode(',', $value) as $listItem) {
                if (!$this->validate($listItem)) {
                    return false;
                }
            }

            return true;
        }

        if (false !== strpos($value, '/')) {
            [$range, $step] = explode('/', $value);

            // Don't allow numeric ranges
            if (is_numeric($range)) {
                return false;
            }

            return $this->validate($range) && filter_var($step, FILTER_VALIDATE_INT);
        }

        if (false !== strpos($value, '-')) {
            if (substr_count($value, '-') > 1) {
                return false;
            }

            $chunks = explode('-', $value);
            $chunks[0] = $this->convertLiterals($chunks[0]);
            $chunks[1] = $this->convertLiterals($chunks[1]);

            if ('*' === $chunks[0] || '*' === $chunks[1]) {
                return false;
            }

            return $this->validate($chunks[0]) && $this->validate($chunks[1]);
        }

        if (!is_numeric($value)) {
            return false;
        }

        if (false !== strpos($value, '.')) {
            return false;
        }

        // We should have a numeric by now, so coerce this into an integer
        $value = (int) $value;

        return \in_array($value, $this->fullRange, true);
    }

    protected function timezoneSafeModify(DateTimeInterface $dt, string $modification): DateTimeInterface
    {
        $timezone = $dt->getTimezone();
        $dt = $dt->setTimezone(new \DateTimeZone("UTC"));
        $dt = $dt->modify($modification);
        $dt = $dt->setTimezone($timezone);
        return $dt;
    }

    protected function setTimeHour(DateTimeInterface $date, bool $invert, int $originalTimestamp): DateTimeInterface
    {
        $date = $date->setTime((int)$date->format('H'), ($invert ? 59 : 0));

        // setTime caused the offset to change, moving time in the wrong direction
        $actualTimestamp = $date->format('U');
        if ((! $invert) && ($actualTimestamp <= $originalTimestamp)) {
            $date = $this->timezoneSafeModify($date, "+1 hour");
        } elseif ($invert && ($actualTimestamp >= $originalTimestamp)) {
            $date = $this->timezoneSafeModify($date, "-1 hour");
        }

        return $date;
    }
}
