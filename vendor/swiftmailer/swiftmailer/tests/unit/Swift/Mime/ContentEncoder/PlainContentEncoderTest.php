<?php

class Swift_Mime_ContentEncoder_PlainContentEncoderTest extends \SwiftMailerTestCase
{
    public function testNameCanBeSpecifiedInConstructor()
    {
        $encoder = $this->_getEncoder('7bit');
        $this->assertEquals('7bit', $encoder->getName());

        $encoder = $this->_getEncoder('8bit');
        $this->assertEquals('8bit', $encoder->getName());
    }

    public function testNoOctetsAreModifiedInString()
    {
        $encoder = $this->_getEncoder('7bit');
        foreach (range(0x00, 0xFF) as $octet) {
            $byte = pack('C', $octet);
            $this->assertIdenticalBinary($byte, $encoder->encodeString($byte));
        }
    }

    public function testNoOctetsAreModifiedInByteStream()
    {
        $encoder = $this->_getEncoder('7bit');
        foreach (range(0x00, 0xFF) as $octet) {
            $byte = pack('C', $octet);

            $os = $this->_createOutputByteStream();
            $is = $this->_createInputByteStream();
            $collection = new Swift_StreamCollector();

            $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
            $os->shouldReceive('read')
               ->once()
               ->andReturn($byte);
            $os->shouldReceive('read')
               ->zeroOrMoreTimes()
               ->andReturn(false);

            $encoder->encodeByteStream($os, $is);
            $this->assertIdenticalBinary($byte, $collection->content);
        }
    }

    public function testLineLengthCanBeSpecified()
    {
        $encoder = $this->_getEncoder('7bit');

        $chars = array();
        for ($i = 0; $i < 50; $i++) {
            $chars[] = 'a';
        }
        $input = implode(' ', $chars); //99 chars long

        $this->assertEquals(
            'a a a a a a a a a a a a a a a a a a a a a a a a a '."\r\n".//50 *
            'a a a a a a a a a a a a a a a a a a a a a a a a a',            //99
            $encoder->encodeString($input, 0, 50),
            '%s: Lines should be wrapped at 50 chars'
            );
    }

    public function testLineLengthCanBeSpecifiedInByteStream()
    {
        $encoder = $this->_getEncoder('7bit');

        $os = $this->_createOutputByteStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
           ->zeroOrMoreTimes()
           ->andReturnUsing($collection);

        for ($i = 0; $i < 50; $i++) {
            $os->shouldReceive('read')
               ->once()
               ->andReturn('a ');
        }

        $os->shouldReceive('read')
           ->zeroOrMoreTimes()
           ->andReturn(false);

        $encoder->encodeByteStream($os, $is, 0, 50);
        $this->assertEquals(
            str_repeat('a ', 25)."\r\n".str_repeat('a ', 25),
            $collection->content
            );
    }

    public function testencodeStringGeneratesCorrectCrlf()
    {
        $encoder = $this->_getEncoder('7bit', true);
        $this->assertEquals("a\r\nb", $encoder->encodeString("a\rb"),
            '%s: Line endings should be standardized'
            );
        $this->assertEquals("a\r\nb", $encoder->encodeString("a\nb"),
            '%s: Line endings should be standardized'
            );
        $this->assertEquals("a\r\n\r\nb", $encoder->encodeString("a\n\rb"),
            '%s: Line endings should be standardized'
            );
        $this->assertEquals("a\r\n\r\nb", $encoder->encodeString("a\r\rb"),
            '%s: Line endings should be standardized'
            );
        $this->assertEquals("a\r\n\r\nb", $encoder->encodeString("a\n\nb"),
            '%s: Line endings should be standardized'
            );
    }

    public function crlfProvider()
    {
        return array(
            array("\r", "a\r\nb"),
            array("\n", "a\r\nb"),
            array("\n\r", "a\r\n\r\nb"),
            array("\n\n", "a\r\n\r\nb"),
            array("\r\r", "a\r\n\r\nb"),
        );
    }

    /**
     * @dataProvider crlfProvider
     */
    public function testCanonicEncodeByteStreamGeneratesCorrectCrlf($test, $expected)
    {
        $encoder = $this->_getEncoder('7bit', true);

        $os = $this->_createOutputByteStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
           ->zeroOrMoreTimes()
           ->andReturnUsing($collection);
        $os->shouldReceive('read')
           ->once()
           ->andReturn('a');
        $os->shouldReceive('read')
           ->once()
           ->andReturn($test);
        $os->shouldReceive('read')
           ->once()
           ->andReturn('b');
        $os->shouldReceive('read')
           ->zeroOrMoreTimes()
           ->andReturn(false);

        $encoder->encodeByteStream($os, $is);
        $this->assertEquals($expected, $collection->content);
    }

    // -- Private helpers

    private function _getEncoder($name, $canonical = false)
    {
        return new Swift_Mime_ContentEncoder_PlainContentEncoder($name, $canonical);
    }

    private function _createOutputByteStream($stub = false)
    {
        return $this->getMockery('Swift_OutputByteStream')->shouldIgnoreMissing();
    }

    private function _createInputByteStream($stub = false)
    {
        return $this->getMockery('Swift_InputByteStream')->shouldIgnoreMissing();
    }
}
