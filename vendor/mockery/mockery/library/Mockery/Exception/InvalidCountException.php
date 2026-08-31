<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Exception;

use Mockery\CountValidator\Exception;
use Mockery\LegacyMockInterface;

use function in_array;

class InvalidCountException extends Exception
{
    /**
     * @var int|null
     */
    protected $actual = null;

    /**
     * @var int
     */
    protected $expected = 0;

    /**
     * @var string
     */
    protected $expectedComparative = '<=';

    /**
     * @var string|null
     */
    protected $method = null;

    /**
     * @var LegacyMockInterface|null
     */
    protected $mockObject = null;

    /**
     * @return int|null
     */
    public function getActualCount()
    {
        return $this->actual;
    }

    /**
     * @return int
     */
    public function getExpectedCount()
    {
        return $this->expected;
    }

    /**
     * @return string
     */
    public function getExpectedCountComparative()
    {
        return $this->expectedComparative;
    }

    /**
     * @return string|null
     */
    public function getMethodName()
    {
        return $this->method;
    }

    /**
     * @return LegacyMockInterface|null
     */
    public function getMock()
    {
        return $this->mockObject;
    }

    /**
     * @throws RuntimeException
     * @return string|null
     */
    public function getMockName()
    {
        $mock = $this->getMock();

        if ($mock === null) {
            return '';
        }

        return $mock->mockery_getName();
    }

    /**
     * @param  int  $count
     * @return self
     */
    public function setActualCount($count)
    {
        $this->actual = $count;
        return $this;
    }

    /**
     * @param  int  $count
     * @return self
     */
    public function setExpectedCount($count)
    {
        $this->expected = $count;
        return $this;
    }

    /**
     * @param  string $comp
     * @return self
     */
    public function setExpectedCountComparative($comp)
    {
        if (! in_array($comp, ['=', '>', '<', '>=', '<='], true)) {
            throw new RuntimeException('Illegal comparative for expected call counts set: ' . $comp);
        }

        $this->expectedComparative = $comp;
        return $this;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setMethodName($name)
    {
        $this->method = $name;
        return $this;
    }

    /**
     * @return self
     */
    public function setMock(LegacyMockInterface $mock)
    {
        $this->mockObject = $mock;
        return $this;
    }
}
