<?php
namespace Hamcrest\Type;

/*
 Copyright (c) 2010 hamcrest.org
 */
use Hamcrest\Core\IsTypeOf;

/**
 * Tests whether the value is a float/double.
 *
 * PHP returns "double" for values of type "float".
 */
class IsDouble extends IsTypeOf
{

    /**
     * Creates a new instance of IsDouble
     */
    public function __construct()
    {
        parent::__construct('double');
    }

    /**
     * Is the value a float/double?
     *
     * @factory floatValue
     */
    public static function doubleValue()
    {
        return new self;
    }
}
