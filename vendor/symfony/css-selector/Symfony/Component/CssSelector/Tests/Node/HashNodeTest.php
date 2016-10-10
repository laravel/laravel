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

use Symfony\Component\CssSelector\Node\HashNode;
use Symfony\Component\CssSelector\Node\ElementNode;

class HashNodeTest extends AbstractNodeTest
{
    public function getToStringConversionTestData()
    {
        return array(
            array(new HashNode(new ElementNode(), 'id'), 'Hash[Element[*]#id]'),
        );
    }

    public function getSpecificityValueTestData()
    {
        return array(
            array(new HashNode(new ElementNode(), 'id'), 100),
            array(new HashNode(new ElementNode(null, 'id'), 'class'), 101),
        );
    }
}
