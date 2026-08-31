<?php

declare(strict_types=1);

namespace Cron;

use DateTimeInterface;
use DateTimeZone;

/**
 * Hours field.  Allows: * , / -.
 */
class HoursField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    protected $rangeStart = 0;

    /**
     * {@inheritdoc}
     */
    protected $rangeEnd = 23;

    /**
     * @var list<array<string, bool|int|string>>|null Transitions returned by DateTimeZone::getTransitions()
     */
    protected $transitions = [];

    /**
     * @var int|null Timestamp of the start of the transitions range
     */
    protected $transitionsStart = null;

    /**
     * @var int|null Timestamp of the end of the transitions range
     */
    protected $transitionsEnd = null;

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy(DateTimeInterface $date, $value, bool $invert): bool
    {
        $checkValue = (int) $date->format('H');
        $retval = $this->isSatisfied($checkValue, $value);
        if ($retval) {
            return $retval;
        }

        // Are we on the edge of a transition
        $lastTransition = $this->getPastTransition($date);
        if (($lastTransition !== null) && ($lastTransition["ts"] > ((int) $date->format('U') - 3600))) {
            $dtLastOffset = clone $date;
            $this->timezoneSafeModify($dtLastOffset, "-1 hour");
            $lastOffset = $dtLastOffset->getOffset();

            $dtNextOffset = clone $date;
            $this->timezoneSafeModify($dtNextOffset, "+1 hour");
            $nextOffset = $dtNextOffset->getOffset();

            $offsetChange = $nextOffset - $lastOffset;
            if ($offsetChange >= 3600) {
                $checkValue -= 1;
                return $this->isSatisfied($checkValue, $value);
            }
            if ((! $invert) && ($offsetChange <= -3600)) {
                $checkValue += 1;
                return $this->isSatisfied($checkValue, $value);
            }
        }

        return $retval;
    }

    /**
     * @return non-empty-array<string, bool|int|string>|null
     */
    public function getPastTransition(DateTimeInterface $date): ?array
    {
        $currentTimestamp = (int) $date->format('U');
        if (
            ($this->transitions === null)
            || ($this->transitionsStart < ($currentTimestamp + 86400))
            || ($this->transitionsEnd > ($currentTimestamp - 86400))
        ) {
            // We start a day before current time so we can differentiate between the first transition entry
            // and a change that happens now
            $dtLimitStart = clone $date;
            $dtLimitStart = $dtLimitStart->modify("-12 months");
            $dtLimitEnd = clone $date;
            $dtLimitEnd = $dtLimitEnd->modify('+12 months');

            $this->transitions = $date->getTimezone()->getTransitions(
                $dtLimitStart->getTimestamp(),
                $dtLimitEnd->getTimestamp()
            );
            if (empty($this->transitions)) {
                return null;
            }
            $this->transitionsStart = $dtLimitStart->getTimestamp();
            $this->transitionsEnd = $dtLimitEnd->getTimestamp();
        }

        $nextTransition = null;
        foreach ($this->transitions as $transition) {
            if ($transition["ts"] > $currentTimestamp) {
                continue;
            }

            if (($nextTransition !== null) && ($transition["ts"] < $nextTransition["ts"])) {
                continue;
            }

            $nextTransition = $transition;
        }

        return ($nextTransition ?? null);
    }

    /**
     * {@inheritdoc}
     *
     * @param string|null                  $parts
     */
    public function increment(DateTimeInterface &$date, $invert = false, $parts = null): FieldInterface
    {
        $originalTimestamp = (int) $date->format('U');

        // Change timezone to UTC temporarily. This will
        // allow us to go back or forwards and hour even
        // if DST will be changed between the hours.
        if (null === $parts || '*' === $parts) {
            if ($invert) {
                $date = $date->sub(new \DateInterval('PT1H'));
            } else {
                $date = $date->add(new \DateInterval('PT1H'));
            }

            $date = $this->setTimeHour($date, $invert, $originalTimestamp);
            return $this;
        }

        $parts = false !== strpos($parts, ',') ? explode(',', $parts) : [$parts];
        $hours = [];
        foreach ($parts as $part) {
            $hours = array_merge($hours, $this->getRangeForExpression($part, 23));
        }

        $current_hour = (int) $date->format('H');
        $position = $invert ? \count($hours) - 1 : 0;
        $countHours = \count($hours);
        if ($countHours > 1) {
            for ($i = 0; $i < $countHours - 1; ++$i) {
                if ((!$invert && $current_hour >= $hours[$i] && $current_hour < $hours[$i + 1]) ||
                    ($invert && $current_hour > $hours[$i] && $current_hour <= $hours[$i + 1])) {
                    $position = $invert ? $i : $i + 1;

                    break;
                }
            }
        }

        $target = (int) $hours[$position];
        $originalHour = (int)$date->format('H');
        $originalDst = (int)$date->format('I');

        $originalDay = (int)$date->format('d');
        $previousOffset = $date->getOffset();

        if (! $invert) {
            if ($originalHour >= $target) {
                $distance = 24 - $originalHour;
                $date = $this->timezoneSafeModify($date, "+{$distance} hours");

                $actualDay = (int)$date->format('d');
                $actualHour = (int)$date->format('H');
                if (($actualDay !== ($originalDay + 1)) && ($actualHour !== 0)) {
                    $offsetChange = ($previousOffset - $date->getOffset());
                    $date = $this->timezoneSafeModify($date, "+{$offsetChange} seconds");
                }

                $originalHour = (int)$date->format('H');
            }

            $distance = $target - $originalHour;
            $date = $this->timezoneSafeModify($date, "+{$distance} hours");
        } else {
            if ($originalHour <= $target) {
                $distance = ($originalHour + 1);
                $date = $this->timezoneSafeModify($date, "-" . $distance . " hours");

                $actualDay = (int)$date->format('d');
                $actualHour = (int)$date->format('H');
                if (($actualDay !== ($originalDay - 1)) && ($actualHour !== 23)) {
                    $offsetChange = ($previousOffset - $date->getOffset());
                    $date = $this->timezoneSafeModify($date, "+{$offsetChange} seconds");
                }

                $originalHour = (int)$date->format('H');
            }

            $distance = $originalHour - $target;
            $date = $this->timezoneSafeModify($date, "-{$distance} hours");
        }

        $actualDst = (int)$date->format('I');
        if ($originalDst < $actualDst) {
            $date = $this->timezoneSafeModify($date, "-1 hours");
        }

        $date = $this->setTimeHour($date, $invert, $originalTimestamp);

        $actualHour = (int)$date->format('H');
        if ($invert && ($actualHour === ($target - 1) || (($actualHour === 23) && ($target === 0)))) {
            $date = $this->timezoneSafeModify($date, "+1 hour");
        }

        return $this;
    }
}
