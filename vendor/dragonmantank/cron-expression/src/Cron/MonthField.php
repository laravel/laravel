<?php

declare(strict_types=1);

namespace Cron;

use DateTimeInterface;

/**
 * Month field.  Allows: * , / -.
 */
class MonthField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    protected $rangeStart = 1;

    /**
     * {@inheritdoc}
     */
    protected $rangeEnd = 12;

    /**
     * {@inheritdoc}
     */
    protected $literals = [1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR', 5 => 'MAY', 6 => 'JUN', 7 => 'JUL',
        8 => 'AUG', 9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DEC', ];

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy(DateTimeInterface $date, $value, bool $invert): bool
    {
        if ($value === '?') {
            return true;
        }

        $value = $this->convertLiterals($value);

        return $this->isSatisfied((int) $date->format('m'), $value);
    }

    /**
     * @inheritDoc
     *
     * @param \DateTime|\DateTimeImmutable $date
     */
    public function increment(DateTimeInterface &$date, $invert = false, $parts = null): FieldInterface
    {
        if (! $invert) {
            $date = $date->modify('first day of next month');
            $date = $date->setTime(0, 0);
        } else {
            $date = $date->modify('last day of previous month');
            $date = $date->setTime(23, 59);
        }

        return $this;
    }
}
