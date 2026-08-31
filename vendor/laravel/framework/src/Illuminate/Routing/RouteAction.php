<?php

namespace Illuminate\Routing;

use Illuminate\Support\Arr;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use LogicException;
use UnexpectedValueException;

class RouteAction
{
    /**
     * Parse the given action into an array.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return array
     */
    public static function parse($uri, $action)
    {
        // If no action is passed in right away, we assume the user will make use of
        // fluent routing. In that case, we set a default closure, to be executed
        // if the user never explicitly sets an action to handle the given uri.
        if (is_null($action)) {
            return static::missingAction($uri);
        }

        // If the action is already a Closure instance, we will just set that instance
        // as the "uses" property, because there is nothing else we need to do when
        // it is available. Otherwise we will need to find it in the action list.
        if (Reflector::isCallable($action, true)) {
            return ! is_array($action) ? ['uses' => $action] : [
                'uses' => $action[0].'@'.$action[1],
                'controller' => $action[0].'@'.$action[1],
            ];
        }

        // If no "uses" property has been set, we will dig through the array to find a
        // Closure instance within this list. We will set the first Closure we come
        // across into the "uses" property that will get fired off by this route.
        elseif (! isset($action['uses'])) {
            $action['uses'] = static::findCallable($action);
        }

        if (! static::containsSerializedClosure($action) && is_string($action['uses']) && ! str_contains($action['uses'], '@')) {
            $action['uses'] = static::makeInvokable($action['uses']);
        }

        return $action;
    }

    /**
     * Get an action for a route that has no action.
     *
     * @param  string  $uri
     * @return array
     *
     * @throws \LogicException
     */
    protected static function missingAction($uri)
    {
        return ['uses' => function () use ($uri) {
            throw new LogicException("Route for [{$uri}] has no action.");
        }];
    }

    /**
     * Find the callable in an action array.
     *
     * @param  array  $action
     * @return callable
     */
    protected static function findCallable(array $action)
    {
        return Arr::first($action, function ($value, $key) {
            return Reflector::isCallable($value) && is_numeric($key);
        });
    }

    /**
     * Make an action for an invokable controller.
     *
     * @param  string  $action
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    protected static function makeInvokable($action)
    {
        if (! method_exists($action, '__invoke')) {
            throw new UnexpectedValueException("Invalid route action: [{$action}].");
        }

        return $action.'@__invoke';
    }

    /**
     * Determine if the given array actions contain a serialized Closure.
     *
     * @param  array  $action
     * @return bool
     */
    public static function containsSerializedClosure(array $action)
    {
        return is_string($action['uses']) && Str::startsWith($action['uses'], [
            'O:47:"Laravel\\SerializableClosure\\SerializableClosure',
            'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure',
        ]) !== false;
    }
}
