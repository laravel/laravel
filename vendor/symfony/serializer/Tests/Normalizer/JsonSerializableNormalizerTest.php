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

use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Tests\Fixtures\JsonSerializableDummy;

/**
 * @author Fred Cox <mcfedr@gmail.com>
 */
class JsonSerializableNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonSerializableNormalizer
     */
    private $normalizer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SerializerInterface
     */
    private $serializer;

    protected function setUp()
    {
        $this->serializer = $this->getMock(JsonSerializerNormalizer::class);
        $this->normalizer = new JsonSerializableNormalizer();
        $this->normalizer->setSerializer($this->serializer);
    }

    public function testSupportNormalization()
    {
        $this->assertTrue($this->normalizer->supportsNormalization(new JsonSerializableDummy()));
        $this->assertFalse($this->normalizer->supportsNormalization(new \stdClass()));
    }

    public function testNormalize()
    {
        $this->serializer
            ->expects($this->once())
            ->method('normalize')
            ->will($this->returnCallback(function($data) {
                $this->assertArraySubset(array('foo' => 'a', 'bar' => 'b', 'baz' => 'c'), $data);

                return 'string_object';
            }))
        ;

        $this->assertEquals('string_object', $this->normalizer->normalize(new JsonSerializableDummy()));
    }

    /**
     * @expectedException \Symfony\Component\Serializer\Exception\CircularReferenceException
     */
    public function testCircularNormalize()
    {
        $this->normalizer->setCircularReferenceLimit(1);

        $this->serializer
            ->expects($this->once())
            ->method('normalize')
            ->will($this->returnCallback(function($data, $format, $context) {
                $this->normalizer->normalize($data['qux'], $format, $context);

                return 'string_object';
            }))
        ;

        $this->assertEquals('string_object', $this->normalizer->normalize(new JsonSerializableDummy()));
    }

    /**
     * @expectedException \Symfony\Component\Serializer\Exception\InvalidArgumentException
     * @expectedExceptionMessage The object must implement "JsonSerializable".
     */
    public function testInvalidDataThrowException()
    {
        $this->normalizer->normalize(new \stdClass());
    }
}

abstract class JsonSerializerNormalizer implements SerializerInterface, NormalizerInterface
{
}
