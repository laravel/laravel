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

namespace Carbon\Constants;

interface Format
{
    /**
     * RFC7231 DateTime format.
     *
     * @var string
     */
    public const RFC7231_FORMAT = 'D, d M Y H:i:s \G\M\T';

    /**
     * Default format to use for __toString method when type juggling occurs.
     *
     * @var string
     */
    public const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s';

    /**
     * Format for converting mocked time, includes microseconds.
     *
     * @var string
     */
    public const MOCK_DATETIME_FORMAT = 'Y-m-d H:i:s.u';

    /**
     * Pattern detection for ->isoFormat and ::createFromIsoFormat.
     *
     * @var string
     */
    public const ISO_FORMAT_REGEXP = '(O[YMDHhms]|[Hh]mm(ss)?|Mo|MM?M?M?|Do|DDDo|DD?D?D?|ddd?d?|do?|w[o|w]?|W[o|W]?|Qo?|YYYYYY|YYYYY|YYYY|YY?|g{1,5}|G{1,5}|e|E|a|A|hh?|HH?|kk?|mm?|ss?|S{1,9}|x|X|zz?|ZZ?)';
}
