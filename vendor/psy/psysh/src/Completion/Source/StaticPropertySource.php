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
 * Static property completion source.
 *
 * Provides completions for static properties using reflection.
 * Handles Class::$property, self::$property, parent::$property, static::$property.
 */
class StaticPropertySource implements SourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & CompletionKind::STATIC_PROPERTY) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletions(AnalysisResult $analysis): array
    {
        if (empty($analysis->leftSideTypes)) {
            return [];
        }

        $allProperties = [];
        foreach ($analysis->leftSideTypes as $type) {
            $properties = $this->getStaticPropertiesFromClass($type);
            $allProperties = \array_merge($allProperties, $properties);
        }

        return \array_values(\array_unique($allProperties));
    }

    /**
     * Get static properties from a class using reflection.
     *
     * @return string[]
     */
    private function getStaticPropertiesFromClass(string $className): array
    {
        try {
            $reflection = new \ReflectionClass($className);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            $staticProperties = \array_filter($properties, fn ($p) => $p->isStatic());

            return \array_values(\array_map(fn ($p) => $p->getName(), $staticProperties));
        } catch (\ReflectionException $e) {
            return [];
        }
    }
}
