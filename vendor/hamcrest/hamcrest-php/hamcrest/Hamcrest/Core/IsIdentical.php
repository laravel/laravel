<?php
namespace Hamcrest\Core;

/*
 Copyright (c) 2009 hamcrest.org
 */
use Hamcrest\Description;

/**
 * The same as {@link Hamcrest\Core\IsSame} but with slightly different
 * semantics.
 */
class IsIdentical extends IsSame
{

    private $_value;

    public function __construct($value)
    {
        parent::__construct($value);
        $this->_value = $value;
    }

    public function describeTo(Description $description)
    {
        $description->appendValue($this->_value);
    }

    /**
     * Tests of the value is identical to $value as tested by the "===" operator.
     *
     * @factory
     */
    public static function identicalTo($value)
    {
        return new self($value);
    }
}
