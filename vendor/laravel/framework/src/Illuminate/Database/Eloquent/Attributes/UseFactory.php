<?php

namespace Illuminate\Database\Eloquent\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseFactory
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Factories\Factory>  $factoryClass
     */
    public function __construct(public string $factoryClass)
    {
    }
}
