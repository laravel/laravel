<?php
namespace Hamcrest\Arrays;

/*
 Copyright (c) 2009 hamcrest.org
 */

use Hamcrest\Description;
use Hamcrest\Matcher;

class SeriesMatchingOnce
{

    private $_elementMatchers;
    private $_keys;
    private $_mismatchDescription;
    private $_nextMatchKey;

    public function __construct(array $elementMatchers, Description $mismatchDescription)
    {
        $this->_elementMatchers = $elementMatchers;
        $this->_keys = array_keys($elementMatchers);
        $this->_mismatchDescription = $mismatchDescription;
    }

    public function matches($item)
    {
        return $this->_isNotSurplus($item) && $this->_isMatched($item);
    }

    public function isFinished()
    {
        if (!empty($this->_elementMatchers)) {
            $nextMatcher = current($this->_elementMatchers);
            $this->_mismatchDescription->appendText('No item matched: ')->appendDescriptionOf($nextMatcher);

            return false;
        }

        return true;
    }

    // -- Private Methods

    private function _isNotSurplus($item)
    {
        if (empty($this->_elementMatchers)) {
            $this->_mismatchDescription->appendText('Not matched: ')->appendValue($item);

            return false;
        }

        return true;
    }

    private function _isMatched($item)
    {
        $this->_nextMatchKey = array_shift($this->_keys);
        $nextMatcher = array_shift($this->_elementMatchers);

        if (!$nextMatcher->matches($item)) {
            $this->_describeMismatch($nextMatcher, $item);

            return false;
        }

        return true;
    }

    private function _describeMismatch(Matcher $matcher, $item)
    {
        $this->_mismatchDescription->appendText('item with key ' . $this->_nextMatchKey . ': ');
        $matcher->describeMismatch($item, $this->_mismatchDescription);
    }
}
