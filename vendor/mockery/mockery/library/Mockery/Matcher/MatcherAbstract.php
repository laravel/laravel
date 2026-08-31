<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

/**
 * @deprecated Implement \Mockery\Matcher\MatcherInterface instead of extending this class
 * @see https://github.com/mockery/mockery/pull/1338
 */
abstract class MatcherAbstract implements MatcherInterface
{
    /**
     * The expected value (or part thereof)
     *
     * @template TExpected
     *
     * @var TExpected
     */
    protected $_expected = null;

    /**
     * Set the expected value
     *
     * @template TExpected
     *
     * @param TExpected $expected
     */
    public function __construct($expected = null)
    {
        $this->_expected = $expected;
    }
}
