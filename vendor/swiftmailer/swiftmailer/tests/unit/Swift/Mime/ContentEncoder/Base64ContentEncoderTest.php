<?php

class Swift_Mime_ContentEncoder_Base64ContentEncoderTest extends \SwiftMailerTestCase
{
    private $_encoder;

    public function setUp()
    {
        $this->_encoder = new Swift_Mime_ContentEncoder_Base64ContentEncoder();
    }

    public function testNameIsBase64()
    {
        $this->assertEquals('base64', $this->_encoder->getName());
    }

    /*
    There's really no point in testing the entire base64 encoding to the
    level QP encoding has been tested.  base64_encode() has been in PHP for
    years.
    */

    public function testInputOutputRatioIs3to4Bytes()
    {
        /*
        RFC 2045, 6.8

         The encoding process represents 24-bit groups of input bits as output
         strings of 4 encoded characters.  Proceeding from left to right, a
         24-bit input group is formed by concatenating 3 8bit input groups.
         These 24 bits are then treated as 4 concatenated 6-bit groups, each
         of which is translated into a single digit in the base64 alphabet.
         */

        $os = $this->_createOutputByteStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
           ->zeroOrMoreTimes()
           ->andReturnUsing($collection);
        $os->shouldReceive('read')
           ->once()
           ->andReturn('123');
        $os->shouldReceive('read')
           ->zeroOrMoreTimes()
           ->andReturn(false);

        $this->_encoder->encodeByteStream($os, $is);
        $this->assertEquals('MTIz', $collection->content);
    }

    public function testPadLength()
    {
        /*
        RFC 2045, 6.8

       Special processing is performed if fewer than 24 bits are available
       at the end of the data being encoded.  A full encoding quantum is
       always completed at the end of a body.  When fewer than 24 input bits
       are available in an input group, zero bits are added (on the right)
       to form an integral number of 6-bit groups.  Padding at the end of
       the data is performed using the "=" character.  Since all base64
       input is an integral number of octets, only the following cases can
       arise: (1) the final quantum of encoding input is an integral
       multiple of 24 bits; here, the final unit of encoded output will be
       an integral multiple of 4 characters with no "=" padding, (2) the
       final quantum of encoding input is exactly 8 bits; here, the final
       unit of encoded output will be two characters followed by two "="
       padding characters, or (3) the final quantum of encoding input is
       exactly 16 bits; here, the final unit of encoded output will be three
       characters followed by one "=" padding character.
       */

        for ($i = 0; $i < 30; ++$i) {
            $os = $this->_createOutputByteStream();
            $is = $this->_createInputByteStream();
            $collection = new Swift_StreamCollector();

            $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
            $os->shouldReceive('read')
               ->once()
               ->andReturn(pack('C', rand(0, 255)));
            $os->shouldReceive('read')
               ->zeroOrMoreTimes()
               ->andReturn(false);

            $this->_encoder->encodeByteStream($os, $is);
            $this->assertRegExp('~^[a-zA-Z0-9/\+]{2}==$~', $collection->content,
                '%s: A single byte should have 2 bytes of padding'
                );
        }

        for ($i = 0; $i < 30; ++$i) {
            $os = $this->_createOutputByteStream();
            $is = $this->_createInputByteStream();
            $collection = new Swift_StreamCollector();

            $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
            $os->shouldReceive('read')
               ->once()
               ->andReturn(pack('C*', rand(0, 255), rand(0, 255)));
            $os->shouldReceive('read')
               ->zeroOrMoreTimes()
               ->andReturn(false);

            $this->_encoder->encodeByteStream($os, $is);
            $this->assertRegExp('~^[a-zA-Z0-9/\+]{3}=$~', $collection->content,
                '%s: Two bytes should have 1 byte of padding'
                );
        }

        for ($i = 0; $i < 30; ++$i) {
            $os = $this->_createOutputByteStream();
            $is = $this->_createInputByteStream();
            $collection = new Swift_StreamCollector();

            $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
            $os->shouldReceive('read')
               ->once()
               ->andReturn(pack('C*', rand(0, 255), rand(0, 255), rand(0, 255)));
            $os->shouldReceive('read')
               ->zeroOrMoreTimes()
               ->andReturn(false);

            $this->_encoder->encodeByteStream($os, $is);
            $this->assertRegExp('~^[a-zA-Z0-9/\+]{4}$~', $collection->content,
                '%s: Three bytes should have no padding'
                );
        }
    }

