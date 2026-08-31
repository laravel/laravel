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
 * Static method completion source.
 *
 * Provides completions for static methods using reflection.
 * Handles Class::method(), self::method(), parent::method(), static::method().
 *
 * Suppresses magic methods until the user types `__`.
 */
class StaticMethodSource implements SourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & CompletionKind::STATIC_METHOD) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletions(AnalysisResult $analysis): array
    {
        if (empty($analysis->leftSideTypes)) {
            return [];
        }

        $includeMagic = \strpos($analysis->prefix, '__') === 0;

        $methods = [];
        foreach ($analysis->leftSideTypes as $type) {
            try {
                $reflection = new \ReflectionClass($type);
            } catch (\ReflectionException $e) {
                continue;
            }

            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->isStatic()) {
                    $methods[] = $method->getName();
                }
            }
        }

        $methods = \array_unique($methods);

        if (!$includeMagic) {
            $methods = \array_filter($methods, fn ($method) => \strpos($method, '__') !== 0);
        }

        return \array_values($methods);
    }
}
