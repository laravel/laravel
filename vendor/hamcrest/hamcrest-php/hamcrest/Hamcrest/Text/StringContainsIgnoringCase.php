<?php
namespace Hamcrest\Text;

/*
 Copyright (c) 2010 hamcrest.org
 */

/**
 * Tests if the argument is a string that contains a substring ignoring case.
 */
class StringContainsIgnoringCase extends SubstringMatcher
{

    public function __construct($substring)
    {
        parent::__construct($substring);
    }

    /**
     * Matches if value is a string that contains $substring regardless of the case.
     *
     * @factory
     */
    public static function containsStringIgnoringCase($substring)
    {
        return new self($substring);
    }

    // -- Protected Methods

    protected function evalSubstringOf($item)
    {
        return (false !== stripos((string) $item, $this->_substring));
    }

    protected function relationship()
    {
        return 'containing in any case';
    }
}
