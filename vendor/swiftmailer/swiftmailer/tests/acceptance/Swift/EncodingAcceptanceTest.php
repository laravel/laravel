<?php

require_once 'swift_required.php';

class Swift_EncodingAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    public function testGet7BitEncodingReturns7BitEncoder()
    {
        $encoder = Swift_Encoding::get7BitEncoding();
        $this->assertEquals('7bit', $encoder->getName());
    }

    public function testGet8BitEncodingReturns8BitEncoder()
    {
        $encoder = Swift_Encoding::get8BitEncoding();
        $this->assertEquals('8bit', $encoder->getName());
    }

    public function testGetQpEncodingReturnsQpEncoder()
    {
        $encoder = Swift_Encoding::getQpEncoding();
        $this->assertEquals('quoted-printable', $encoder->getName());
    }

    public function testGetBase64EncodingReturnsBase64Encoder()
    {
        $encoder = Swift_Encoding::getBase64Encoding();
        $this->assertEquals('base64', $encoder->getName());
    }
}
