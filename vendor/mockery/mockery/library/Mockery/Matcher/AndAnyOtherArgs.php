<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

class AndAnyOtherArgs extends MatcherAbstract
{
    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    public function __toString()
    {
        return '<AndAnyOthers>';
    }

    /**
     * Check if the actual value matches the expected.
     *
     * @template TMixed
     *
     * @param TMixed $actual
     *
     * @return bool
     */
    public function match(&$actual)
    {
        return true;
    }
}
