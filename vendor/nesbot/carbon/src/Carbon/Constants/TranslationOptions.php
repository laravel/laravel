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

interface TranslationOptions
{
    /**
     * Translate string options.
     */
    public const TRANSLATE_MONTHS = 1;
    public const TRANSLATE_DAYS = 2;
    public const TRANSLATE_UNITS = 4;
    public const TRANSLATE_MERIDIEM = 8;
    public const TRANSLATE_DIFF = 0x10;
    public const TRANSLATE_ALL = self::TRANSLATE_MONTHS | self::TRANSLATE_DAYS | self::TRANSLATE_UNITS | self::TRANSLATE_MERIDIEM | self::TRANSLATE_DIFF;

    /**
     * Special settings to get the start of week from current locale culture.
     */
    public const WEEK_DAY_AUTO = 'auto';

    /**
     * Default locale (language and region).
     *
     * @var string
     */
    public const DEFAULT_LOCALE = 'en';
}
