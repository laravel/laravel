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

use Symfony\Component\CssSelector\Node\Specificity;

class SpecificityTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider getValueTestData */
    public function testValue(Specificity $specificity, $value)
    {
        $this->assertEquals($value, $specificity->getValue());
    }

    /** @dataProvider getValueTestData */
    public function testPlusValue(Specificity $specificity, $value)
    {
        $this->assertEquals($value + 123, $specificity->plus(new Specificity(1, 2, 3))->getValue());
    }

    public function getValueTestData()
    {
        return array(
            array(new Specificity(0, 0, 0), 0),
            array(new Specificity(0, 0, 2), 2),
            array(new Specificity(0, 3, 0), 30),
            array(new Specificity(4, 0, 0), 400),
            array(new Specificity(4, 3, 2), 432),
        );
    }
}
