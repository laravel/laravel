<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Suggestion;

/**
 * Shared identifier extraction for suggestion/frecency logic.
 */
final class WordExtractor
{
    /**
     * Extract normalized identifier-like words from text.
     *
     * @return string[]
     */
    public static function extractNormalizedIdentifiers(string $text): array
    {
        $words = [];

        \preg_match_all(
            '/(?:\$)?[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*/u',
            $text,
            $matches
        );

        foreach ($matches[0] as $word) {
            $normalized = \strtolower(\ltrim($word, '$'));

            if (\strlen($normalized) < 3) {
                continue;
            }

            $words[] = $normalized;
        }

        return \array_unique($words);
    }
}
