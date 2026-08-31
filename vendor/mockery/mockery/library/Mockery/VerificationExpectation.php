<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

class VerificationExpectation extends Expectation
{
    public function __clone()
    {
        parent::__clone();

        $this->_actualCount = 0;
    }

    /**
     * @return void
     */
    public function clearCountValidators()
    {
        $this->_countValidators = [];
    }
}
