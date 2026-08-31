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

/**
 * A completion source for object methods.
 *
 * Provides method completions when completing after `->` operator.
 */
class MethodSource implements SourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & CompletionKind::OBJECT_METHOD) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletions(AnalysisResult $analysis): array
    {
        if (empty($analysis->leftSideTypes)) {
            return [];
        }

        $allCompletions = [];

        foreach ($analysis->leftSideTypes as $type) {
            try {
                $reflection = new \ReflectionClass($type);
            } catch (\ReflectionException $e) {
                continue;
            }

            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                $allCompletions[] = $method->getName();
            }
        }

        // Return all methods (fuzzy matching in CompletionEngine will filter)
        $allCompletions = \array_unique($allCompletions);
        \sort($allCompletions);

        return $allCompletions;
    }
}
