<?php
namespace Hamcrest\Arrays;

/*
 Copyright (c) 2009 hamcrest.org
 */

// NOTE: This class is not exactly a direct port of Java's since Java handles
//       arrays quite differently than PHP

// TODO: Allow this to take matchers or values within the array
use Hamcrest\Description;
use Hamcrest\TypeSafeMatcher;
use Hamcrest\Util;

/**
 * Matcher for array whose elements satisfy a sequence of matchers.
 * The array size must equal the number of element matchers.
 */
class IsArray extends TypeSafeMatcher
{

    private $_elementMatchers;

    public function __construct(array $elementMatchers)
    {
        parent::__construct(self::TYPE_ARRAY);

        Util::checkAllAreMatchers($elementMatchers);

        $this->_elementMatchers = $elementMatchers;
    }

    protected function matchesSafely($array)
    {
        if (array_keys($array) != array_keys($this->_elementMatchers)) {
            return false;
        }

        /** @var $matcher \Hamcrest\Matcher */
        foreach ($this->_elementMatchers as $k => $matcher) {
            if (!$matcher->matches($array[$k])) {
                return false;
            }
        }

        return true;
    }

    protected function describeMismatchSafely($actual, Description $mismatchDescription)
    {
        if (count($actual) != count($this->_elementMatchers)) {
            $mismatchDescription->appendText('array length was ' . count($actual));

            return;
        } elseif (array_keys($actual) != array_keys($this->_elementMatchers)) {
            $mismatchDescription->appendText('array keys were ')
                                                    ->appendValueList(
                                                        $this->descriptionStart(),
                                                        $this->descriptionSeparator(),
                                                        $this->descriptionEnd(),
                                                        array_keys($actual)
                                                    )
                                                    ;

            return;
        }

        /** @var $matcher \Hamcrest\Matcher */
        foreach ($this->_elementMatchers as $k => $matcher) {
            if (!$matcher->matches($actual[$k])) {
                $mismatchDescription->appendText('element ')->appendValue($k)
                    ->appendText(' was ')->appendValue($actual[$k]);

                return;
            }
        }
    }

    public function describeTo(Description $description)
    {
        $description->appendList(
            $this->descriptionStart(),
            $this->descriptionSeparator(),
            $this->descriptionEnd(),
            $this->_elementMatchers
        );
    }

    /**
     * Evaluates to true only if each $matcher[$i] is satisfied by $array[$i].
     *
     * @factory ...
     */
    public static function anArray(/* args... */)
    {
        $args = func_get_args();

        return new self(Util::createMatcherArray($args));
    }

    // -- Protected Methods

    protected function descriptionStart()
    {
        return '[';
    }

    protected function descriptionSeparator()
    {
        return ', ';
    }

    protected function descriptionEnd()
    {
        return ']';
    }
}