    public function testMaximumLineLengthIs76Characters()
    {
        /*
         The encoded output stream must be represented in lines of no more
         than 76 characters each.  All line breaks or other characters not
         found in Table 1 must be ignored by decoding software.
         */

        $os = $this->_createOutputByteStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
           ->zeroOrMoreTimes()
           ->andReturnUsing($collection);
        $os->shouldReceive('read')
           ->once()
           ->andReturn('abcdefghijkl'); //12
        $os->shouldReceive('read')
           ->once()
           ->andReturn('mnopqrstuvwx'); //24
        $os->shouldReceive('read')
           ->once()
           ->andReturn('yzabc1234567'); //36
        $os->shouldReceive('read')
           ->once()
           ->andReturn('890ABCDEFGHI'); //48
        $os->shouldReceive('read')
           ->once()
           ->andReturn('JKLMNOPQRSTU'); //60
        $os->shouldReceive('read')
           ->once()
           ->andReturn('VWXYZ1234567'); //72
        $os->shouldReceive('read')
           ->once()
           ->andReturn('abcdefghijkl'); //84
        $os->shouldReceive('read')
           ->zeroOrMoreTimes()
           ->andReturn(false);

        $this->_encoder->encodeByteStream($os, $is);
        $this->assertEquals(
            "YWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXphYmMxMjM0NTY3ODkwQUJDREVGR0hJSktMTU5PUFFS\r\n".
            'U1RVVldYWVoxMjM0NTY3YWJjZGVmZ2hpamts',
            $collection->content
            );
    }

    public function testMaximumLineLengthCanBeDifferent()
    {
        $os = $this->_createOutputByteStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
           ->zeroOrMoreTimes()
           ->andReturnUsing($collection);
        $os->shouldReceive('read')
           ->once()
           ->andReturn('abcdefghijkl'); //12
        $os->shouldReceive('read')
           ->once()
           ->andReturn('mnopqrstuvwx'); //24
        $os->shouldReceive('read')
           ->once()
           ->andReturn('yzabc1234567'); //36
        $os->shouldReceive('read')
           ->once()
           ->andReturn('890ABCDEFGHI'); //48
        $os->shouldReceive('read')
           ->once()
           ->andReturn('JKLMNOPQRSTU'); //60
        $os->shouldReceive('read')
           ->once()
           ->andReturn('VWXYZ1234567'); //72
        $os->shouldReceive('read')
           ->once()
           ->andReturn('abcdefghijkl'); //84
        $os->shouldReceive('read')
           ->zeroOrMoreTimes()
           ->andReturn(false);

        $this->_encoder->encodeByteStream($os, $is, 0, 50);
        $this->assertEquals(
            "YWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXphYmMxMjM0NTY3OD\r\n".
            "kwQUJDREVGR0hJSktMTU5PUFFSU1RVVldYWVoxMjM0NTY3YWJj\r\n".
            'ZGVmZ2hpamts',
            $collection->content
            );
    }

    public function testMaximumLineLengthIsNeverMoreThan76Chars()
    {
        $os = $this->_createOutputByteStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
           ->zeroOrMoreTimes()
           ->andReturnUsing($collection);
        $os->shouldReceive('read')
           ->once()
           ->andReturn('abcdefghijkl'); //12
        $os->shouldReceive('read')
           ->once()
           ->andReturn('mnopqrstuvwx'); //24
        $os->shouldReceive('read')
           ->once()
           ->andReturn('yzabc1234567'); //36
        $os->shouldReceive('read')
           ->once()
           ->andReturn('890ABCDEFGHI'); //48
        $os->shouldReceive('read')
           ->once()
           ->andReturn('JKLMNOPQRSTU'); //60
        $os->shouldReceive('read')
           ->once()
           ->andReturn('VWXYZ1234567'); //72
        $os->shouldReceive('read')
           ->once()
           ->andReturn('abcdefghijkl'); //84
        $os->shouldReceive('read')
           ->zeroOrMoreTimes()
           ->andReturn(false);

        $this->_encoder->encodeByteStream($os, $is, 0, 100);
        $this->assertEquals(
            "YWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXphYmMxMjM0NTY3ODkwQUJDREVGR0hJSktMTU5PUFFS\r\n".
            'U1RVVldYWVoxMjM0NTY3YWJjZGVmZ2hpamts',
            $collection->content
            );
    }

    public function testFirstLineLengthCanBeDifferent()
    {
        $os = $this->_createOutputByteStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
           ->zeroOrMoreTimes()
           ->andReturnUsing($collection);
        $os->shouldReceive('read')
           ->once()
           ->andReturn('abcdefghijkl'); //12
        $os->shouldReceive('read')
           ->once()
           ->andReturn('mnopqrstuvwx'); //24
        $os->shouldReceive('read')
           ->once()
           ->andReturn('yzabc1234567'); //36
        $os->shouldReceive('read')
           ->once()
           ->andReturn('890ABCDEFGHI'); //48
        $os->shouldReceive('read')
           ->once()
           ->andReturn('JKLMNOPQRSTU'); //60
        $os->shouldReceive('read')
           ->once()
           ->andReturn('VWXYZ1234567'); //72
        $os->shouldReceive('read')
           ->once()
           ->andReturn('abcdefghijkl'); //84
        $os->shouldReceive('read')
           ->zeroOrMoreTimes()
           ->andReturn(false);

        $this->_encoder->encodeByteStream($os, $is, 19);
        $this->assertEquals(
            "YWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXphYmMxMjM0NTY3ODkwQUJDR\r\n".
            'EVGR0hJSktMTU5PUFFSU1RVVldYWVoxMjM0NTY3YWJjZGVmZ2hpamts',
            $collection->content
            );
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
