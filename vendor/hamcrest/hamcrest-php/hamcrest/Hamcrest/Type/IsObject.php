<?php
namespace Hamcrest\Type;

/*
 Copyright (c) 2010 hamcrest.org
 */
use Hamcrest\Core\IsTypeOf;

/**
 * Tests whether the value is an object.
 */
class IsObject extends IsTypeOf
{

    /**
     * Creates a new instance of IsObject
     */
    public function __construct()
    {
        parent::__construct('object');
    }

    /**
     * Is the value an object?
     *
     * @factory anObject
     */
    public static function objectValue()
    {
        return new self;
    }
}
