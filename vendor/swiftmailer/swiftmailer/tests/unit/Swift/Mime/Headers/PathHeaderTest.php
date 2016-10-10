<?php

class Swift_Mime_Headers_PathHeaderTest extends \PHPUnit_Framework_TestCase
{
    public function testTypeIsPathHeader()
    {
        $header = $this->_getHeader('Return-Path');
        $this->assertEquals(Swift_Mime_Header::TYPE_PATH, $header->getFieldType());
    }

    public function testSingleAddressCanBeSetAndFetched()
    {
        $header = $this->_getHeader('Return-Path');
        $header->setAddress('chris@swiftmailer.org');
        $this->assertEquals('chris@swiftmailer.org', $header->getAddress());
    }

    public function testAddressMustComplyWithRfc2822()
    {
        try {
            $header = $this->_getHeader('Return-Path');
            $header->setAddress('chr is@swiftmailer.org');
            $this->fail('Addresses not valid according to RFC 2822 addr-spec grammar must be rejected.');
        } catch (Exception $e) {
        }
    }

    public function testValueIsAngleAddrWithValidAddress()
    {
        /* -- RFC 2822, 3.6.7.

            return          =       "Return-Path:" path CRLF

            path            =       ([CFWS] "<" ([CFWS] / addr-spec) ">" [CFWS]) /
                                                            obs-path
     */

        $header = $this->_getHeader('Return-Path');
        $header->setAddress('chris@swiftmailer.org');
        $this->assertEquals('<chris@swiftmailer.org>', $header->getFieldBody());
    }

    public function testValueIsEmptyAngleBracketsIfEmptyAddressSet()
    {
        $header = $this->_getHeader('Return-Path');
        $header->setAddress('');
        $this->assertEquals('<>', $header->getFieldBody());
    }

    public function testSetBodyModel()
    {
        $header = $this->_getHeader('Return-Path');
        $header->setFieldBodyModel('foo@bar.tld');
        $this->assertEquals('foo@bar.tld', $header->getAddress());
    }

    public function testGetBodyModel()
    {
        $header = $this->_getHeader('Return-Path');
        $header->setAddress('foo@bar.tld');
        $this->assertEquals('foo@bar.tld', $header->getFieldBodyModel());
    }

    public function testToString()
    {
        $header = $this->_getHeader('Return-Path');
        $header->setAddress('chris@swiftmailer.org');
        $this->assertEquals('Return-Path: <chris@swiftmailer.org>'."\r\n",
            $header->toString()
            );
    }

    private function _getHeader($name)
    {
        return new Swift_Mime_Headers_PathHeader($name, new Swift_Mime_Grammar());
    }
}
