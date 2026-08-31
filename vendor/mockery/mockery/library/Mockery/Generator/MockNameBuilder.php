<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use function implode;
use function str_replace;

class MockNameBuilder
{
    /**
     * @var int
     */
    protected static $mockCounter = 0;

    /**
     * @var list<string>
     */
    protected $parts = [];

    /**
     * @param string $part
     */
    public function addPart($part)
    {
        $this->parts[] = $part;

        return $this;
    }

    /**
     * @return string
     */
    public function build()
    {
        $parts = ['Mockery', static::$mockCounter++];

        foreach ($this->parts as $part) {
            $parts[] = str_replace('\\', '_', $part);
        }

        return implode('_', $parts);
    }
}
