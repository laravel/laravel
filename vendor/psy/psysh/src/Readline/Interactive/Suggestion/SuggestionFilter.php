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

/**
 * Validates and scores suggestions based on PHP context.
 *
 * Ensures suggestions make sense in the current context.
 */
class SuggestionFilter
{
    private ?FrecencyIndex $frecencyIndex = null;

    /**
     * Set frecency index for importance-based ranking.
     */
    public function setFrecencyIndex(FrecencyIndex $index): void
    {
        $this->frecencyIndex = $index;
    }

    /**
     * Check if suggestion is valid for the given buffer context.
     *
     * @param string $buffer     Current input buffer
     * @param string $suggestion Proposed suggestion text
     *
     * @return bool
     */
    public function isValid(string $buffer, string $suggestion): bool
    {
        if (\trim($suggestion) === '') {
            return false;
        }

        if ($this->isAfterObjectOperator($buffer)) {
            return !$this->looksLikeFunction($buffer.$suggestion);
        }

        return true;
    }

    /**
     * Score a suggestion for ranking purposes.
     *
     * Higher score = better suggestion.
     */
    public function score(string $buffer, SuggestionResult $suggestion): int
    {
        $score = 50;
        $appliedText = $suggestion->applyToBuffer($buffer);

        if ($this->frecencyIndex !== null) {
            $score += $this->calculateFrecencyBoost($appliedText);
        }

        $length = \mb_strlen($suggestion->getDisplayText());
        if ($length < 10) {
            $score += 5;
        } elseif ($length > 50) {
            $score -= 5;
        }

        switch ($suggestion->getSource()) {
            case SuggestionResult::SOURCE_CALL_SIGNATURE:
                $score += 15;
                break;
            case SuggestionResult::SOURCE_HISTORY:
                $score += 10;
                break;
        }

        if ($buffer !== '' && \strpos($appliedText, $buffer) === 0) {
            $score += 10;
        }

        if (\strpos($suggestion->getDisplayText(), "\n") === false) {
            $score += 5;
        }

        $finalScore = (int) \max(0, \min(100, $score));

        if (DebugLog::isEnabled()) {
            DebugLog::log('SuggestionFilter', 'SCORE', [
                'score'  => $finalScore,
                'source' => $suggestion->getSource(),
                'text'   => $appliedText,
            ]);
        }

        return $finalScore;
    }

    /**
     * Calculate frecency boost for a suggestion.
     *
     * Extracts important words and sums their frecency scores.
     *
     * @param string $text full text produced by applying the suggestion
     *
     * @return float Boost from 0-30
     */
    private function calculateFrecencyBoost(string $text): float
    {
        $words = WordExtractor::extractNormalizedIdentifiers($text);

        if (empty($words)) {
            return 0.0;
        }

        $totalScore = 0.0;
        foreach ($words as $word) {
            $totalScore += $this->frecencyIndex->getScore($word);
        }

        $avgScore = $totalScore / \count($words);

        return \min(30.0, $avgScore / 3.0);
    }

    /**
     * Check if buffer ends with object operator (->).
     */
    private function isAfterObjectOperator(string $buffer): bool
    {
        return (bool) \preg_match('/->\s*[a-zA-Z_]*$/', $buffer);
    }

    /**
     * Check if text looks like a function (ends with parentheses).
     */
    private function looksLikeFunction(string $text): bool
    {
        return (bool) \preg_match('/\([^)]*\)\s*$/', $text);
    }
}
