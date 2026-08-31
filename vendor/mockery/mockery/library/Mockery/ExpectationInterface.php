<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

interface ExpectationInterface
{
    /**
     * @template TArgs
     *
     * @param TArgs ...$args
     *
     * @return self
     */
    public function andReturn(...$args);

    /**
     * @return self
     */
    public function andReturns();

    /**
     * @return LegacyMockInterface|MockInterface
     */
    public function getMock();

    /**
     * @return int
     */
    public function getOrderNumber();
}
