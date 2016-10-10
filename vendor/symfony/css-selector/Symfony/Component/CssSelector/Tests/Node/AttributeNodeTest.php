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

use Symfony\Component\CssSelector\Node\AttributeNode;
use Symfony\Component\CssSelector\Node\ElementNode;

class AttributeNodeTest extends AbstractNodeTest
{
    public function getToStringConversionTestData()
    {
        return array(
            array(new AttributeNode(new ElementNode(), null, 'attribute', 'exists', null), 'Attribute[Element[*][attribute]]'),
            array(new AttributeNode(new ElementNode(), null, 'attribute', '$=', 'value'), "Attribute[Element[*][attribute $= 'value']]"),
            array(new AttributeNode(new ElementNode(), 'namespace', 'attribute', '$=', 'value'), "Attribute[Element[*][namespace|attribute $= 'value']]"),
        );
    }

    public function getSpecificityValueTestData()
    {
        return array(
            array(new AttributeNode(new ElementNode(), null, 'attribute', 'exists', null), 10),
            array(new AttributeNode(new ElementNode(null, 'element'), null, 'attribute', 'exists', null), 11),
            array(new AttributeNode(new ElementNode(), null, 'attribute', '$=', 'value'), 10),
            array(new AttributeNode(new ElementNode(), 'namespace', 'attribute', '$=', 'value'), 10),
        );
    }
}
