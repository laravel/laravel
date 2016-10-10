<?php

class Swift_Mime_MimePartAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    private $_contentEncoder;
    private $_cache;
    private $_grammar;
    private $_headers;

    public function setUp()
    {
        $this->_cache = new Swift_KeyCache_ArrayKeyCache(
            new Swift_KeyCache_SimpleKeyCacheInputStream()
            );
        $factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory();
        $this->_contentEncoder = new Swift_Mime_ContentEncoder_QpContentEncoder(
            new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8'),
            new Swift_StreamFilters_ByteArrayReplacementFilter(
                array(array(0x0D, 0x0A), array(0x0D), array(0x0A)),
                array(array(0x0A), array(0x0A), array(0x0D, 0x0A))
                )
            );

        $headerEncoder = new Swift_Mime_HeaderEncoder_QpHeaderEncoder(
            new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8')
            );
        $paramEncoder = new Swift_Encoder_Rfc2231Encoder(
            new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8')
            );
        $this->_grammar = new Swift_Mime_Grammar();
        $this->_headers = new Swift_Mime_SimpleHeaderSet(
            new Swift_Mime_SimpleHeaderFactory($headerEncoder, $paramEncoder, $this->_grammar)
            );
    }

    public function testCharsetIsSetInHeader()
    {
        $part = $this->_createMimePart();
        $part->setContentType('text/plain');
        $part->setCharset('utf-8');
        $part->setBody('foobar');
        $this->assertEquals(
            'Content-Type: text/plain; charset=utf-8'."\r\n".
            'Content-Transfer-Encoding: quoted-printable'."\r\n".
            "\r\n".
            'foobar',
            $part->toString()
            );
    }

    public function testFormatIsSetInHeaders()
    {
        $part = $this->_createMimePart();
        $part->setContentType('text/plain');
        $part->setFormat('flowed');
        $part->setBody('> foobar');
        $this->assertEquals(
            'Content-Type: text/plain; format=flowed'."\r\n".
            'Content-Transfer-Encoding: quoted-printable'."\r\n".
            "\r\n".
            '> foobar',
            $part->toString()
            );
    }

    public function testDelSpIsSetInHeaders()
    {
        $part = $this->_createMimePart();
        $part->setContentType('text/plain');
        $part->setDelSp(true);
        $part->setBody('foobar');
        $this->assertEquals(
            'Content-Type: text/plain; delsp=yes'."\r\n".
            'Content-Transfer-Encoding: quoted-printable'."\r\n".
            "\r\n".
            'foobar',
            $part->toString()
            );
    }

    public function testAll3ParamsInHeaders()
    {
        $part = $this->_createMimePart();
        $part->setContentType('text/plain');
        $part->setCharset('utf-8');
        $part->setFormat('fixed');
        $part->setDelSp(true);
        $part->setBody('foobar');
        $this->assertEquals(
            'Content-Type: text/plain; charset=utf-8; format=fixed; delsp=yes'."\r\n".
            'Content-Transfer-Encoding: quoted-printable'."\r\n".
            "\r\n".
            'foobar',
            $part->toString()
            );
    }

    public function testBodyIsCanonicalized()
    {
        $part = $this->_createMimePart();
        $part->setContentType('text/plain');
        $part->setCharset('utf-8');
        $part->setBody("foobar\r\rtest\ning\r");
        $this->assertEquals(
            'Content-Type: text/plain; charset=utf-8'."\r\n".
            'Content-Transfer-Encoding: quoted-printable'."\r\n".
            "\r\n".
            "foobar\r\n".
            "\r\n".
            "test\r\n".
            "ing\r\n",
            $part->toString()
            );
    }

    // -- Private helpers

    protected function _createMimePart()
    {
        $entity = new Swift_Mime_MimePart(
            $this->_headers,
            $this->_contentEncoder,
            $this->_cache,
            $this->_grammar
            );

        return $entity;
    }
}
