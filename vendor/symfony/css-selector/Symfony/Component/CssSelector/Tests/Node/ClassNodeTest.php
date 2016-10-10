<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Tests\Node;

use Symfony\Component\CssSelector\Node\ClassNode;
use Symfony\Component\CssSelector\Node\ElementNode;

class ClassNodeTest extends AbstractNodeTest
{
    public function getToStringConversionTestData()
    {
        return array(
            array(new ClassNode(new ElementNode(), 'class'), 'Class[Element[*].class]'),
        );
    }

    public function getSpecificityValueTestData()
    {
        return array(
            array(new ClassNode(new ElementNode(), 'class'), 10),
            array(new ClassNode(new ElementNode(null, 'element'), 'class'), 11),
        );
    }
}
