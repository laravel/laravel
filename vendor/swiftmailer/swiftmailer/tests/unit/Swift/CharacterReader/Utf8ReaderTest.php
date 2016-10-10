<?php

class Swift_CharacterReader_Utf8ReaderTest extends \PHPUnit_Framework_TestCase
{
    private $_reader;

    public function setUp()
    {
        $this->_reader = new Swift_CharacterReader_Utf8Reader();
    }

    public function testLeading7BitOctetCausesReturnZero()
    {
        for ($ordinal = 0x00; $ordinal <= 0x7F; ++$ordinal) {
            $this->assertSame(
                0, $this->_reader->validateByteSequence(array($ordinal), 1)
                );
        }
    }

    public function testLeadingByteOf2OctetCharCausesReturn1()
    {
        for ($octet = 0xC0; $octet <= 0xDF; ++$octet) {
            $this->assertSame(
                1, $this->_reader->validateByteSequence(array($octet), 1)
                );
        }
    }

    public function testLeadingByteOf3OctetCharCausesReturn2()
    {
        for ($octet = 0xE0; $octet <= 0xEF; ++$octet) {
            $this->assertSame(
                2, $this->_reader->validateByteSequence(array($octet), 1)
                );
        }
    }

    public function testLeadingByteOf4OctetCharCausesReturn3()
    {
        for ($octet = 0xF0; $octet <= 0xF7; ++$octet) {
            $this->assertSame(
                3, $this->_reader->validateByteSequence(array($octet), 1)
                );
        }
    }

    public function testLeadingByteOf5OctetCharCausesReturn4()
    {
        for ($octet = 0xF8; $octet <= 0xFB; ++$octet) {
            $this->assertSame(
                4, $this->_reader->validateByteSequence(array($octet), 1)
                );
        }
    }

    public function testLeadingByteOf6OctetCharCausesReturn5()
    {
        for ($octet = 0xFC; $octet <= 0xFD; ++$octet) {
            $this->assertSame(
                5, $this->_reader->validateByteSequence(array($octet), 1)
                );
        }
    }
}
