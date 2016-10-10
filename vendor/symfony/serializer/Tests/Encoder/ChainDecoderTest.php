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

use Symfony\Component\Serializer\Encoder\ChainDecoder;

class ChainDecoderTest extends \PHPUnit_Framework_TestCase
{
    const FORMAT_1 = 'format1';
    const FORMAT_2 = 'format2';
    const FORMAT_3 = 'format3';

    private $chainDecoder;
    private $decoder1;
    private $decoder2;

    protected function setUp()
    {
        $this->decoder1 = $this
            ->getMockBuilder('Symfony\Component\Serializer\Encoder\DecoderInterface')
            ->getMock();

        $this->decoder1
            ->method('supportsDecoding')
            ->will($this->returnValueMap(array(
                array(self::FORMAT_1, true),
                array(self::FORMAT_2, false),
                array(self::FORMAT_3, false),
            )));

        $this->decoder2 = $this
            ->getMockBuilder('Symfony\Component\Serializer\Encoder\DecoderInterface')
            ->getMock();

        $this->decoder2
            ->method('supportsDecoding')
            ->will($this->returnValueMap(array(
                array(self::FORMAT_1, false),
                array(self::FORMAT_2, true),
                array(self::FORMAT_3, false),
            )));

        $this->chainDecoder = new ChainDecoder(array($this->decoder1, $this->decoder2));
    }

    public function testSupportsDecoding()
    {
        $this->assertTrue($this->chainDecoder->supportsDecoding(self::FORMAT_1));
        $this->assertTrue($this->chainDecoder->supportsDecoding(self::FORMAT_2));
        $this->assertFalse($this->chainDecoder->supportsDecoding(self::FORMAT_3));
    }

    public function testDecode()
    {
        $this->decoder1->expects($this->never())->method('decode');
        $this->decoder2->expects($this->once())->method('decode');

        $this->chainDecoder->decode('string_to_decode', self::FORMAT_2);
    }

    /**
     * @expectedException Symfony\Component\Serializer\Exception\RuntimeException
     */
    public function testDecodeUnsupportedFormat()
    {
        $this->chainDecoder->decode('string_to_decode', self::FORMAT_3);
    }
}
