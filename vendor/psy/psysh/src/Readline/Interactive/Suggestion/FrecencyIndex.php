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

use Psy\Readline\Interactive\Helper\DebugLog;
use Psy\Readline\Interactive\Input\History;

/**
 * Frecency (frequency + recency) index for history-based ranking.
 *
 * Analyzes command history to build a weighted map of important words.
 * Uses both frequency (how often) and recency (how recent) to rank words.
 */
class FrecencyIndex
{
    /** Word to frecency score map, keyed by word. */
    private array $scores = [];

    /** Common PHP keywords to exclude. */
    private const STOPWORDS = [
        // PHP keywords
        'if', 'else', 'elseif', 'endif', 'for', 'foreach', 'endforeach',
        'while', 'endwhile', 'do', 'switch', 'case', 'default', 'break',
        'continue', 'return', 'function', 'class', 'interface', 'trait',
        'extends', 'implements', 'namespace', 'use', 'as', 'new', 'clone',
        'public', 'protected', 'private', 'static', 'final', 'abstract',
        'const', 'var', 'global', 'echo', 'print', 'isset', 'empty', 'unset',
        'exit', 'die', 'eval', 'include', 'require', 'include_once', 'require_once',
        'and', 'or', 'xor', 'not', 'true', 'false', 'null', 'try', 'catch',
        'finally', 'throw', 'instanceof', 'goto', 'yield', 'from', 'declare',
        // Common short words (but NOT 'array' - it's useful!)
        'this', 'self', 'parent', 'list', 'fn',
    ];

    /** Time decay factor (higher = more weight on recent). */
    private const RECENCY_DECAY = 0.95;

    /** Number of days to consider for recency. */
    private const RECENCY_WINDOW = 30;

    /**
     * Build frecency index from history.
     */
    public function __construct(History $history)
    {
        $this->buildIndex($history);
    }

    /**
     * Get frecency score for a word.
     *
     * Returns 0.0 for unknown words.
     */
    public function getScore(string $word): float
    {
        return $this->scores[\strtolower($word)] ?? 0.0;
    }

    /**
     * Get all scored words.
     *
     * @return float[] Keyed by word
     */
    public function getAllScores(): array
    {
        return $this->scores;
    }

    /**
     * Check if a word exists in the index.
     */
    public function hasWord(string $word): bool
    {
        return isset($this->scores[\strtolower($word)]);
    }

    /**
     * Build the frecency index from history entries.
     */
    private function buildIndex(History $history): void
    {
        $now = \time();
        $windowStart = $now - (self::RECENCY_WINDOW * 86400); // Days to seconds

        $historyCount = $history->getCount();
        DebugLog::log('FrecencyIndex', 'BUILD_START', [
            'history_count' => $historyCount,
            'window_days'   => self::RECENCY_WINDOW,
        ]);

        $wordData = [];
        $processedEntries = 0;

        foreach ($history->getAll() as $entry) {
            $timestamp = $entry['timestamp'] ?? $now;

            if ($timestamp < $windowStart) {
                continue;
            }

            $processedEntries++;

            $words = $this->extractWords($entry['command']);

            $age = ($now - $timestamp) / 86400;

            $recencyWeight = \pow(self::RECENCY_DECAY, $age);

            if ($processedEntries <= 3) {
                DebugLog::log('FrecencyIndex', 'ENTRY', [
                    'num'    => $processedEntries,
                    'age'    => \sprintf('%.1f', $age).'d',
                    'weight' => \sprintf('%.3f', $recencyWeight),
                    'words'  => \implode(', ', $words),
                ]);
            }

            foreach ($words as $word) {
                if (!isset($wordData[$word])) {
                    $wordData[$word] = [
                        'count'       => 0,
                        'totalWeight' => 0.0,
                    ];
                }

                $wordData[$word]['count']++;
                $wordData[$word]['totalWeight'] += $recencyWeight;
            }
        }

        DebugLog::log('FrecencyIndex', 'PROCESSED', [
            'entries' => $processedEntries,
            'words'   => \count($wordData),
        ]);

        foreach ($wordData as $word => $data) {
            $avgRecency = $data['totalWeight'] / $data['count'];
            $frequency = $data['count'];
            $score = $frequency * $avgRecency;
            $this->scores[$word] = \min(100.0, $score * 10);
        }

        \arsort($this->scores);

        $top = \array_slice($this->scores, 0, 10, true);
        foreach ($top as $word => $score) {
            $count = $wordData[$word]['count'];
            $avgRec = $wordData[$word]['totalWeight'] / $count;
            DebugLog::log('FrecencyIndex', 'TOP_WORD', [
                'word'        => $word,
                'score'       => \sprintf('%.1f', $score),
                'freq'        => $count,
                'avg_recency' => \sprintf('%.3f', $avgRec),
            ]);
        }
    }

    /**
     * Extract meaningful words from a command.
     *
     * @return string[] Normalized word list
     */
    private function extractWords(string $command): array
    {
        return \array_values(\array_filter(
            WordExtractor::extractNormalizedIdentifiers($command),
            fn (string $word): bool => !\ctype_digit($word) && !\in_array($word, self::STOPWORDS, true)
        ));
    }
}
