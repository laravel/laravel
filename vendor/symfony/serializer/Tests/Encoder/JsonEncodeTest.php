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

use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class JsonEncodeTest extends \PHPUnit_Framework_TestCase
{
    private $encoder;

    protected function setUp()
    {
        $this->encode = new JsonEncode();
    }

    public function testSupportsEncoding()
    {
        $this->assertTrue($this->encode->supportsEncoding(JsonEncoder::FORMAT));
        $this->assertFalse($this->encode->supportsEncoding('foobar'));
    }

    /**
     * @dataProvider encodeProvider
     */
    public function testEncode($toEncode, $expected, $context)
    {
        $this->assertEquals(
            $expected,
            $this->encode->encode($toEncode, JsonEncoder::FORMAT, $context)
        );
    }

    public function encodeProvider()
    {
        return array(
            array(array(), '[]', array()),
            array(array(), '{}', array('json_encode_options' => JSON_FORCE_OBJECT)),
        );
    }

    /**
     * @requires function json_last_error_msg
     * @expectedException Symfony\Component\Serializer\Exception\UnexpectedValueException
     */
    public function testEncodeWithError()
    {
        $this->encode->encode("\xB1\x31",  JsonEncoder::FORMAT);
    }
}
