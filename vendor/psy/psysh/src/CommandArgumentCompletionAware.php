<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy;

use Psy\Completion\AnalysisResult;

/**
 * Interface for commands that provide positional argument completion.
 */
interface CommandArgumentCompletionAware
{
    /**
     * Whether this command owns completion for the current argument context.
     */
    public function supportsArgumentCompletion(AnalysisResult $analysis): bool;

    /**
     * Return completion candidates for the current command-tail context.
     *
     * @return string[]
     */
    public function getArgumentCompletions(AnalysisResult $analysis): array;
}
