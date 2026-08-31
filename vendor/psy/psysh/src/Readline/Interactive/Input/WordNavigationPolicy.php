<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Input;

/**
 * Word-based cursor navigation policy.
 */
class WordNavigationPolicy
{
    /**
     * Find the position of the start of the previous word.
     */
    public function findPreviousWord(string $text, int $cursor): int
    {
        $beforeCursor = \rtrim(\mb_substr($text, 0, $cursor));

        if (\preg_match('/[^\w]*(\w+)[^\w]*$/', $beforeCursor, $matches, \PREG_OFFSET_CAPTURE)) {
            return $matches[1][1];
        }

        return 0;
    }

    /**
     * Find the position of the start of the next word.
     */
    public function findNextWord(string $text, int $cursor): int
    {
        $afterCursor = \mb_substr($text, $cursor);

        if (\preg_match('/^\W*\w+/', $afterCursor, $matches)) {
            return $cursor + \mb_strlen($matches[0]);
        }

        return \mb_strlen($text);
    }
}
