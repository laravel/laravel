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
use Psy\Util\Docblock;

/**
 * Magic property completion source.
 *
 * Provides completions for magic properties declared via docblock tags.
 */
class MagicPropertySource implements SourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & (CompletionKind::OBJECT_PROPERTY | CompletionKind::STATIC_PROPERTY)) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletions(AnalysisResult $analysis): array
    {
        $types = $analysis->leftSideTypes;

        if (empty($types) && $analysis->leftSideValue !== null && \is_object($analysis->leftSideValue)) {
            $types = [\get_class($analysis->leftSideValue)];
        }

        if (empty($types)) {
            return [];
        }

        $properties = [];
        foreach ($types as $type) {
            try {
                $reflection = new \ReflectionClass($type);
            } catch (\ReflectionException $e) {
                continue;
            }

            foreach (Docblock::getMagicProperties($reflection) as $property) {
                $properties[] = $property->getName();
            }
        }

        return \array_values(\array_unique($properties));
    }
}
