<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Fixtures;

class TraversableDummy implements \IteratorAggregate
{
    public $foo = 'foo';
    public $bar = 'bar';

    public function getIterator()
    {
        return new \ArrayIterator(get_object_vars($this));
    }
}
