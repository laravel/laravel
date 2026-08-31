<?php
namespace Hamcrest\Text;

/*
 Copyright (c) 2009 hamcrest.org
 */
use Hamcrest\Description;
use Hamcrest\TypeSafeMatcher;

/**
 * Tests if a string is equal to another string, ignoring any changes in
 * whitespace.
 */
class IsEqualIgnoringWhiteSpace extends TypeSafeMatcher
{

    private $_string;

    public function __construct($string)
    {
        parent::__construct(self::TYPE_STRING);

        $this->_string = $string;
    }

    protected function matchesSafely($item)
    {
        return (strtolower($this->_stripSpace($item))
                === strtolower($this->_stripSpace($this->_string)));
    }

    protected function describeMismatchSafely($item, Description $mismatchDescription)
    {
        $mismatchDescription->appendText('was ')->appendText($item);
    }

    public function describeTo(Description $description)
    {
        $description->appendText('equalToIgnoringWhiteSpace(')
                                ->appendValue($this->_string)
                                ->appendText(')')
                                ;
    }

    /**
     * Matches if value is a string equal to $string, regardless of whitespace.
     *
     * @factory
     */
    public static function equalToIgnoringWhiteSpace($string)
    {
        return new self($string);
    }

    // -- Private Methods

    private function _stripSpace($string)
    {
        $parts = preg_split("/[\r\n\t ]+/", $string);
        foreach ($parts as $i => $part) {
            $parts[$i] = trim($part, " \r\n\t");
        }

        return trim(implode(' ', $parts), " \r\n\t");
    }
}
