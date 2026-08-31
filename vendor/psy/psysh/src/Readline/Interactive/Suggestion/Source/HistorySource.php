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

use Psy\Readline\Interactive\Input\History;
use Psy\Readline\Interactive\Suggestion\SuggestionResult;

/**
 * Provides suggestions from command history.
 *
 * Searches history for commands that start with the current buffer.
 * Returns the most recent matching command.
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
    public function getSuggestion(string $buffer, int $cursorPosition): ?SuggestionResult
    {
        // Search history most-recent first.
        $entries = $this->history->getAll();
        for ($i = \count($entries) - 1; $i >= 0; $i--) {
            $entry = $entries[$i];
            $line = $entry['command'];

            if (\strpos($line, $buffer) === 0 && $line !== $buffer) {
                $acceptText = \substr($line, \strlen($buffer));
                $displayText = $acceptText;
                if ($entry['lines'] > 1) {
                    $displayText = History::collapseToSingleLine($acceptText);
                }

                return SuggestionResult::forAppend(
                    $displayText,
                    SuggestionResult::SOURCE_HISTORY,
                    $cursorPosition,
                    $acceptText
                );
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 100;
    }
}
