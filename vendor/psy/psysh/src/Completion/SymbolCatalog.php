<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion;

/**
 * Cached runtime symbol catalog.
 *
 * Snapshot data is refreshed when declared symbol counts change.
 */
class SymbolCatalog
{
    private string $version = '';
    /** @var string[][] Cached symbol lists, keyed by catalog method */
    private array $snapshot = [];

    /**
     * Get a version identifier for cache invalidation.
     */
    public function getVersion(): string
    {
        $this->refreshIfNeeded();

        return $this->version;
    }

    /**
     * @return string[]
     */
    public function getClasses(): array
    {
        $this->refreshIfNeeded();

        return $this->snapshot['classes'];
    }

    /**
     * @return string[]
     */
    public function getInterfaces(): array
    {
        $this->refreshIfNeeded();

        return $this->snapshot['interfaces'];
    }

    /**
     * @return string[]
     */
    public function getTraits(): array
    {
        $this->refreshIfNeeded();

        return $this->snapshot['traits'];
    }

    /**
     * @return string[]
     */
    public function getFunctions(): array
    {
        $this->refreshIfNeeded();

        return $this->snapshot['functions'];
    }

    /**
     * @return string[]
     */
    public function getConstants(): array
    {
        $this->refreshIfNeeded();

        return $this->snapshot['constants'];
    }

    /**
     * @return string[]
     */
    public function getNamespaces(): array
    {
        $this->refreshIfNeeded();

        return $this->snapshot['namespaces'];
    }

    /**
     * @return string[]
     */
    public function getAttributeClasses(): array
    {
        $this->refreshIfNeeded();

        return $this->snapshot['attributes'];
    }

    private function refreshIfNeeded(): void
    {
        $classes = \get_declared_classes();
        $interfaces = \get_declared_interfaces();
        $traits = \get_declared_traits();
        $functions = \get_defined_functions();
        $constants = \array_keys(\get_defined_constants());

        $version = \implode(':', [
            \count($classes),
            \count($interfaces),
            \count($traits),
            \count($functions['user']),
            \count($functions['internal']),
            \count($constants),
        ]);

        if ($this->version === $version && !empty($this->snapshot)) {
            return;
        }

        $allFunctions = \array_merge($functions['user'], $functions['internal']);
        \sort($classes);
        \sort($interfaces);
        \sort($traits);
        \sort($allFunctions);
        \sort($constants);

        $namespaces = $this->extractNamespaces($classes, $interfaces, $traits);
        \sort($namespaces);

        $attributes = $this->extractAttributes($classes);
        \sort($attributes);

        $this->snapshot = [
            'classes'    => $classes,
            'interfaces' => $interfaces,
            'traits'     => $traits,
            'functions'  => $allFunctions,
            'constants'  => $constants,
            'namespaces' => $namespaces,
            'attributes' => $attributes,
        ];
        $this->version = $version;
    }

    /**
     * @param string[] $classes
     * @param string[] $interfaces
     * @param string[] $traits
     *
     * @return string[]
     */
    private function extractNamespaces(array $classes, array $interfaces, array $traits): array
    {
        $classLikes = \array_merge($classes, $interfaces, $traits);
        $namespaces = [];

        foreach ($classLikes as $className) {
            $parts = \explode('\\', $className);
            if (\count($parts) < 2) {
                continue;
            }

            \array_pop($parts);

            $namespace = '';
            foreach ($parts as $part) {
                if ($namespace !== '') {
                    $namespace .= '\\';
                }
                $namespace .= $part;
                $namespaces[$namespace] = true;
            }
        }

        return \array_keys($namespaces);
    }

    /**
     * @param string[] $classes
     *
     * @return string[]
     */
    private function extractAttributes(array $classes): array
    {
        if (\PHP_VERSION_ID < 80000 || !\class_exists('Attribute', false)) {
            return [];
        }

        $attributes = [];
        foreach ($classes as $className) {
            try {
                $reflection = new \ReflectionClass($className);
                if (!\method_exists($reflection, 'getAttributes')) {
                    continue;
                }

                // @phan-suppress-next-line PhanUndeclaredMethod - available on PHP 8+, guarded by method_exists
                $classAttributes = $reflection->getAttributes('Attribute');
                if (!empty($classAttributes)) {
                    $attributes[] = $className;
                }
            } catch (\ReflectionException $e) {
                // Skip classes that cannot be reflected.
            }
        }

        return $attributes;
    }
}
