<?php

namespace Illuminate\Container\Attributes;

use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;
use UnitEnum;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Database implements ContextualAttribute
{
    /**
     * Create a new class instance.
     */
    public function __construct(public UnitEnum|string|null $connection = null)
    {
    }

    /**
     * Resolve the database connection.
     *
     * @param  self  $attribute
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return \Illuminate\Database\Connection
     */
    public static function resolve(self $attribute, Container $container)
    {
        return $container->make('db')->connection($attribute->connection);
    }
}
