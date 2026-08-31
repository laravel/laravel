<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\CountValidator;

use Mockery\Expectation;

abstract class CountValidatorAbstract implements CountValidatorInterface
{
    /**
     * Expectation for which this validator is assigned
     *
     * @var Expectation
     */
    protected $_expectation = null;

    /**
     * Call count limit
     *
     * @var int
     */
    protected $_limit = null;

    /**
     * Set Expectation object and upper call limit
     *
     * @param int $limit
     */
    public function __construct(Expectation $expectation, $limit)
    {
        $this->_expectation = $expectation;
        $this->_limit = $limit;
    }

    /**
     * Checks if the validator can accept an additional nth call
     *
     * @param int $n
     *
     * @return bool
     */
    public function isEligible($n)
    {
        return $n < $this->_limit;
    }

    /**
     * Validate the call count against this validator
     *
     * @param int $n
     *
     * @return bool
     */
    abstract public function validate($n);
}
