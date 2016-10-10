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

use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MaxDepthDummy
{
    /**
     * @MaxDepth(2)
     */
    public $foo;

    public $bar;

    /**
     * @var self
     */
    public $child;

    /**
     * @MaxDepth(3)
     */
    public function getBar()
    {
        return $this->bar;
    }

    public function getChild()
    {
        return $this->child;
    }
}
