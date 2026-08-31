<?php

namespace DeepCopy\TypeFilter\Date;

use DatePeriod;
use DeepCopy\TypeFilter\TypeFilter;

/**
 * @final
 */
class DatePeriodFilter implements TypeFilter
{
    /**
     * {@inheritdoc}
     *
     * @param DatePeriod $element
     *
     * @see http://news.php.net/php.bugs/205076
     */
    public function apply($element)
    {
        $options = 0;
        if (PHP_VERSION_ID >= 80200 && $element->include_end_date) {
            $options |= DatePeriod::INCLUDE_END_DATE;
        }
        if (!$element->include_start_date) {
            $options |= DatePeriod::EXCLUDE_START_DATE;
        }

        if ($element->getEndDate()) {
            return new DatePeriod($element->getStartDate(), $element->getDateInterval(), $element->getEndDate(), $options);
        }

        if (PHP_VERSION_ID >= 70217) {
            $recurrences = $element->getRecurrences();
        } else {
            $recurrences = $element->recurrences - $element->include_start_date;
        }

        return new DatePeriod($element->getStartDate(), $element->getDateInterval(), $recurrences, $options);
    }
}
