<?php

namespace Illuminate\Database\Eloquent\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseResourceCollection
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<*>  $class
     */
    public function __construct(public string $class)
    {
    }
}
