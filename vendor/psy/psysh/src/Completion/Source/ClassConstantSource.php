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
 * Class constant completion source.
 *
 * Provides completions for class constants using reflection.
 * Handles Class::CONSTANT, self::CONSTANT, parent::CONSTANT, static::CONSTANT.
 */
class ClassConstantSource implements SourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & CompletionKind::CLASS_CONSTANT) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletions(AnalysisResult $analysis): array
    {
        if (empty($analysis->leftSideTypes)) {
            return [];
        }

        $constants = [];
        foreach ($analysis->leftSideTypes as $type) {
            try {
                $reflection = new \ReflectionClass($type);
            } catch (\ReflectionException $e) {
                continue;
            }

            foreach ($reflection->getReflectionConstants() as $constant) {
                if ($constant->isPublic()) {
                    $constants[] = $constant->getName();
                }
            }
        }

        return \array_values(\array_unique($constants));
    }
}
