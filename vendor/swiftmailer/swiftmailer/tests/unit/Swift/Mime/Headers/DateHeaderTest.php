<?php

class Swift_Mime_Headers_DateHeaderTest extends \PHPUnit_Framework_TestCase
{
    /* --
    The following tests refer to RFC 2822, section 3.6.1 and 3.3.
    */

    public function testTypeIsDateHeader()
    {
        $header = $this->_getHeader('Date');
        $this->assertEquals(Swift_Mime_Header::TYPE_DATE, $header->getFieldType());
    }

    public function testGetTimestamp()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertSame($timestamp, $header->getTimestamp());
    }

    public function testTimestampCanBeSetBySetter()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertSame($timestamp, $header->getTimestamp());
    }

    public function testIntegerTimestampIsConvertedToRfc2822Date()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertEquals(date('r', $timestamp), $header->getFieldBody());
    }

    public function testSetBodyModel()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setFieldBodyModel($timestamp);
        $this->assertEquals(date('r', $timestamp), $header->getFieldBody());
    }

    public function testGetBodyModel()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertEquals($timestamp, $header->getFieldBodyModel());
    }

    public function testToString()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertEquals('Date: '.date('r', $timestamp)."\r\n",
            $header->toString()
            );
    }

    private function _getHeader($name)
    {
        return new Swift_Mime_Headers_DateHeader($name, new Swift_Mime_Grammar());
    }
}
