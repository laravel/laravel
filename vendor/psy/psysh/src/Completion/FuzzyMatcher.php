<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion;

/**
 * Fuzzy matching utility for tab completion.
 *
 * Matches candidates where the search string's characters appear in order,
 * similar to Fish shell and modern IDE fuzzy completion.
 *
 * Examples:
 *   - "asum" matches "array_sum"
 *   - "stl" matches "strtolower"
 *   - "AE" matches "ArrayException" (case-insensitive)
 */
class FuzzyMatcher
{
    /**
     * Filter candidates using fuzzy matching.
     *
     * Returns candidates where all characters in the search string appear
     * in order within the candidate string (case-insensitive).
     *
     * Results are sorted by match quality (exact prefix matches first,
     * then by how early the match starts).
     *
     * @param string   $search     Search string (e.g., "asum")
     * @param string[] $candidates Array of candidates to filter
     *
     * @return string[] Filtered and sorted candidates
     */
    public static function filter(string $search, array $candidates): array
    {
        if ($search === '') {
            $sorted = $candidates;
            \sort($sorted);

            return $sorted;
        }

        $matches = [];
        foreach ($candidates as $candidate) {
            $score = self::match($search, $candidate);
            if ($score !== null) {
                $matches[] = ['candidate' => $candidate, 'score' => $score];
            }
        }

        // Sort by score (lower is better), then alphabetically
        \usort($matches, function ($a, $b) {
            if ($a['score'] !== $b['score']) {
                return $a['score'] <=> $b['score'];
            }

            return \strcmp($a['candidate'], $b['candidate']);
        });

        return \array_column($matches, 'candidate');
    }

    /**
     * Check if search matches candidate and return a quality score.
     *
     * Returns null if no match, or a numeric score where lower is better.
     * Score factors:
     *   - Exact prefix match gets lowest score (best)
     *   - Earlier matches get better scores
     *   - Consecutive character matches get bonus
     *   - First character must match at start or after a word boundary
     *
     * @return int|null Match score (lower is better), or null if no match
     */
    private static function match(string $search, string $candidate): ?int
    {
        $searchLen = \strlen($search);
        $candidateLen = \strlen($candidate);

        if ($searchLen === 0) {
            return 0;
        }

        $searchLower = \strtolower($search);
        $candidateLower = \strtolower($candidate);

        // Check for exact prefix match first (best score)
        if (\strpos($candidateLower, $searchLower) === 0) {
            return 0;
        }

        // Check if it contains the search as a substring (very good score)
        $substringPos = \strpos($candidateLower, $searchLower);
        if ($substringPos !== false) {
            // Only match if substring starts at a word boundary
            if ($substringPos === 0 || self::isWordBoundary($candidate[$substringPos - 1])) {
                return $substringPos + 1;
            }
        }

        // Fuzzy match: characters must appear in order
        // First character MUST match at start or after a word boundary
        $searchIdx = 0;
        $candidateIdx = 0;
        $lastMatchIdx = -1;
        $firstMatchIdx = null;
        $consecutiveMatches = 0;
        $firstCharFound = false;

        while ($searchIdx < $searchLen && $candidateIdx < $candidateLen) {
            if ($searchLower[$searchIdx] === $candidateLower[$candidateIdx]) {
                // For the first character, ensure it's at a word boundary
                if ($searchIdx === 0) {
                    if ($candidateIdx === 0 || self::isWordBoundary($candidate[$candidateIdx - 1])) {
                        $firstCharFound = true;
                    } else {
                        // First char not at word boundary, skip it
                        $candidateIdx++;
                        continue;
                    }
                }

                if ($firstMatchIdx === null) {
                    $firstMatchIdx = $candidateIdx;
                }

                // Track consecutive matches
                if ($candidateIdx === $lastMatchIdx + 1) {
                    $consecutiveMatches++;
                }

                $lastMatchIdx = $candidateIdx;
                $searchIdx++;
            }
            $candidateIdx++;
        }

        // Did we match all search characters, and did the first char match at a word boundary?
        if ($searchIdx < $searchLen || !$firstCharFound) {
            return null;
        }

        // Score: position of first match + distance between matches - consecutive bonus
        // Lower score is better
        $score = 100 + $firstMatchIdx + ($lastMatchIdx - $firstMatchIdx) - ($consecutiveMatches * 10);

        return $score;
    }

    /**
     * Check if a character is a word boundary.
     *
     * Word boundaries include: underscore, space, dash, slash, backslash, and other non-alphanumeric chars.
     */
    private static function isWordBoundary(string $char): bool
    {
        return !\ctype_alnum($char);
    }

    /**
     * Check if search string matches candidate (fuzzy).
     *
     * @return bool True if all characters in search appear in order in candidate
     */
    public static function matches(string $search, string $candidate): bool
    {
        return self::match($search, $candidate) !== null;
    }
}
