<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Util;

use Psy\Exception\RuntimeException;
use Psy\Reflection\ReflectionConstant;
use Psy\Reflection\ReflectionNamespace;

/**
 * A utility class for getting Reflectors.
 */
class Mirror
{
    const CONSTANT = 1;
    const METHOD = 2;
    const STATIC_PROPERTY = 4;
    const PROPERTY = 8;

    /**
     * Get a Reflector for a function, class or instance, constant, method or property.
     *
     * Optionally, pass a $filter param to restrict the types of members checked. For example, to only Reflectors for
     * static properties and constants, pass:
     *
     *    $filter = Mirror::CONSTANT | Mirror::STATIC_PROPERTY
     *
     * @throws \Psy\Exception\RuntimeException when a $member specified but not present on $value
     * @throws \InvalidArgumentException       if $value is something other than an object or class/function name
     *
     * @param mixed       $value  Class or function name, or variable instance
     * @param string|null $member Optional: property, constant or method name (default: null)
     * @param int         $filter (default: CONSTANT | METHOD | PROPERTY | STATIC_PROPERTY)
     *
     * @return \Reflector
     */
    public static function get($value, ?string $member = null, int $filter = 15): \Reflector
    {
        if ($member === null && \is_string($value)) {
            if (\function_exists($value)) {
                return new \ReflectionFunction($value);
            } elseif (\defined($value) || ReflectionConstant::isMagicConstant($value)) {
                return new ReflectionConstant($value);
            }
        }

        $class = self::getClass($value);

        if ($member === null) {
            return $class;
        } elseif ($filter & self::CONSTANT && $class->hasConstant($member)) {
            return new \ReflectionClassConstant($value, $member);
        } elseif ($filter & self::METHOD && $class->hasMethod($member)) {
            return $class->getMethod($member);
        } elseif ($filter & self::PROPERTY && $class->hasProperty($member)) {
            return $class->getProperty($member);
        } elseif ($filter & self::STATIC_PROPERTY && $class->hasProperty($member) && $class->getProperty($member)->isStatic()) {
            return $class->getProperty($member);
        } else {
            throw new RuntimeException(\sprintf('Unknown member %s on class %s', $member, \is_object($value) ? \get_class($value) : $value));
        }
    }

    /**
     * Get a ReflectionClass (or ReflectionObject, or ReflectionNamespace) if possible.
     *
     * @throws \InvalidArgumentException if $value is not a namespace or class name or instance
     *
     * @param mixed $value
     *
     * @return \ReflectionClass|ReflectionNamespace
     */
    private static function getClass($value)
    {
        if (\is_object($value)) {
            return new \ReflectionObject($value);
        }

        if (!\is_string($value)) {
            throw new \InvalidArgumentException('Mirror expects an object or class');
        }

        if (\class_exists($value) || \interface_exists($value) || \trait_exists($value)) {
            return new \ReflectionClass($value);
        }

        $namespace = \preg_replace('/(^\\\\|\\\\$)/', '', $value);
        if (self::namespaceExists($namespace)) {
            return new ReflectionNamespace($namespace);
        }

        throw new \InvalidArgumentException('Unknown namespace, class or function: '.$value);
    }

    /**
     * Check declared namespaces for a given namespace.
     */
    private static function namespaceExists(string $value): bool
    {
        return \in_array(\strtolower($value), self::getDeclaredNamespaces());
    }

    /**
     * Get an array of all currently declared namespaces.
     *
     * Note that this relies on at least one function, class, interface, trait
     * or constant to have been declared in that namespace.
     */
    private static function getDeclaredNamespaces(): array
    {
        $functions = \get_defined_functions();

        $allNames = \array_merge(
            $functions['internal'],
            $functions['user'],
            \get_declared_classes(),
            \get_declared_interfaces(),
            \get_declared_traits(),
            \array_keys(\get_defined_constants())
        );

        $namespaces = [];
        foreach ($allNames as $name) {
            $chunks = \explode('\\', \strtolower($name));

            // the last one is the function or class or whatever...
            \array_pop($chunks);

            while (!empty($chunks)) {
                $namespaces[\implode('\\', $chunks)] = true;
                \array_pop($chunks);
            }
        }

        $namespaceNames = \array_keys($namespaces);

        \sort($namespaceNames);

        return $namespaceNames;
    }
}
