<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

use function in_array;

class AnyOf extends MatcherAbstract
{
    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    public function __toString()
    {
        return '<AnyOf>';
    }

    /**
     * Check if the actual value does not match the expected (in this
     * case it's specifically NOT expected).
     *
     * @template TMixed
     *
     * @param TMixed $actual
     *
     * @return bool
     */
    public function match(&$actual)
    {
        return in_array($actual, $this->_expected, true);
    }
}
