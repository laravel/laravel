<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use Closure;

/**
 * @method Expectation withArgs(array|Closure $args)
 */
class HigherOrderMessage
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var LegacyMockInterface|MockInterface
     */
    private $mock;

    public function __construct(MockInterface $mock, $method)
    {
        $this->mock = $mock;
        $this->method = $method;
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return Expectation|ExpectationInterface|HigherOrderMessage
     */
    public function __call($method, $args)
    {
        if ($this->method === 'shouldNotHaveReceived') {
            return $this->mock->{$this->method}($method, $args);
        }

        $expectation = $this->mock->{$this->method}($method);

        return $expectation->withArgs($args);
    }
}
