<?php
namespace Hamcrest\Arrays;

/*
 Copyright (c) 2009 hamcrest.org
 */

use Hamcrest\Description;

class MatchingOnce
{

    private $_elementMatchers;
    private $_mismatchDescription;

    public function __construct(array $elementMatchers, Description $mismatchDescription)
    {
        $this->_elementMatchers = $elementMatchers;
        $this->_mismatchDescription = $mismatchDescription;
    }

    public function matches($item)
    {
        return $this->_isNotSurplus($item) && $this->_isMatched($item);
    }

    public function isFinished($items)
    {
        if (empty($this->_elementMatchers)) {
            return true;
        }

        $this->_mismatchDescription
                 ->appendText('No item matches: ')->appendList('', ', ', '', $this->_elementMatchers)
                 ->appendText(' in ')->appendValueList('[', ', ', ']', $items)
                 ;

        return false;
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
            /** @var $matcher \Hamcrest\Matcher */
        foreach ($this->_elementMatchers as $i => $matcher) {
            if ($matcher->matches($item)) {
                unset($this->_elementMatchers[$i]);

                return true;
            }
        }

        $this->_mismatchDescription->appendText('Not matched: ')->appendValue($item);

        return false;
    }
}
