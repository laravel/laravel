<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

use League\CommonMark\Exception\InvalidArgumentException;

/**
 * @psalm-immutable
 */
final class HtmlFilter
{
    // Return the entire string as-is
    public const ALLOW = 'allow';
    // Escape the entire string so any HTML/JS won't be interpreted as such
    public const ESCAPE = 'escape';
    // Return an empty string
    public const STRIP = 'strip';

    /**
     * Runs the given HTML through the given filter
     *
     * @param string $html   HTML input to be filtered
     * @param string $filter One of the HtmlFilter constants
     *
     * @return string Filtered HTML
     *
     * @throws InvalidArgumentException when an invalid $filter is given
     *
     * @psalm-pure
     */
    public static function filter(string $html, string $filter): string
    {
        switch ($filter) {
            case self::STRIP:
                return '';
            case self::ESCAPE:
                return \htmlspecialchars($html, \ENT_NOQUOTES);
            case self::ALLOW:
                return $html;
            default:
                throw new InvalidArgumentException(\sprintf('Invalid filter provided: "%s"', $filter));
        }
    }
}
