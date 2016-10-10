<?php

class Swift_Encoder_Base64EncoderTest extends \PHPUnit_Framework_TestCase
{
    private $_encoder;

    public function setUp()
    {
        $this->_encoder = new Swift_Encoder_Base64Encoder();
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

        $this->assertEquals(
            'MTIz', $this->_encoder->encodeString('123'),
            '%s: 3 bytes of input should yield 4 bytes of output'
            );
        $this->assertEquals(
            'MTIzNDU2', $this->_encoder->encodeString('123456'),
            '%s: 6 bytes in input should yield 8 bytes of output'
            );
        $this->assertEquals(
            'MTIzNDU2Nzg5', $this->_encoder->encodeString('123456789'),
            '%s: 9 bytes in input should yield 12 bytes of output'
            );
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
            $input = pack('C', rand(0, 255));
            $this->assertRegExp(
                '~^[a-zA-Z0-9/\+]{2}==$~', $this->_encoder->encodeString($input),
                '%s: A single byte should have 2 bytes of padding'
                );
        }

        for ($i = 0; $i < 30; ++$i) {
            $input = pack('C*', rand(0, 255), rand(0, 255));
            $this->assertRegExp(
                '~^[a-zA-Z0-9/\+]{3}=$~', $this->_encoder->encodeString($input),
                '%s: Two bytes should have 1 byte of padding'
                );
        }

        for ($i = 0; $i < 30; ++$i) {
            $input = pack('C*', rand(0, 255), rand(0, 255), rand(0, 255));
            $this->assertRegExp(
                '~^[a-zA-Z0-9/\+]{4}$~', $this->_encoder->encodeString($input),
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

        $input =
        'abcdefghijklmnopqrstuvwxyz'.
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
        '1234567890'.
        'abcdefghijklmnopqrstuvwxyz'.
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
        '1234567890'.
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $output =
        'YWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXpBQk'.//38
        'NERUZHSElKS0xNTk9QUVJTVFVWV1hZWjEyMzQ1'."\r\n".//76 *
        'Njc4OTBhYmNkZWZnaGlqa2xtbm9wcXJzdHV2d3'.//38
        'h5ekFCQ0RFRkdISUpLTE1OT1BRUlNUVVZXWFla'."\r\n".//76 *
        'MTIzNDU2Nzg5MEFCQ0RFRkdISUpLTE1OT1BRUl'.//38
        'NUVVZXWFla';                                       //48

        $this->assertEquals(
            $output, $this->_encoder->encodeString($input),
            '%s: Lines should be no more than 76 characters'
            );
    }

    public function testMaximumLineLengthCanBeSpecified()
    {
        $input =
        'abcdefghijklmnopqrstuvwxyz'.
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
        '1234567890'.
        'abcdefghijklmnopqrstuvwxyz'.
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
        '1234567890'.
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $output =
        'YWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXpBQk'.//38
        'NERUZHSElKS0'."\r\n".//50 *
        'xNTk9QUVJTVFVWV1hZWjEyMzQ1Njc4OTBhYmNk'.//38
        'ZWZnaGlqa2xt'."\r\n".//50 *
        'bm9wcXJzdHV2d3h5ekFCQ0RFRkdISUpLTE1OT1'.//38
        'BRUlNUVVZXWF'."\r\n".//50 *
        'laMTIzNDU2Nzg5MEFCQ0RFRkdISUpLTE1OT1BR'.//38
        'UlNUVVZXWFla';                                     //50 *

        $this->assertEquals(
            $output, $this->_encoder->encodeString($input, 0, 50),
            '%s: Lines should be no more than 100 characters'
            );
    }

    public function testFirstLineLengthCanBeDifferent()
    {
        $input =
        'abcdefghijklmnopqrstuvwxyz'.
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
        '1234567890'.
        'abcdefghijklmnopqrstuvwxyz'.
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
        '1234567890'.
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $output =
        'YWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXpBQk'.//38
        'NERUZHSElKS0xNTk9QU'."\r\n".//57 *
        'VJTVFVWV1hZWjEyMzQ1Njc4OTBhYmNkZWZnaGl'.//38
        'qa2xtbm9wcXJzdHV2d3h5ekFCQ0RFRkdISUpLT'."\r\n".//76 *
        'E1OT1BRUlNUVVZXWFlaMTIzNDU2Nzg5MEFCQ0R'.//38
        'FRkdISUpLTE1OT1BRUlNUVVZXWFla';                    //67

        $this->assertEquals(
            $output, $this->_encoder->encodeString($input, 19),
            '%s: First line offset is 19 so first line should be 57 chars long'
            );
    }
}
