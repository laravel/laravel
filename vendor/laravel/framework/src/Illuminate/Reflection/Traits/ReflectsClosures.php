<?php

namespace Illuminate\Support\Traits;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Reflector;
use ReflectionFunction;
use ReflectionIntersectionType;
use ReflectionUnionType;
use RuntimeException;

trait ReflectsClosures
{
    /**
     * Get the class name of the first parameter of the given Closure.
     *
     * @param  \Closure  $closure
     * @return string
     *
     * @throws \ReflectionException
     * @throws \RuntimeException
     */
    protected function firstClosureParameterType(Closure $closure)
    {
        $types = array_values($this->closureParameterTypes($closure));

        if (! $types) {
            throw new RuntimeException('The given Closure has no parameters.');
        }

        if ($types[0] === null) {
            throw new RuntimeException('The first parameter of the given Closure is missing a type hint.');
        }

        return $types[0];
    }

    /**
     * Get the class names of the first parameter of the given Closure, including union types.
     *
     * @param  \Closure  $closure
     * @return array
     *
     * @throws \ReflectionException
     * @throws \RuntimeException
     */
    protected function firstClosureParameterTypes(Closure $closure)
    {
        $reflection = new ReflectionFunction($closure);

        $types = (new Collection($reflection->getParameters()))
            ->mapWithKeys(function ($parameter) {
                if ($parameter->isVariadic()) {
                    return [$parameter->getName() => null];
                }

                return [$parameter->getName() => Reflector::getParameterClassNames($parameter)];
            })
            ->filter()
            ->values()
            ->all();

        if (empty($types)) {
            throw new RuntimeException('The given Closure has no parameters.');
        }

        if (isset($types[0]) && empty($types[0])) {
            throw new RuntimeException('The first parameter of the given Closure is missing a type hint.');
        }

        return $types[0];
    }

    /**
     * Get the class names / types of the parameters of the given Closure.
     *
     * @param  \Closure  $closure
     * @return array
     *
     * @throws \ReflectionException
     */
    protected function closureParameterTypes(Closure $closure)
    {
        $reflection = new ReflectionFunction($closure);

        return (new Collection($reflection->getParameters()))
            ->mapWithKeys(function ($parameter) {
                if ($parameter->isVariadic()) {
                    return [$parameter->getName() => null];
                }

                return [$parameter->getName() => Reflector::getParameterClassName($parameter)];
            })
            ->all();
    }

    /**
     * Get the class names / types of the return type of the given Closure.
     *
     * @param  \Closure  $closure
     * @return list<class-string>
     *
     * @throws \ReflectionException
     */
    protected function closureReturnTypes(Closure $closure)
    {
        $reflection = new ReflectionFunction($closure);

        if ($reflection->getReturnType() === null ||
            $reflection->getReturnType() instanceof ReflectionIntersectionType) {
            return [];
        }

        $types = $reflection->getReturnType() instanceof ReflectionUnionType
            ? $reflection->getReturnType()->getTypes()
            : [$reflection->getReturnType()];

        return (new Collection($types))
            ->reject(fn ($type) => $type->isBuiltin())
            ->reject(fn ($type) => in_array($type->getName(), ['static', 'self']))
            ->map(fn ($type) => $type->getName())
            ->values()
            ->all();
    }
}
