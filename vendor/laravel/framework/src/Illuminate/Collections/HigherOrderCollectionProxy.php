<?php

namespace Illuminate\Support;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @mixin \Illuminate\Support\Enumerable<TKey, TValue>
 * @mixin TValue
 */
class HigherOrderCollectionProxy
{
    /**
     * The collection being operated on.
     *
     * @var \Illuminate\Support\Enumerable<TKey, TValue>
     */
    protected $collection;

    /**
     * The method being proxied.
     *
     * @var string
     */
    protected $method;

    /**
     * Create a new proxy instance.
     *
     * @param  \Illuminate\Support\Enumerable<TKey, TValue>  $collection
     * @param  string  $method
     */
    public function __construct(Enumerable $collection, $method)
    {
        $this->method = $method;
        $this->collection = $collection;
    }

    /**
     * Proxy accessing an attribute onto the collection items.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->collection->{$this->method}(function ($value) use ($key) {
            return is_array($value) ? $value[$key] : $value->{$key};
        });
    }

    /**
     * Proxy a method call onto the collection items.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->collection->{$this->method}(function ($value) use ($method, $parameters) {
            return is_string($value)
                ? $value::{$method}(...$parameters)
                : $value->{$method}(...$parameters);
        });
    }
}
