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

/**
 * Completion source interface.
 *
 * Sources provide completion candidates for specific contexts (variables,
 * methods, classes, etc.). The CompletionEngine applies fuzzy matching to
 * the candidates returned by sources.
 *
 * Filtering Strategies:
 *
 * Sources use different strategies based on their candidate set size:
 *
 * Small sources (commands, variables, keywords):
 *   - Return ALL candidates regardless of prefix
 *   - CompletionEngine handles all filtering
 *
 * Large sources (classes, functions, constants):
 *   - If prefix matches ≥10 candidates: return prefix-filtered subset
 *   - Otherwise: return ALL candidates for fuzzy matching
 *   - Prevents overwhelming fuzzy matcher with huge sets
 *
 * Both strategies ensure fuzzy matching is applied consistently by the engine.
 */
interface SourceInterface
{
    /**
     * Check if this source applies to the given completion kinds.
     *
     * Uses bitwise AND to check if any of the kinds in the bitmask match.
     *
     * @param int $kinds Bitmask of CompletionKind constants
     */
    public function appliesToKind(int $kinds): bool;

    /**
     * Get completion candidates for the analyzed input.
     *
     * Sources may use analysis->prefix for optimization but are not required
     * to filter by it. CompletionEngine applies fuzzy matching to all results.
     *
     * @return string[] Array of completion strings
     */
    public function getCompletions(AnalysisResult $analysis): array;
}
