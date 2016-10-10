<?php

class Swift_Mime_HeaderEncoder_Base64HeaderEncoderAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    private $_encoder;

    public function setUp()
    {
        $this->_encoder = new Swift_Mime_HeaderEncoder_Base64HeaderEncoder();
    }

    public function testEncodingJIS()
    {
        if (function_exists('mb_convert_encoding')) {
            // base64_encode and split cannot handle long JIS text to fold
            $subject = '長い長い長い長い長い長い長い長い長い長い長い長い長い長い長い長い長い長い長い長い件名';

            $encodedWrapperLength = strlen('=?iso-2022-jp?'.$this->_encoder->getName().'??=');

            $old = mb_internal_encoding();
            mb_internal_encoding('utf-8');
            $newstring = mb_encode_mimeheader($subject, 'iso-2022-jp', 'B', "\r\n");
            mb_internal_encoding($old);

            $encoded = $this->_encoder->encodeString($subject, 0, 75 - $encodedWrapperLength, 'iso-2022-jp');
            $this->assertEquals(
                $encoded, $newstring,
                'Encoded string should decode back to original string for sample '
            );
        }
    }
}
