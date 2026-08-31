<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use InvalidArgumentException;

class MockDefinition
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var MockConfiguration
     */
    protected $config;

    /**
     * @param  string                   $code
     * @throws InvalidArgumentException
     */
    public function __construct(MockConfiguration $config, $code)
    {
        if (! $config->getName()) {
            throw new InvalidArgumentException('MockConfiguration must contain a name');
        }

        $this->config = $config;
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->config->getName();
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return MockConfiguration
     */
    public function getConfig()
    {
        return $this->config;
    }
}
