<?php

namespace Illuminate\Container;

use Closure;
use Illuminate\Contracts\Container\ContextualAttribute;
use ReflectionAttribute;
use ReflectionNamedType;

/**
 * @internal
 */
class Util
{
    /**
     * If the given value is not an array and not null, wrap it in one.
     *
     * From Arr::wrap() in Illuminate\Support.
     *
     * @param  mixed  $value
     * @return array
     */
    public static function arrayWrap($value)
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }

    /**
     * Return the default value of the given value.
     *
     * From global value() helper in Illuminate\Support.
     *
     * @param  mixed  $value
     * @param  mixed  ...$args
     * @return mixed
     */
    public static function unwrapIfClosure($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }

    /**
     * Get the class name of the given parameter's type, if possible.
     *
     * From Reflector::getParameterClassName() in Illuminate\Support.
     *
     * @param  \ReflectionParameter  $parameter
     * @return string|null
     */
    public static function getParameterClassName($parameter)
    {
        $type = $parameter->getType();

        if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return null;
        }

        $name = $type->getName();

        if (! is_null($class = $parameter->getDeclaringClass())) {
            if ($name === 'self') {
                return $class->getName();
            }

            if ($name === 'parent' && $parent = $class->getParentClass()) {
                return $parent->getName();
            }
        }

        return $name;
    }

    /**
     * Get a contextual attribute from a dependency.
     *
     * @param  \ReflectionParameter  $dependency
     * @return \ReflectionAttribute|null
     */
    public static function getContextualAttributeFromDependency($dependency)
    {
        return $dependency->getAttributes(ContextualAttribute::class, ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
    }
}
