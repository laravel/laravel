<?php
namespace Hamcrest\Type;

/*
 Copyright (c) 2010 hamcrest.org
 */
use Hamcrest\Core\IsTypeOf;

/**
 * Tests whether the value is an array.
 */
class IsArray extends IsTypeOf
{

    /**
     * Creates a new instance of IsArray
     */
    public function __construct()
    {
        parent::__construct('array');
    }

    /**
     * Is the value an array?
     *
     * @factory
     */
    public static function arrayValue()
    {
        return new self;
    }
}
