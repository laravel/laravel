<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

class CachingGenerator implements Generator
{
    /**
     * @var array<string,string>
     */
    protected $cache = [];

    /**
     * @var Generator
     */
    protected $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return string
     */
    public function generate(MockConfiguration $config)
    {
        $hash = $config->getHash();

        if (array_key_exists($hash, $this->cache)) {
            return $this->cache[$hash];
        }

        return $this->cache[$hash] = $this->generator->generate($config);
    }
}
