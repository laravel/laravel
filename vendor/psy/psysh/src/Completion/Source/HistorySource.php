<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion\Source;

use Psy\Completion\AnalysisResult;
use Psy\Completion\CompletionKind;
use Psy\Readline\Interactive\Input\History;

/**
 * History-based completion source.
 *
 * Provides completion candidates from command history at the start of input.
 */
class HistorySource implements SourceInterface
{
    private History $history;

    public function __construct(History $history)
    {
        $this->history = $history;
    }

    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & CompletionKind::COMMAND) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletions(AnalysisResult $analysis): array
    {
        if ($analysis->prefix === '') {
            return [];
        }

        // Newest first, deduplicated
        $commands = $this->history->search('', false);

        return \array_values(\array_unique($commands));
    }
}
