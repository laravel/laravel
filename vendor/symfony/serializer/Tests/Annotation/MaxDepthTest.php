<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Annotation;

use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class MaxDepthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Serializer\Exception\InvalidArgumentException
     */
    public function testNotAnIntMaxDepthParameter()
    {
        new MaxDepth(array('value' => 'foo'));
    }

    public function testMaxDepthParameters()
    {
        $validData = 3;

        $groups = new MaxDepth(array('value' => 3));
        $this->assertEquals($validData, $groups->getMaxDepth());
    }
}
