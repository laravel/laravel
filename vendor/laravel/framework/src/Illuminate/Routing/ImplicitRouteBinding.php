<?php

namespace Illuminate\Routing;

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;

class ImplicitRouteBinding
{
    /**
     * Resolve the implicit route bindings for the given route.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Illuminate\Routing\Route  $route
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     * @throws \Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException
     */
    public static function resolveForRoute($container, $route)
    {
        $parameters = $route->parameters();

        $route = static::resolveBackedEnumsForRoute($route, $parameters);

        foreach ($route->signatureParameters(['subClass' => UrlRoutable::class]) as $parameter) {
            if (! $parameterName = static::getParameterName($parameter->getName(), $parameters)) {
                continue;
            }

            $parameterValue = $parameters[$parameterName];

            if ($parameterValue instanceof UrlRoutable) {
                continue;
            }

            $instance = $container->make(Reflector::getParameterClassName($parameter));

            $parent = $route->parentOfParameter($parameterName);

            $routeBindingMethod = $route->allowsTrashedBindings() && $instance::isSoftDeletable()
                ? 'resolveSoftDeletableRouteBinding'
                : 'resolveRouteBinding';

            if ($parent instanceof UrlRoutable &&
                ! $route->preventsScopedBindings() &&
                ($route->enforcesScopedBindings() || array_key_exists($parameterName, $route->bindingFields()))) {
                $childRouteBindingMethod = $route->allowsTrashedBindings() && $instance::isSoftDeletable()
                    ? 'resolveSoftDeletableChildRouteBinding'
                    : 'resolveChildRouteBinding';

                if (! $model = $parent->{$childRouteBindingMethod}(
                    $parameterName, $parameterValue, $route->bindingFieldFor($parameterName)
                )) {
                    throw (new ModelNotFoundException)->setModel(get_class($instance), [$parameterValue]);
                }
            } elseif (! $model = $instance->{$routeBindingMethod}($parameterValue, $route->bindingFieldFor($parameterName))) {
                throw (new ModelNotFoundException)->setModel(get_class($instance), [$parameterValue]);
            }

            $route->setParameter($parameterName, $model);
        }
    }

    /**
     * Resolve the Backed Enums route bindings for the route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  array  $parameters
     * @return \Illuminate\Routing\Route
     *
     * @throws \Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException
     */
    protected static function resolveBackedEnumsForRoute($route, $parameters)
    {
        foreach ($route->signatureParameters(['backedEnum' => true]) as $parameter) {
            if (! $parameterName = static::getParameterName($parameter->getName(), $parameters)) {
                continue;
            }

            $parameterValue = $parameters[$parameterName];

            if ($parameterValue === null) {
                continue;
            }

            $backedEnumClass = $parameter->getType()?->getName();

            $backedEnum = $parameterValue instanceof $backedEnumClass
                ? $parameterValue
                : $backedEnumClass::tryFrom((string) $parameterValue);

            if (is_null($backedEnum)) {
                throw new BackedEnumCaseNotFoundException($backedEnumClass, $parameterValue);
            }

            $route->setParameter($parameterName, $backedEnum);
        }

        return $route;
    }

    /**
     * Return the parameter name if it exists in the given parameters.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return string|null
     */
    protected static function getParameterName($name, $parameters)
    {
        if (array_key_exists($name, $parameters)) {
            return $name;
        }

        if (array_key_exists($snakedName = Str::snake($name), $parameters)) {
            return $snakedName;
        }
    }
}
