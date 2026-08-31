<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

/**
 * Utility class for handling/generating XML and HTML
 *
 * @psalm-immutable
 */
final class Xml
{
    /**
     * @psalm-pure
     */
    public static function escape(string $string): string
    {
        return \str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $string);
    }
}
