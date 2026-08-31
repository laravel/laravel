<?php

namespace Illuminate\Container\Attributes;

use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class RouteParameter implements ContextualAttribute
{
    /**
     * Create a new class instance.
     */
    public function __construct(public string $parameter)
    {
    }

    /**
     * Resolve the route parameter.
     *
     * @param  self  $attribute
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return mixed
     */
    public static function resolve(self $attribute, Container $container)
    {
        return $container->make('request')->route($attribute->parameter);
    }
}
