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
 * Object property completion source.
 *
 * Provides completions for properties of objects using runtime reflection.
 * Prefers actual object instances when available, falls back to static
 * reflection on the class type.
 */
class ObjectPropertySource implements SourceInterface
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
        if ($analysis->leftSideValue !== null && \is_object($analysis->leftSideValue)) {
            return \array_keys(\get_object_vars($analysis->leftSideValue));
        }

        if (empty($analysis->leftSideTypes)) {
            return [];
        }

        $properties = [];
        foreach ($analysis->leftSideTypes as $type) {
            try {
                $reflection = new \ReflectionClass($type);
            } catch (\ReflectionException $e) {
                continue;
            }

            foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                $properties[] = $property->getName();
            }
        }

        return \array_values(\array_unique($properties));
    }
}
