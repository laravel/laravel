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
use Psy\Readline\Interactive\Suggestion\Source\CallSignatureSource;
use Psy\Readline\Interactive\Suggestion\Source\HistorySource;
use Psy\Readline\Interactive\Suggestion\Source\SourceInterface;

/**
 * Suggestion engine that coordinates multiple suggestion sources.
 *
 * Manages caching and selects the best suggestion from available sources.
 */
class SuggestionEngine
{
    /** @var SourceInterface[] */
    private array $sources = [];

    private History $history;
    private ?SuggestionResult $lastSuggestion = null;
    private string $lastBuffer = '';
    private int $lastCursorPosition = -1;
    private int $lastHistoryRevision = -1;
    private SuggestionFilter $filter;
    private bool $frecencyInitialized = false;

    public function __construct(History $history)
    {
        $this->history = $history;

        $this->sources = [
            new CallSignatureSource(),
            new HistorySource($history),
        ];

        $this->sortSources();

        $this->filter = new SuggestionFilter();

        DebugLog::log('SuggestionEngine', 'INIT', ['sources' => \count($this->sources)]);
    }

    /**
     * Get suggestion for current buffer state.
     *
     * Only suggests when cursor is at end of buffer.
     *
     * @param string $buffer         Current input buffer
     * @param int    $cursorPosition Current cursor position
     *
     * @return SuggestionResult|null
     */
    public function getSuggestion(string $buffer, int $cursorPosition): ?SuggestionResult
    {
        // @todo should this be multi-line aware?
        if ($cursorPosition !== \mb_strlen($buffer)) {
            DebugLog::log('Suggestion', 'SKIP', ['reason' => 'cursor_not_at_end', 'pos' => $cursorPosition, 'len' => \mb_strlen($buffer)]);

            return null;
        }

        if (\trim($buffer) === '') {
            DebugLog::log('Suggestion', 'SKIP', ['reason' => 'empty_buffer']);

            return null;
        }

        $historyRevision = $this->history->getRevision();

        if ($historyRevision !== $this->lastHistoryRevision) {
            $this->frecencyInitialized = false;
        }

        if ($buffer === $this->lastBuffer &&
            $cursorPosition === $this->lastCursorPosition &&
            $historyRevision === $this->lastHistoryRevision &&
            $this->lastSuggestion !== null) {
            DebugLog::log('Suggestion', 'CACHE_HIT', ['text' => $this->lastSuggestion->getDisplayText(), 'source' => $this->lastSuggestion->getSource()]);

            return $this->lastSuggestion;
        }

        $candidates = [];
        foreach ($this->sources as $source) {
            $suggestion = $source->getSuggestion($buffer, $cursorPosition);
            if ($suggestion !== null && $this->filter->isValid($buffer, $suggestion->getDisplayText())) {
                $candidates[] = $suggestion;
                DebugLog::log('Suggestion', 'CANDIDATE', ['source' => $suggestion->getSource(), 'text' => $suggestion->getDisplayText()]);
            }
        }

        $best = null;
        if (!empty($candidates)) {
            $this->ensureFrecencyIndex();
            DebugLog::log('SuggestionEngine', 'SCORING', ['candidates' => \count($candidates)]);
            $best = $this->selectBestCandidate($buffer, $candidates);
            DebugLog::log('Suggestion', 'SHOW', ['text' => $best->getDisplayText(), 'source' => $best->getSource(), 'buffer' => $buffer]);
        } else {
            DebugLog::log('Suggestion', 'NONE', ['buffer' => $buffer]);
        }

        $this->lastBuffer = $buffer;
        $this->lastCursorPosition = $cursorPosition;
        $this->lastHistoryRevision = $historyRevision;
        $this->lastSuggestion = $best;

        return $best;
    }

    /**
     * Clear cached suggestion.
     */
    public function clearCache(): void
    {
        if ($this->lastSuggestion !== null) {
            DebugLog::log('Suggestion', 'CACHE_CLEAR', ['had_suggestion' => true]);
        }

        $this->lastSuggestion = null;
        $this->lastBuffer = '';
        $this->lastCursorPosition = -1;
        $this->lastHistoryRevision = -1;
    }

    /**
     * Add a suggestion source.
     */
    public function addSource(SourceInterface $source): void
    {
        $this->sources[] = $source;
        $this->sortSources();
        $this->clearCache();
    }

    /**
     * Sort sources by priority, highest first.
     */
    private function sortSources(): void
    {
        \usort($this->sources, fn (SourceInterface $a, SourceInterface $b) => $b->getPriority() <=> $a->getPriority());
    }

    /**
     * Build and attach the frecency index on first scoring operation.
     */
    private function ensureFrecencyIndex(): void
    {
        if ($this->frecencyInitialized) {
            return;
        }

        $startedAt = \microtime(true);
        $frecencyIndex = new FrecencyIndex($this->history);
        $this->filter->setFrecencyIndex($frecencyIndex);
        $this->frecencyInitialized = true;

        DebugLog::log('SuggestionEngine', 'FRECENCY_READY', [
            'words' => \count($frecencyIndex->getAllScores()),
            'ms'    => \sprintf('%.1f', (\microtime(true) - $startedAt) * 1000),
        ]);
    }

    /**
     * Score candidates once and return the best one.
     *
     * Keeps source-order as a deterministic tie-breaker.
     *
     * @param SuggestionResult[] $candidates
     */
    private function selectBestCandidate(string $buffer, array $candidates): SuggestionResult
    {
        $scored = [];
        foreach ($candidates as $index => $candidate) {
            $scored[] = [
                'suggestion' => $candidate,
                'score'      => $this->filter->score($buffer, $candidate),
                'index'      => $index,
            ];
        }

        \usort($scored, static function (array $a, array $b): int {
            if ($a['score'] !== $b['score']) {
                return $b['score'] <=> $a['score'];
            }

            return $a['index'] <=> $b['index'];
        });

        return $scored[0]['suggestion'];
    }
}
