<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion\Refiner;

use Psy\Completion\AnalysisResult;
use Psy\Completion\CompletionKind;

/**
 * Recognizes shell-shaped command input before generic code sources run.
 *
 * It classifies bare command heads and option tokens so the rest of the
 * pipeline can treat shell commands as their own completion mode.
 */
class CommandSyntaxRefiner implements AnalysisRefinerInterface
{
    /**
     * {@inheritdoc}
     */
    public function refine(AnalysisResult $analysis): AnalysisResult
    {
        if (($analysis->kinds & CompletionKind::COMMAND_ELIGIBLE) === 0) {
            return $analysis;
        }

        $trimmed = \rtrim($analysis->input);

        if (\preg_match('/^([a-z][a-z0-9-]*)\s+.*?(-{1,2}[\w-]*)$/', $trimmed, $matches)) {
            return $analysis->withContext(CompletionKind::COMMAND_OPTION, $matches[2], $matches[1]);
        }

        if ($analysis->input !== $trimmed) {
            return $analysis;
        }

        if (!\preg_match('/^([a-z][a-z0-9-]*)$/', $trimmed, $matches)) {
            return $analysis;
        }

        return $analysis->withContext(
            CompletionKind::COMMAND | CompletionKind::KEYWORD | CompletionKind::SYMBOL,
            $matches[1]
        );
    }
}
