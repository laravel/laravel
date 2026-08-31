<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Iterator;

/**
 * @author Jérémy Derussé <jeremy@derusse.com>
 *
 * @internal
 */
class LazyIterator implements \IteratorAggregate
{
    private \Closure $iteratorFactory;

    public function __construct(callable $iteratorFactory)
    {
        $this->iteratorFactory = $iteratorFactory(...);
    }

    public function getIterator(): \Traversable
    {
        yield from ($this->iteratorFactory)();
    }
}
