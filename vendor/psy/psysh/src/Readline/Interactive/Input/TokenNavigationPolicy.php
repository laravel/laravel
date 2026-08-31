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
 * Token-based cursor navigation policy.
 */
class TokenNavigationPolicy
{
    private const PHP_PREFIX_LENGTH = 6;

    /**
     * @param array $tokens
     * @param array $tokenPositions
     */
    public function findPreviousToken(array $tokens, array $tokenPositions, int $cursor): int
    {
        if (empty($tokens) || empty($tokenPositions)) {
            return 0;
        }

        $cursorPos = $cursor + self::PHP_PREFIX_LENGTH;
        $lastNonWhitespaceStart = 0;

        foreach ($tokens as $index => $token) {
            $tokenStart = $tokenPositions[$index]['start'];
            $tokenEnd = $tokenPositions[$index]['end'];

            if ($tokenStart < self::PHP_PREFIX_LENGTH) {
                continue;
            }

            if (\is_array($token) && $token[0] === \T_WHITESPACE) {
                continue;
            }

            // Cursor is inside this token — jump to its start
            if ($tokenStart < $cursorPos && $cursorPos <= $tokenEnd) {
                return $tokenStart - self::PHP_PREFIX_LENGTH;
            }

            // Cursor is before this token — jump to previous non-whitespace token
            if ($tokenStart >= $cursorPos) {
                return \max(0, $lastNonWhitespaceStart - self::PHP_PREFIX_LENGTH);
            }

            $lastNonWhitespaceStart = $tokenStart;
        }

        return \max(0, $lastNonWhitespaceStart - self::PHP_PREFIX_LENGTH);
    }

    /**
     * @param array $tokens
     * @param array $tokenPositions
     */
    public function findNextToken(array $tokens, array $tokenPositions, int $cursor, int $lineLength): int
    {
        if (empty($tokens) || empty($tokenPositions)) {
            return $lineLength;
        }

        $cursorPos = $cursor + self::PHP_PREFIX_LENGTH;
        $skipCurrentToken = false;

        foreach ($tokens as $index => $token) {
            $tokenStart = $tokenPositions[$index]['start'];
            $tokenEnd = $tokenPositions[$index]['end'];

            if ($tokenStart < self::PHP_PREFIX_LENGTH) {
                continue;
            }

            if (\is_array($token) && $token[0] === \T_WHITESPACE) {
                continue;
            }

            if ($tokenStart <= $cursorPos && $cursorPos < $tokenEnd) {
                $skipCurrentToken = true;
                continue;
            }

            if ($tokenStart > $cursorPos || ($skipCurrentToken && $tokenStart >= $cursorPos)) {
                return $tokenStart - self::PHP_PREFIX_LENGTH;
            }
        }

        return $lineLength;
    }
}
