<?php
namespace Hamcrest\Type;

/*
 Copyright (c) 2010 hamcrest.org
 */
use Hamcrest\Core\IsTypeOf;

/**
 * Tests whether the value is callable.
 */
class IsCallable extends IsTypeOf
{

    /**
     * Creates a new instance of IsCallable
     */
    public function __construct()
    {
        parent::__construct('callable');
    }

    public function matches($item)
    {
        return is_callable($item);
    }

    /**
     * Is the value callable?
     *
     * @factory
     */
    public static function callableValue()
    {
        return new self;
    }
}
