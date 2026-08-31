<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon\Traits;

use Carbon\CarbonInterface;
use Carbon\CarbonInterval;

trait DeprecatedPeriodProperties
{
    /**
     * Period start in PHP < 8.2.
     *
     * @var CarbonInterface
     *
     * @deprecated PHP 8.2 this property is no longer in sync with the actual period start.
     */
    public $start;

    /**
     * Period end in PHP < 8.2.
     *
     * @var CarbonInterface|null
     *
     * @deprecated PHP 8.2 this property is no longer in sync with the actual period end.
     */
    public $end;

    /**
     * Period current iterated date in PHP < 8.2.
     *
     * @var CarbonInterface|null
     *
     * @deprecated PHP 8.2 this property is no longer in sync with the actual period current iterated date.
     */
    public $current;

    /**
     * Period interval in PHP < 8.2.
     *
     * @var CarbonInterval|null
     *
     * @deprecated PHP 8.2 this property is no longer in sync with the actual period interval.
     */
    public $interval;

    /**
     * Period recurrences in PHP < 8.2.
     *
     * @var int|float|null
     *
     * @deprecated PHP 8.2 this property is no longer in sync with the actual period recurrences.
     */
    public $recurrences;

    /**
     * Period start included option in PHP < 8.2.
     *
     * @var bool
     *
     * @deprecated PHP 8.2 this property is no longer in sync with the actual period start included option.
     */
    public $include_start_date;

    /**
     * Period end included option in PHP < 8.2.
     *
     * @var bool
     *
     * @deprecated PHP 8.2 this property is no longer in sync with the actual period end included option.
     */
    public $include_end_date;
}
