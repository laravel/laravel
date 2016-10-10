<?php

class Swift_Bug206Test extends \PHPUnit_Framework_TestCase
{
    private $_factory;

    public function setUp()
    {
        $factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory();
        $headerEncoder = new Swift_Mime_HeaderEncoder_QpHeaderEncoder(
            new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8')
        );
        $paramEncoder = new Swift_Encoder_Rfc2231Encoder(
            new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8')
        );
        $grammar = new Swift_Mime_Grammar();
        $this->_factory = new Swift_Mime_SimpleHeaderFactory($headerEncoder, $paramEncoder, $grammar);
    }

    public function testMailboxHeaderEncoding()
    {
        $this->_testHeaderIsFullyEncoded('email@example.org', 'Family Name, Name', ' "Family Name, Name" <email@example.org>');
        $this->_testHeaderIsFullyEncoded('email@example.org', 'Family Namé, Name', ' Family =?utf-8?Q?Nam=C3=A9=2C?= Name');
        $this->_testHeaderIsFullyEncoded('email@example.org', 'Family Namé , Name', ' Family =?utf-8?Q?Nam=C3=A9_=2C?= Name');
        $this->_testHeaderIsFullyEncoded('email@example.org', 'Family Namé ;Name', ' Family =?utf-8?Q?Nam=C3=A9_=3BName?= ');
    }

    private function _testHeaderIsFullyEncoded($email, $name, $expected)
    {
        $mailboxHeader = $this->_factory->createMailboxHeader('To', array(
            $email => $name,
        ));

        $headerBody = substr($mailboxHeader->toString(), 3, strlen($expected));

        $this->assertEquals($expected, $headerBody);
    }
}
