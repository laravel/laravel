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
 * Object method completion source.
 *
 * Provides completions for methods of objects using runtime reflection.
 * Prefers actual object instances when available, falls back to static
 * reflection on the class type.
 *
 * Suppresses magic methods until the user types `__`.
 */
class ObjectMethodSource implements SourceInterface
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
        $includeMagic = \strpos($analysis->prefix, '__') === 0;

        // Prefer runtime value if available
        if ($analysis->leftSideValue !== null && \is_object($analysis->leftSideValue)) {
            return $this->filterMethods(\get_class_methods($analysis->leftSideValue), $includeMagic);
        }

        if (empty($analysis->leftSideTypes)) {
            return [];
        }

        $methods = [];
        foreach ($analysis->leftSideTypes as $type) {
            try {
                $reflection = new \ReflectionClass($type);
            } catch (\ReflectionException $e) {
                continue;
            }

            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $methods[] = $method->getName();
            }
        }

        return $this->filterMethods(\array_unique($methods), $includeMagic);
    }

    /**
     * Filter method names, hiding magic methods unless explicitly requested.
     *
     * @param string[] $methods
     *
     * @return string[]
     */
    private function filterMethods(array $methods, bool $includeMagic): array
    {
        if (!$includeMagic) {
            $methods = \array_filter($methods, fn ($method) => \strpos($method, '__') !== 0);
        }

        return \array_values($methods);
    }
}
