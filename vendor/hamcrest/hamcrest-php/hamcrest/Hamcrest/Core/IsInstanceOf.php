<?php
namespace Hamcrest\Core;

/*
 Copyright (c) 2009 hamcrest.org
 */
use Hamcrest\Description;
use Hamcrest\DiagnosingMatcher;

/**
 * Tests whether the value is an instance of a class.
 */
class IsInstanceOf extends DiagnosingMatcher
{

    private $_theClass;

    /**
     * Creates a new instance of IsInstanceOf
     *
     * @param string $theClass
     *   The predicate evaluates to true for instances of this class
     *   or one of its subclasses.
     */
    public function __construct($theClass)
    {
        $this->_theClass = $theClass;
    }

    protected function matchesWithDiagnosticDescription($item, Description $mismatchDescription)
    {
        if (!is_object($item)) {
            $mismatchDescription->appendText('was ')->appendValue($item);

            return false;
        }

        if (!($item instanceof $this->_theClass)) {
            $mismatchDescription->appendText('[' . get_class($item) . '] ')
                                                    ->appendValue($item);

            return false;
        }

        return true;
    }

    public function describeTo(Description $description)
    {
        $description->appendText('an instance of ')
                                ->appendText($this->_theClass)
                                ;
    }

    /**
     * Is the value an instance of a particular type?
     * This version assumes no relationship between the required type and
     * the signature of the method that sets it up, for example in
     * <code>assertThat($anObject, anInstanceOf('Thing'));</code>
     *
     * @factory any
     */
    public static function anInstanceOf($theClass)
    {
        return new self($theClass);
    }
}
