<?php

class Swift_Mime_SimpleHeaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_factory;

    public function setUp()
    {
        $this->_factory = $this->_createFactory();
    }

    public function testMailboxHeaderIsCorrectType()
    {
        $header = $this->_factory->createMailboxHeader('X-Foo');
        $this->assertInstanceof('Swift_Mime_Headers_MailboxHeader', $header);
    }

    public function testMailboxHeaderHasCorrectName()
    {
        $header = $this->_factory->createMailboxHeader('X-Foo');
        $this->assertEquals('X-Foo', $header->getFieldName());
    }

    public function testMailboxHeaderHasCorrectModel()
    {
        $header = $this->_factory->createMailboxHeader('X-Foo',
            array('foo@bar' => 'FooBar')
            );
        $this->assertEquals(array('foo@bar' => 'FooBar'), $header->getFieldBodyModel());
    }

    public function testDateHeaderHasCorrectType()
    {
        $header = $this->_factory->createDateHeader('X-Date');
        $this->assertInstanceof('Swift_Mime_Headers_DateHeader', $header);
    }

    public function testDateHeaderHasCorrectName()
    {
        $header = $this->_factory->createDateHeader('X-Date');
        $this->assertEquals('X-Date', $header->getFieldName());
    }

    public function testDateHeaderHasCorrectModel()
    {
        $header = $this->_factory->createDateHeader('X-Date', 123);
        $this->assertEquals(123, $header->getFieldBodyModel());
    }

    public function testTextHeaderHasCorrectType()
    {
        $header = $this->_factory->createTextHeader('X-Foo');
        $this->assertInstanceof('Swift_Mime_Headers_UnstructuredHeader', $header);
    }

    public function testTextHeaderHasCorrectName()
    {
        $header = $this->_factory->createTextHeader('X-Foo');
        $this->assertEquals('X-Foo', $header->getFieldName());
    }

    public function testTextHeaderHasCorrectModel()
    {
        $header = $this->_factory->createTextHeader('X-Foo', 'bar');
        $this->assertEquals('bar', $header->getFieldBodyModel());
    }

    public function testParameterizedHeaderHasCorrectType()
    {
        $header = $this->_factory->createParameterizedHeader('X-Foo');
        $this->assertInstanceof('Swift_Mime_Headers_ParameterizedHeader', $header);
    }

    public function testParameterizedHeaderHasCorrectName()
    {
        $header = $this->_factory->createParameterizedHeader('X-Foo');
        $this->assertEquals('X-Foo', $header->getFieldName());
    }

    public function testParameterizedHeaderHasCorrectModel()
    {
        $header = $this->_factory->createParameterizedHeader('X-Foo', 'bar');
        $this->assertEquals('bar', $header->getFieldBodyModel());
    }

    public function testParameterizedHeaderHasCorrectParams()
    {
        $header = $this->_factory->createParameterizedHeader('X-Foo', 'bar',
            array('zip' => 'button')
            );
        $this->assertEquals(array('zip' => 'button'), $header->getParameters());
    }

    public function testIdHeaderHasCorrectType()
    {
        $header = $this->_factory->createIdHeader('X-ID');
        $this->assertInstanceof('Swift_Mime_Headers_IdentificationHeader', $header);
    }

    public function testIdHeaderHasCorrectName()
    {
        $header = $this->_factory->createIdHeader('X-ID');
        $this->assertEquals('X-ID', $header->getFieldName());
    }

    public function testIdHeaderHasCorrectModel()
    {
        $header = $this->_factory->createIdHeader('X-ID', 'xyz@abc');
        $this->assertEquals(array('xyz@abc'), $header->getFieldBodyModel());
    }

    public function testPathHeaderHasCorrectType()
    {
        $header = $this->_factory->createPathHeader('X-Path');
        $this->assertInstanceof('Swift_Mime_Headers_PathHeader', $header);
    }

    public function testPathHeaderHasCorrectName()
    {
        $header = $this->_factory->createPathHeader('X-Path');
        $this->assertEquals('X-Path', $header->getFieldName());
    }

    public function testPathHeaderHasCorrectModel()
    {
        $header = $this->_factory->createPathHeader('X-Path', 'foo@bar');
        $this->assertEquals('foo@bar', $header->getFieldBodyModel());
    }

    public function testCharsetChangeNotificationNotifiesEncoders()
    {
        $encoder = $this->_createHeaderEncoder();
        $encoder->expects($this->once())
                ->method('charsetChanged')
                ->with('utf-8');
        $paramEncoder = $this->_createParamEncoder();
        $paramEncoder->expects($this->once())
                     ->method('charsetChanged')
                     ->with('utf-8');

        $factory = $this->_createFactory($encoder, $paramEncoder);

        $factory->charsetChanged('utf-8');
    }

    // -- Creation methods

    private function _createFactory($encoder = null, $paramEncoder = null)
    {
        return new Swift_Mime_SimpleHeaderFactory(
            $encoder
                ? $encoder : $this->_createHeaderEncoder(),
            $paramEncoder
                ? $paramEncoder : $this->_createParamEncoder(),
            new Swift_Mime_Grammar()
            );
    }

    private function _createHeaderEncoder()
    {
        return $this->getMock('Swift_Mime_HeaderEncoder');
    }

    private function _createParamEncoder()
    {
        return $this->getMock('Swift_Encoder');
    }
}
