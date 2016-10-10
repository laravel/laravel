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

use Symfony\Component\CssSelector\Node\ElementNode;
use Symfony\Component\CssSelector\Node\PseudoNode;

class PseudoNodeTest extends AbstractNodeTest
{
    public function getToStringConversionTestData()
    {
        return array(
            array(new PseudoNode(new ElementNode(), 'pseudo'), 'Pseudo[Element[*]:pseudo]'),
        );
    }

    public function getSpecificityValueTestData()
    {
        return array(
            array(new PseudoNode(new ElementNode(), 'pseudo'), 10),
        );
    }
}
