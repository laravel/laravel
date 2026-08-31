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

use Psy\Readline\Interactive\Suggestion\SuggestionResult;

/**
 * Interface for suggestion sources.
 *
 * Sources provide suggestions based on different strategies (history, completion, etc.).
 */
interface SourceInterface
{
    /**
     * Get suggestion for the given buffer.
     *
     * The engine calls sources only for non-empty buffers; callers invoking
     * sources directly should apply the same precondition.
     */
    public function getSuggestion(string $buffer, int $cursorPosition): ?SuggestionResult;

    /**
     * Get priority of this source (higher = checked first).
     */
    public function getPriority(): int;
}
