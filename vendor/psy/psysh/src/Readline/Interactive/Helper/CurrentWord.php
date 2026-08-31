<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Helper;

/**
 * Shared current-word extraction for completion and suggestion flows.
 */
final class CurrentWord
{
    /**
     * Extract the word immediately before the cursor.
     *
     * Word boundaries stop at whitespace, '$', '->', and '::'.
     */
    public static function extract(string $line, int $position): string
    {
        $length = \mb_strlen($line);
        $position = \max(0, \min($position, $length));

        if ($position === 0 || $line === '') {
            return '';
        }

        $start = $position;
        while ($start > 0) {
            $char = \mb_substr($line, $start - 1, 1);
            if ($char === '' || \ctype_space($char) || $char === '$') {
                break;
            }

            if ($start >= 2) {
                $prev = \mb_substr($line, $start - 2, 1);
                if (($prev === '-' && $char === '>') || ($prev === ':' && $char === ':')) {
                    break;
                }
            }

            $start--;
        }

        return \mb_substr($line, $start, $position - $start);
    }
}
