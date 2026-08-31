<?php

namespace Illuminate\Database\Eloquent\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Scope
{
    /**
     * Create a new attribute instance.
     */
    public function __construct()
    {
    }
}
