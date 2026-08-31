<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Suggestion\Source;

use Psy\Completion\CompletionEngine;
use Psy\Completion\CompletionRequest;
use Psy\Readline\Interactive\Helper\CurrentWord;
use Psy\Readline\Interactive\Suggestion\SuggestionResult;

/**
 * Completion-based suggestion source.
 *
 * Uses the completion engine to provide context-aware suggestions.
 */
class ContextAwareSource implements SourceInterface
{
    private CompletionEngine $completer;

    public function __construct(CompletionEngine $completer)
    {
        $this->completer = $completer;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuggestion(string $buffer, int $cursorPosition): ?SuggestionResult
    {
        $completions = $this->completer->getCompletions(
            new CompletionRequest($buffer, $cursorPosition, CompletionRequest::MODE_SUGGESTION)
        );

        if (empty($completions)) {
            return null;
        }

        $completion = $completions[0];
        $currentWord = CurrentWord::extract($buffer, $cursorPosition);

        if ($currentWord !== '' && \stripos($completion, $currentWord) === 0) {
            $suffix = \substr($completion, \strlen($currentWord));
        } else {
            $suffix = $completion;
        }

        return SuggestionResult::forAppend(
            $suffix,
            SuggestionResult::SOURCE_CONTEXT_AWARE,
            $cursorPosition
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        // High priority, but lower than history (which should be 100)
        // We want context-aware to kick in when history doesn't match
        return 90;
    }
}
