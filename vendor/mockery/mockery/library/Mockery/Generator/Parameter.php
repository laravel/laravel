<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use Mockery\Reflector;
use ReflectionClass;
use ReflectionParameter;
use function class_exists;

/**
 * @mixin ReflectionParameter
 */
class Parameter
{
    /**
     * @var int
     */
    private static $parameterCounter = 0;

    /**
     * @var ReflectionParameter
     */
    private $rfp;

    public function __construct(ReflectionParameter $rfp)
    {
        $this->rfp = $rfp;
    }

    /**
     * Proxy all method calls to the reflection parameter.
     *
     * @template TMixed
     * @template TResult
     *
     * @param string        $method
     * @param array<TMixed> $args
     *
     * @return TResult
     */
    public function __call($method, array $args)
    {
        /** @var TResult */
        return $this->rfp->{$method}(...$args);
    }

    /**
     * Get the reflection class for the parameter type, if it exists.
     *
     * This will be null if there was no type, or it was a scalar or a union.
     *
     * @return null|ReflectionClass
     *
     * @deprecated since 1.3.3 and will be removed in 2.0.
     */
    public function getClass()
    {
        $typeHint = Reflector::getTypeHint($this->rfp, true);

        return class_exists($typeHint) ? DefinedTargetClass::factory($typeHint, false) : null;
    }

    /**
     * Get the name of the parameter.
     *
     * Some internal classes have funny looking definitions!
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->rfp->getName();

        if (! $name || $name === '...') {
            return 'arg' . self::$parameterCounter++;
        }

        return $name;
    }

    /**
     * Get the string representation for the paramater type.
     *
     * @return null|string
     */
    public function getTypeHint()
    {
        return Reflector::getTypeHint($this->rfp);
    }

    /**
     * Get the string representation for the paramater type.
     *
     * @return string
     *
     * @deprecated since 1.3.2 and will be removed in 2.0. Use getTypeHint() instead.
     */
    public function getTypeHintAsString()
    {
        return (string) Reflector::getTypeHint($this->rfp, true);
    }

    /**
     * Determine if the parameter is an array.
     *
     * @return bool
     */
    public function isArray()
    {
        return Reflector::isArray($this->rfp);
    }

    /**
     * Determine if the parameter is variadic.
     *
     * @return bool
     */
    public function isVariadic()
    {
        return $this->rfp->isVariadic();
    }
}
