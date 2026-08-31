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

use Psy\Readline\Interactive\Input\Buffer;

/**
 * Bracket pairing helper.
 *
 * Stateless helper that determines when to auto-close brackets,
 * skip over closing brackets, and delete bracket pairs on backspace.
 */
class BracketPair
{
    public const OPENING_BRACKETS = ['(', '[', '{'];
    public const CLOSING_BRACKETS = [')', ']', '}'];

    private const PAIRS = [
        '(' => ')',
        '[' => ']',
        '{' => '}',
        '"' => '"',
        "'" => "'",
    ];

    private const CLOSING_TO_OPENING = [
        ')' => '(',
        ']' => '[',
        '}' => '{',
    ];

    /**
     * Should we auto-close this opening bracket?
     */
    public static function shouldAutoClose(string $char, Buffer $buffer): bool
    {
        if (!isset(self::PAIRS[$char])) {
            return false;
        }

        if (self::isQuote($char) && self::isInsideString($buffer)) {
            return false;
        }

        // Don't auto-close if next char is alphanumeric, e.g. typing "ar(ray"
        $nextChar = $buffer->getCharAfterCursor();
        if ($nextChar !== null && self::startsWithAlnum($nextChar)) {
            return false;
        }

        return true;
    }

    /**
     * Should we skip over the closing bracket instead of inserting?
     */
    public static function shouldSkipOver(string $char, Buffer $buffer): bool
    {
        $nextChar = $buffer->getCharAfterCursor();

        return $nextChar === $char;
    }

    /**
     * Should backspace delete the matching closing bracket?
     */
    public static function shouldDeletePair(Buffer $buffer): bool
    {
        $before = $buffer->getCharBeforeCursor();
        $after = $buffer->getCharAfterCursor();

        if ($before === null || $after === null) {
            return false;
        }

        return isset(self::PAIRS[$before]) && self::PAIRS[$before] === $after;
    }

    /**
     * Get the closing bracket for this opening bracket.
     */
    public static function getClosingBracket(string $openingBracket): ?string
    {
        return self::PAIRS[$openingBracket] ?? null;
    }

    /**
     * Check if a closing bracket matches the most recent unclosed opening bracket.
     *
     * Tokenizes the code to respect strings and comments when tracking brackets.
     */
    public static function doesClosingBracketMatch(string $closingChar, string $code): bool
    {
        if (!isset(self::CLOSING_TO_OPENING[$closingChar])) {
            return false;
        }

        $expectedOpening = self::CLOSING_TO_OPENING[$closingChar];

        $tokens = \token_get_all('<?php '.$code);
        $stack = [];

        foreach ($tokens as $token) {
            if (\is_array($token)) {
                $type = $token[0];
                if ($type === \T_CONSTANT_ENCAPSED_STRING ||
                    $type === \T_ENCAPSED_AND_WHITESPACE ||
                    $type === \T_COMMENT ||
                    $type === \T_DOC_COMMENT) {
                    continue;
                }
            }

            if (\is_string($token)) {
                if (\in_array($token, self::OPENING_BRACKETS)) {
                    $stack[] = $token;
                } elseif (isset(self::CLOSING_TO_OPENING[$token])) {
                    \array_pop($stack);
                }
            }
        }

        if (empty($stack)) {
            return false;
        }

        return \end($stack) === $expectedOpening;
    }

    /**
     * Check if the cursor is between empty brackets (excluding quotes).
     *
     * Used by deletion commands to extend deletion through empty bracket pairs.
     * Example: "foo(|)" → should delete "foo()"
     */
    public static function isInsideEmptyBrackets(Buffer $buffer): bool
    {
        $before = $buffer->getCharBeforeCursor();
        $after = $buffer->getCharAfterCursor();

        if ($before === null || $after === null) {
            return false;
        }

        // Only non-quote bracket pairs, not quotes like "" or ''
        return \in_array($before, self::OPENING_BRACKETS)
            && self::PAIRS[$before] === $after;
    }

    /**
     * Check whether a character is a quote.
     */
    private static function isQuote(string $char): bool
    {
        return $char === '"' || $char === "'";
    }

    /**
     * Check whether the grapheme cluster starts with an alphanumeric code point.
     */
    private static function startsWithAlnum(string $char): bool
    {
        $firstCodePoint = \mb_substr($char, 0, 1);

        return $firstCodePoint !== '' && \ctype_alnum($firstCodePoint);
    }

    /**
     * Simple heuristic to detect if cursor is inside a string.
     *
     * Counts quotes before cursor - if odd, we're inside a string.
     * Not perfect but works for most cases.
     */
    private static function isInsideString(Buffer $buffer): bool
    {
        $beforeCursor = $buffer->getBeforeCursor();

        $doubleQuotes = self::countUnescapedQuotes($beforeCursor, '"');
        $singleQuotes = self::countUnescapedQuotes($beforeCursor, "'");

        return ($doubleQuotes % 2 === 1) || ($singleQuotes % 2 === 1);
    }

    /**
     * Count unescaped quotes in the string.
     */
    private static function countUnescapedQuotes(string $text, string $quote): int
    {
        $count = 0;
        $escaped = false;

        for ($i = 0; $i < \strlen($text); $i++) {
            $char = $text[$i];

            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            if ($char === $quote) {
                $count++;
            }
        }

        return $count;
    }
}
