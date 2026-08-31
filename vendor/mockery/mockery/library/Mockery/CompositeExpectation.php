<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use function array_map;
use function current;
use function implode;
use function reset;

class CompositeExpectation implements ExpectationInterface
{
    /**
     * Stores an array of all expectations for this composite
     *
     * @var array<ExpectationInterface>
     */
    protected $_expectations = [];

    /**
     * Intercept any expectation calls and direct against all expectations
     *
     * @param string $method
     *
     * @return self
     */
    public function __call($method, array $args)
    {
        foreach ($this->_expectations as $expectation) {
            $expectation->{$method}(...$args);
        }

        return $this;
    }

    /**
     * Return the string summary of this composite expectation
     *
     * @return string
     */
    public function __toString()
    {
        $parts = array_map(static function (ExpectationInterface $expectation): string {
            return (string) $expectation;
        }, $this->_expectations);

        return '[' . implode(', ', $parts) . ']';
    }

    /**
     * Add an expectation to the composite
     *
     * @param ExpectationInterface|HigherOrderMessage $expectation
     *
     * @return void
     */
    public function add($expectation)
    {
        $this->_expectations[] = $expectation;
    }

    /**
     * @param mixed ...$args
     */
    public function andReturn(...$args)
    {
        return $this->__call(__FUNCTION__, $args);
    }

    /**
     * Set a return value, or sequential queue of return values
     *
     * @param mixed ...$args
     *
     * @return self
     */
    public function andReturns(...$args)
    {
        return $this->andReturn(...$args);
    }

    /**
     * Return the parent mock of the first expectation
     *
     * @return LegacyMockInterface&MockInterface
     */
    public function getMock()
    {
        reset($this->_expectations);
        $first = current($this->_expectations);
        return $first->getMock();
    }

    /**
     * Return order number of the first expectation
     *
     * @return int
     */
    public function getOrderNumber()
    {
        reset($this->_expectations);
        $first = current($this->_expectations);
        return $first->getOrderNumber();
    }

    /**
     * Mockery API alias to getMock
     *
     * @return LegacyMockInterface&MockInterface
     */
    public function mock()
    {
        return $this->getMock();
    }

    /**
     * Starts a new expectation addition on the first mock which is the primary target outside of a demeter chain
     *
     * @param mixed ...$args
     *
     * @return Expectation
     */
    public function shouldNotReceive(...$args)
    {
        reset($this->_expectations);
        $first = current($this->_expectations);
        return $first->getMock()->shouldNotReceive(...$args);
    }

    /**
     * Starts a new expectation addition on the first mock which is the primary target, outside of a demeter chain
     *
     * @param mixed ...$args
     *
     * @return Expectation
     */
    public function shouldReceive(...$args)
    {
        reset($this->_expectations);
        $first = current($this->_expectations);
        return $first->getMock()->shouldReceive(...$args);
    }
}
