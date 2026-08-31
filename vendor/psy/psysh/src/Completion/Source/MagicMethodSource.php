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
 * Magic method completion source.
 *
 * Provides completions for magic methods declared via @method docblock tags.
 */
class MagicMethodSource implements SourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & (CompletionKind::OBJECT_METHOD | CompletionKind::STATIC_METHOD)) !== 0;
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

        $staticOnly = ($analysis->kinds & CompletionKind::STATIC_METHOD) !== 0
            && ($analysis->kinds & CompletionKind::OBJECT_METHOD) === 0;

        $methods = [];
        foreach ($types as $type) {
            try {
                $reflection = new \ReflectionClass($type);
            } catch (\ReflectionException $e) {
                continue;
            }

            foreach (Docblock::getMagicMethods($reflection) as $method) {
                if ($staticOnly && !$method->isStatic()) {
                    continue;
                }

                $methods[] = $method->getName();
            }
        }

        return \array_values(\array_unique($methods));
    }
}
