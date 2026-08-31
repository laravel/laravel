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
 * A completion source for object properties.
 *
 * Provides property completions when completing after `->` operator.
 */
class PropertySource implements SourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & CompletionKind::OBJECT_PROPERTY) !== 0;
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

            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

            foreach ($properties as $property) {
                $allCompletions[] = $property->getName();
            }
        }

        // Return all properties (fuzzy matching in CompletionEngine will filter)
        $allCompletions = \array_unique($allCompletions);
        \sort($allCompletions);

        return $allCompletions;
    }
}
