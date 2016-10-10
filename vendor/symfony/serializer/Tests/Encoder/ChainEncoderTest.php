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

use Symfony\Component\Serializer\Encoder\ChainEncoder;
use Symfony\Component\Serializer\Encoder\NormalizationAwareInterface;

class ChainEncoderTest extends \PHPUnit_Framework_TestCase
{
    const FORMAT_1 = 'format1';
    const FORMAT_2 = 'format2';
    const FORMAT_3 = 'format3';

    private $chainEncoder;
    private $encoder1;
    private $encoder2;

    protected function setUp()
    {
        $this->encoder1 = $this
            ->getMockBuilder('Symfony\Component\Serializer\Encoder\EncoderInterface')
            ->getMock();

        $this->encoder1
            ->method('supportsEncoding')
            ->will($this->returnValueMap(array(
                array(self::FORMAT_1, true),
                array(self::FORMAT_2, false),
                array(self::FORMAT_3, false),
            )));

        $this->encoder2 = $this
            ->getMockBuilder('Symfony\Component\Serializer\Encoder\EncoderInterface')
            ->getMock();

        $this->encoder2
            ->method('supportsEncoding')
            ->will($this->returnValueMap(array(
                array(self::FORMAT_1, false),
                array(self::FORMAT_2, true),
                array(self::FORMAT_3, false),
            )));

        $this->chainEncoder = new ChainEncoder(array($this->encoder1, $this->encoder2));
    }

    public function testSupportsEncoding()
    {
        $this->assertTrue($this->chainEncoder->supportsEncoding(self::FORMAT_1));
        $this->assertTrue($this->chainEncoder->supportsEncoding(self::FORMAT_2));
        $this->assertFalse($this->chainEncoder->supportsEncoding(self::FORMAT_3));
    }

    public function testEncode()
    {
        $this->encoder1->expects($this->never())->method('encode');
        $this->encoder2->expects($this->once())->method('encode');

        $this->chainEncoder->encode(array('foo' => 123), self::FORMAT_2);
    }

    /**
     * @expectedException Symfony\Component\Serializer\Exception\RuntimeException
     */
    public function testEncodeUnsupportedFormat()
    {
        $this->chainEncoder->encode(array('foo' => 123), self::FORMAT_3);
    }

    public function testNeedsNormalizationBasic()
    {
        $this->assertTrue($this->chainEncoder->needsNormalization(self::FORMAT_1));
        $this->assertTrue($this->chainEncoder->needsNormalization(self::FORMAT_2));
    }

    /**
     * @dataProvider booleanProvider
     */
    public function testNeedsNormalizationChainNormalizationAware($bool)
    {
        $chainEncoder = $this
            ->getMockBuilder('Symfony\Component\Serializer\Tests\Encoder\ChainNormalizationAwareEncoder')
            ->getMock();

        $chainEncoder->method('supportsEncoding')->willReturn(true);
        $chainEncoder->method('needsNormalization')->willReturn($bool);

        $sut = new ChainEncoder(array($chainEncoder));

        $this->assertEquals($bool, $sut->needsNormalization(self::FORMAT_1));
    }

    public function testNeedsNormalizationNormalizationAware()
    {
        $encoder = new NormalizationAwareEncoder();
        $sut = new ChainEncoder(array($encoder));

        $this->assertFalse($sut->needsNormalization(self::FORMAT_1));
    }

    public function booleanProvider()
    {
        return array(
            array(true),
            array(false),
        );
    }
}

class ChainNormalizationAwareEncoder extends ChainEncoder implements NormalizationAwareInterface
{
}

class NormalizationAwareEncoder implements NormalizationAwareInterface
{
    public function supportsEncoding($format)
    {
        return true;
    }
}
