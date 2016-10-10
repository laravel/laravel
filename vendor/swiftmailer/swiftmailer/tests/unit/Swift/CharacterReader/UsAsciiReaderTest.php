<?php

class Swift_CharacterReader_UsAsciiReaderTest extends \PHPUnit_Framework_TestCase
{
    /*

    for ($c = '', $size = 1; false !== $bytes = $os->read($size); ) {
        $c .= $bytes;
        $size = $v->validateCharacter($c);
        if (-1 == $size) {
            throw new Exception( ... invalid char .. );
        } elseif (0 == $size) {
            return $c; //next character in $os
        }
    }

    */

    private $_reader;

    public function setUp()
    {
        $this->_reader = new Swift_CharacterReader_UsAsciiReader();
    }

    public function testAllValidAsciiCharactersReturnZero()
    {
        for ($ordinal = 0x00; $ordinal <= 0x7F; ++$ordinal) {
            $this->assertSame(
                0, $this->_reader->validateByteSequence(array($ordinal), 1)
                );
        }
    }

    public function testMultipleBytesAreInvalid()
    {
        for ($ordinal = 0x00; $ordinal <= 0x7F; $ordinal += 2) {
            $this->assertSame(
                -1, $this->_reader->validateByteSequence(array($ordinal, $ordinal + 1), 2)
                );
        }
    }

    public function testBytesAboveAsciiRangeAreInvalid()
    {
        for ($ordinal = 0x80; $ordinal <= 0xFF; ++$ordinal) {
            $this->assertSame(
                -1, $this->_reader->validateByteSequence(array($ordinal), 1)
                );
        }
    }
}
