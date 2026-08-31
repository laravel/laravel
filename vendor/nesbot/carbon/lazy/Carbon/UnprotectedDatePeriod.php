<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon;

use DatePeriod;

if (!class_exists(DatePeriodBase::class, false)) {
    class DatePeriodBase extends DatePeriod
    {
    }
}
