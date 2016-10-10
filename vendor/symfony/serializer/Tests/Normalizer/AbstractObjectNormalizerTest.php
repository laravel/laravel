<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Normalizer;

use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class AbstractObjectNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testDenormalize()
    {
        $normalizer = new AbstractObjectNormalizerDummy();
        $normalizedData = $normalizer->denormalize(array('foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz'), __NAMESPACE__.'\Dummy');

        $this->assertSame('foo', $normalizedData->foo);
        $this->assertNull($normalizedData->bar);
        $this->assertSame('baz', $normalizedData->baz);
    }
}

class AbstractObjectNormalizerDummy extends AbstractObjectNormalizer
{
    protected function extractAttributes($object, $format = null, array $context = array())
    {
    }

    protected function getAttributeValue($object, $attribute, $format = null, array $context = array())
    {
    }

    protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = array())
    {
        $object->$attribute = $value;
    }

    protected function isAllowedAttribute($classOrObject, $attribute, $format = null, array $context = array())
    {
        return in_array($attribute, array('foo', 'baz'));
    }
}

class Dummy
{
    public $foo;
    public $bar;
    public $baz;
}
