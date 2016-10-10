<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Encoder;

use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class JsonDecodeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\Serializer\Encoder\JsonDecode */
    private $decode;

    protected function setUp()
    {
        $this->decode = new JsonDecode();
    }

    public function testSupportsDecoding()
    {
        $this->assertTrue($this->decode->supportsDecoding(JsonEncoder::FORMAT));
        $this->assertFalse($this->decode->supportsDecoding('foobar'));
    }

    /**
     * @dataProvider decodeProvider
     */
    public function testDecode($toDecode, $expected, $context)
    {
        $this->assertEquals(
            $expected,
            $this->decode->decode($toDecode, JsonEncoder::FORMAT, $context)
        );
    }

    public function decodeProvider()
    {
        $stdClass = new \stdClass();
        $stdClass->foo = 'bar';

        $assoc = array('foo' => 'bar');

        return array(
            array('{"foo": "bar"}', $stdClass, array()),
            array('{"foo": "bar"}', $assoc, array('json_decode_associative' => true)),
        );
    }

    /**
     * @requires function json_last_error_msg
     * @dataProvider decodeProviderException
     * @expectedException Symfony\Component\Serializer\Exception\UnexpectedValueException
     */
    public function testDecodeWithException($value)
    {
        $this->decode->decode($value,  JsonEncoder::FORMAT);
    }

    public function decodeProviderException()
    {
        return array(
            array("{'foo': 'bar'}"),
            array('kaboom!'),
        );
    }
}
