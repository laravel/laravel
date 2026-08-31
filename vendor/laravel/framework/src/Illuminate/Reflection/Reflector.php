<?php

namespace Illuminate\Support;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionEnum;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;

class Reflector
{
    /**
     * This is a PHP 7.4 compatible implementation of is_callable.
     *
     * @param  mixed  $var
     * @param  bool  $syntaxOnly
     * @return bool
     */
    public static function isCallable($var, $syntaxOnly = false)
    {
        if (! is_array($var)) {
            return is_callable($var, $syntaxOnly);
        }

        if (! isset($var[0], $var[1]) || ! is_string($var[1] ?? null)) {
            return false;
        }

        if ($syntaxOnly &&
            (is_string($var[0]) || is_object($var[0])) &&
            is_string($var[1])) {
            return true;
        }

        $class = is_object($var[0]) ? get_class($var[0]) : $var[0];

        $method = $var[1];

        if (! class_exists($class)) {
            return false;
        }

        if (method_exists($class, $method)) {
            return (new ReflectionMethod($class, $method))->isPublic();
        }

        if (is_object($var[0]) && method_exists($class, '__call')) {
            return (new ReflectionMethod($class, '__call'))->isPublic();
        }

        if (! is_object($var[0]) && method_exists($class, '__callStatic')) {
            return (new ReflectionMethod($class, '__callStatic'))->isPublic();
        }

        return false;
    }

    /**
     * Get the specified class attribute, optionally following an inheritance chain.
     *
     * @template TAttribute of object
     *
     * @param  object|class-string  $objectOrClass
     * @param  class-string<TAttribute>  $attribute
     * @param  bool  $ascend
     * @return TAttribute|null
     */
    public static function getClassAttribute($objectOrClass, $attribute, $ascend = false)
    {
        return static::getClassAttributes($objectOrClass, $attribute, $ascend)->flatten()->first();
    }

    /**
     * Get the specified class attribute(s), optionally following an inheritance chain.
     *
     * @template TTarget of object
     * @template TAttribute of object
     *
     * @param  TTarget|class-string<TTarget>  $objectOrClass
     * @param  class-string<TAttribute>  $attribute
     * @param  bool  $includeParents
     * @return ($includeParents is true ? Collection<class-string<contravariant TTarget>, Collection<int, TAttribute>> : Collection<int, TAttribute>)
     */
    public static function getClassAttributes($objectOrClass, $attribute, $includeParents = false)
    {
        $reflectionClass = new ReflectionClass($objectOrClass);

        $attributes = [];

        do {
            $attributes[$reflectionClass->name] = new Collection(array_map(
                fn (ReflectionAttribute $reflectionAttribute) => $reflectionAttribute->newInstance(),
                $reflectionClass->getAttributes($attribute)
            ));
        } while ($includeParents && false !== $reflectionClass = $reflectionClass->getParentClass());

        return $includeParents ? new Collection($attributes) : array_first($attributes);
    }

    /**
     * Get the class name of the given parameter's type, if possible.
     *
     * @param  \ReflectionParameter  $parameter
     * @return string|null
     */
    public static function getParameterClassName($parameter)
    {
        $type = $parameter->getType();

        if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return;
        }

        return static::getTypeName($parameter, $type);
    }

    /**
     * Get the class names of the given parameter's type, including union types.
     *
     * @param  \ReflectionParameter  $parameter
     * @return array
     */
    public static function getParameterClassNames($parameter)
    {
        $type = $parameter->getType();

        if (! $type instanceof ReflectionUnionType) {
            return array_filter([static::getParameterClassName($parameter)]);
        }

        $unionTypes = [];

        foreach ($type->getTypes() as $listedType) {
            if (! $listedType instanceof ReflectionNamedType || $listedType->isBuiltin()) {
                continue;
            }

            $unionTypes[] = static::getTypeName($parameter, $listedType);
        }

        return array_filter($unionTypes);
    }

    /**
     * Get the given type's class name.
     *
     * @param  \ReflectionParameter  $parameter
     * @param  \ReflectionNamedType  $type
     * @return string
     */
    protected static function getTypeName($parameter, $type)
    {
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
     * Determine if the parameter's type is a subclass of the given type.
     *
     * @param  \ReflectionParameter  $parameter
     * @param  string  $className
     * @return bool
     */
    public static function isParameterSubclassOf($parameter, $className)
    {
        $paramClassName = static::getParameterClassName($parameter);

        return $paramClassName
            && (class_exists($paramClassName) || interface_exists($paramClassName))
            && (new ReflectionClass($paramClassName))->isSubclassOf($className);
    }

    /**
     * Determine if the parameter's type is a Backed Enum with a string backing type.
     *
     * @param  \ReflectionParameter  $parameter
     * @return bool
     */
    public static function isParameterBackedEnumWithStringBackingType($parameter)
    {
        if (! $parameter->getType() instanceof ReflectionNamedType) {
            return false;
        }

        $backedEnumClass = $parameter->getType()?->getName();

        if (is_null($backedEnumClass)) {
            return false;
        }

        if (enum_exists($backedEnumClass)) {
            $reflectionBackedEnum = new ReflectionEnum($backedEnumClass);

            return $reflectionBackedEnum->isBacked()
                && $reflectionBackedEnum->getBackingType()->getName() == 'string';
        }

        return false;
    }
}
