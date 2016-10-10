<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\NameConverter;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class CamelCaseToSnakeCaseNameConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $attributeMetadata = new CamelCaseToSnakeCaseNameConverter();
        $this->assertInstanceOf('Symfony\Component\Serializer\NameConverter\NameConverterInterface', $attributeMetadata);
    }

    /**
     * @dataProvider attributeProvider
     */
    public function testNormalize($underscored, $lowerCamelCased)
    {
        $nameConverter = new CamelCaseToSnakeCaseNameConverter();
        $this->assertEquals($nameConverter->normalize($lowerCamelCased), $underscored);
    }

    /**
     * @dataProvider attributeProvider
     */
    public function testDenormalize($underscored, $lowerCamelCased)
    {
        $nameConverter = new CamelCaseToSnakeCaseNameConverter();
        $this->assertEquals($nameConverter->denormalize($underscored), $lowerCamelCased);
    }

    public function attributeProvider()
    {
        return array(
            array('coop_tilleuls', 'coopTilleuls'),
            array('_kevin_dunglas', '_kevinDunglas'),
            array('this_is_a_test', 'thisIsATest'),
        );
    }
}
