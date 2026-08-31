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

interface DiffOptions
{
    /**
     * Diff wording options(expressed in octal).
     */
    public const NO_ZERO_DIFF = 01;
    public const JUST_NOW = 02;
    public const ONE_DAY_WORDS = 04;
    public const TWO_DAY_WORDS = 010;
    public const SEQUENTIAL_PARTS_ONLY = 020;
    public const ROUND = 040;
    public const FLOOR = 0100;
    public const CEIL = 0200;

    /**
     * Diff syntax options.
     */
    public const DIFF_ABSOLUTE = 1; // backward compatibility with true
    public const DIFF_RELATIVE_AUTO = 0; // backward compatibility with false
    public const DIFF_RELATIVE_TO_NOW = 2;
    public const DIFF_RELATIVE_TO_OTHER = 3;
}
