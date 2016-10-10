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

use Symfony\Component\Serializer\Tests\Fixtures\ScalarDummy;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Serializer;

class CustomNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomNormalizer
     */
    private $normalizer;

    protected function setUp()
    {
        $this->normalizer = new CustomNormalizer();
        $this->normalizer->setSerializer(new Serializer());
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Symfony\Component\Serializer\Normalizer\NormalizerInterface', $this->normalizer);
        $this->assertInstanceOf('Symfony\Component\Serializer\Normalizer\DenormalizerInterface', $this->normalizer);
        $this->assertInstanceOf('Symfony\Component\Serializer\SerializerAwareInterface', $this->normalizer);
    }

    public function testSerialize()
    {
        $obj = new ScalarDummy();
        $obj->foo = 'foo';
        $obj->xmlFoo = 'xml';
        $this->assertEquals('foo', $this->normalizer->normalize($obj, 'json'));
        $this->assertEquals('xml', $this->normalizer->normalize($obj, 'xml'));
    }

    public function testDeserialize()
    {
        $obj = $this->normalizer->denormalize('foo', get_class(new ScalarDummy()), 'xml');
        $this->assertEquals('foo', $obj->xmlFoo);
        $this->assertNull($obj->foo);

        $obj = $this->normalizer->denormalize('foo', get_class(new ScalarDummy()), 'json');
        $this->assertEquals('foo', $obj->foo);
        $this->assertNull($obj->xmlFoo);
    }

    public function testSupportsNormalization()
    {
        $this->assertTrue($this->normalizer->supportsNormalization(new ScalarDummy()));
        $this->assertFalse($this->normalizer->supportsNormalization(new \stdClass()));
    }

    public function testSupportsDenormalization()
    {
        $this->assertTrue($this->normalizer->supportsDenormalization(array(), 'Symfony\Component\Serializer\Tests\Fixtures\ScalarDummy'));
        $this->assertFalse($this->normalizer->supportsDenormalization(array(), 'stdClass'));
        $this->assertTrue($this->normalizer->supportsDenormalization(array(), 'Symfony\Component\Serializer\Tests\Fixtures\DenormalizableDummy'));
    }
}
