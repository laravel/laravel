<?php
namespace Hamcrest\Arrays;

/*
 Copyright (c) 2009 hamcrest.org
 */
use Hamcrest\Description;
use Hamcrest\TypeSafeDiagnosingMatcher;
use Hamcrest\Util;

/**
 * Matches if an array contains a set of items satisfying nested matchers.
 */
class IsArrayContainingInOrder extends TypeSafeDiagnosingMatcher
{

    private $_elementMatchers;

    public function __construct(array $elementMatchers)
    {
        parent::__construct(self::TYPE_ARRAY);

        Util::checkAllAreMatchers($elementMatchers);

        $this->_elementMatchers = $elementMatchers;
    }

    protected function matchesSafelyWithDiagnosticDescription($array, Description $mismatchDescription)
    {
        $series = new SeriesMatchingOnce($this->_elementMatchers, $mismatchDescription);

        foreach ($array as $element) {
            if (!$series->matches($element)) {
                return false;
            }
        }

        return $series->isFinished();
    }

    public function describeTo(Description $description)
    {
        $description->appendList('[', ', ', ']', $this->_elementMatchers);
    }

    /**
     * An array with elements that match the given matchers in the same order.
     *
     * @factory contains ...
     */
    public static function arrayContaining(/* args... */)
    {
        $args = func_get_args();

        return new self(Util::createMatcherArray($args));
    }
}
