<?php

class Swift_Mime_Headers_ParameterizedHeaderTest extends \SwiftMailerTestCase
{
    private $_charset = 'utf-8';
    private $_lang = 'en-us';

    public function testTypeIsParameterizedHeader()
    {
        $header = $this->_getHeader('Content-Type',
            $this->_getHeaderEncoder('Q', true), $this->_getParameterEncoder(true)
            );
        $this->assertEquals(Swift_Mime_Header::TYPE_PARAMETERIZED, $header->getFieldType());
    }

    public function testValueIsReturnedVerbatim()
    {
        $header = $this->_getHeader('Content-Type',
            $this->_getHeaderEncoder('Q', true), $this->_getParameterEncoder(true)
            );
        $header->setValue('text/plain');
        $this->assertEquals('text/plain', $header->getValue());
    }

    public function testParametersAreAppended()
    {
        /* -- RFC 2045, 5.1
        parameter := attribute "=" value

     attribute := token
                                    ; Matching of attributes
                                    ; is ALWAYS case-insensitive.

     value := token / quoted-string

     token := 1*<any (US-ASCII) CHAR except SPACE, CTLs,
                 or tspecials>

     tspecials :=  "(" / ")" / "<" / ">" / "@" /
                   "," / ";" / ":" / "\" / <">
                   "/" / "[" / "]" / "?" / "="
                   ; Must be in quoted-string,
                   ; to use within parameter values
        */

        $header = $this->_getHeader('Content-Type',
            $this->_getHeaderEncoder('Q', true), $this->_getParameterEncoder(true)
            );
        $header->setValue('text/plain');
        $header->setParameters(array('charset' => 'utf-8'));
        $this->assertEquals('text/plain; charset=utf-8', $header->getFieldBody());
    }

    public function testSpaceInParamResultsInQuotedString()
    {
        $header = $this->_getHeader('Content-Disposition',
            $this->_getHeaderEncoder('Q', true), $this->_getParameterEncoder(true)
            );
        $header->setValue('attachment');
        $header->setParameters(array('filename' => 'my file.txt'));
        $this->assertEquals('attachment; filename="my file.txt"',
            $header->getFieldBody()
            );
    }

    public function testLongParamsAreBrokenIntoMultipleAttributeStrings()
    {
        /* -- RFC 2231, 3.
        The asterisk character ("*") followed
        by a decimal count is employed to indicate that multiple parameters
        are being used to encapsulate a single parameter value.  The count
        starts at 0 and increments by 1 for each subsequent section of the
        parameter value.  Decimal values are used and neither leading zeroes
        nor gaps in the sequence are allowed.

        The original parameter value is recovered by concatenating the
        various sections of the parameter, in order.  For example, the
        content-type field

                Content-Type: message/external-body; access-type=URL;
         URL*0="ftp://";
         URL*1="cs.utk.edu/pub/moore/bulk-mailer/bulk-mailer.tar"

        is semantically identical to

                Content-Type: message/external-body; access-type=URL;
                    URL="ftp://cs.utk.edu/pub/moore/bulk-mailer/bulk-mailer.tar"

        Note that quotes around parameter values are part of the value
        syntax; they are NOT part of the value itself.  Furthermore, it is
        explicitly permitted to have a mixture of quoted and unquoted
        continuation fields.
        */

        $value = str_repeat('a', 180);

        $encoder = $this->_getParameterEncoder();
        $encoder->shouldReceive('encodeString')
                ->once()
                ->with($value, \Mockery::any(), 63, \Mockery::any())
                ->andReturn(str_repeat('a', 63)."\r\n".
                    str_repeat('a', 63)."\r\n".str_repeat('a', 54));

        $header = $this->_getHeader('Content-Disposition',
            $this->_getHeaderEncoder('Q', true), $encoder
            );
        $header->setValue('attachment');
        $header->setParameters(array('filename' => $value));
        $header->setMaxLineLength(78);
        $this->assertEquals(
            'attachment; '.
            'filename*0*=utf-8\'\''.str_repeat('a', 63).";\r\n ".
            'filename*1*='.str_repeat('a', 63).";\r\n ".
            'filename*2*='.str_repeat('a', 54),
            $header->getFieldBody()
            );
    }

    public function testEncodedParamDataIncludesCharsetAndLanguage()
    {
        /* -- RFC 2231, 4.
        Asterisks ("*") are reused to provide the indicator that language and
        character set information is present and encoding is being used. A
        single quote ("'") is used to delimit the character set and language
        information at the beginning of the parameter value. Percent signs
        ("%") are used as the encoding flag, which agrees with RFC 2047.

        Specifically, an asterisk at the end of a parameter name acts as an
        indicator that character set and language information may appear at
        the beginning of the parameter value. A single quote is used to
        separate the character set, language, and actual value information in
        the parameter value string, and an percent sign is used to flag
        octets encoded in hexadecimal.  For example:

                Content-Type: application/x-stuff;
         title*=us-ascii'en-us'This%20is%20%2A%2A%2Afun%2A%2A%2A

        Note that it is perfectly permissible to leave either the character
        set or language field blank.  Note also that the single quote
        delimiters MUST be present even when one of the field values is
        omitted.
        */

        $value = str_repeat('a', 20).pack('C', 0x8F).str_repeat('a', 10);

        $encoder = $this->_getParameterEncoder();
        $encoder->shouldReceive('encodeString')
                ->once()
                ->with($value, 12, 62, \Mockery::any())
                ->andReturn(str_repeat('a', 20).'%8F'.str_repeat('a', 10));

        $header = $this->_getHeader('Content-Disposition',
            $this->_getHeaderEncoder('Q', true), $encoder
            );
        $header->setValue('attachment');
        $header->setParameters(array('filename' => $value));
        $header->setMaxLineLength(78);
        $header->setLanguage($this->_lang);
        $this->assertEquals(
            'attachment; filename*='.$this->_charset."'".$this->_lang."'".
            str_repeat('a', 20).'%8F'.str_repeat('a', 10),
            $header->getFieldBody()
            );
    }

    public function testMultipleEncodedParamLinesAreFormattedCorrectly()
    {
        /* -- RFC 2231, 4.1.
        Character set and language information may be combined with the
        parameter continuation mechanism. For example:

        Content-Type: application/x-stuff
     title*0*=us-ascii'en'This%20is%20even%20more%20
     title*1*=%2A%2A%2Afun%2A%2A%2A%20
     title*2="isn't it!"

        Note that:

     (1)   Language and character set information only appear at
           the beginning of a given parameter value.

     (2)   Continuations do not provide a facility for using more
           than one character set or language in the same
           parameter value.

     (3)   A value presented using multiple continuations may
           contain a mixture of encoded and unencoded segments.

     (4)   The first segment of a continuation MUST be encoded if
           language and character set information are given.

     (5)   If the first segment of a continued parameter value is
           encoded the language and character set field delimiters
           MUST be present even when the fields are left blank.
        */

        $value = str_repeat('a', 20).pack('C', 0x8F).str_repeat('a', 60);

        $encoder = $this->_getParameterEncoder();
        $encoder->shouldReceive('encodeString')
                ->once()
                ->with($value, 12, 62, \Mockery::any())
                ->andReturn(str_repeat('a', 20).'%8F'.str_repeat('a', 28)."\r\n".
                    str_repeat('a', 32));

        $header = $this->_getHeader('Content-Disposition',
            $this->_getHeaderEncoder('Q', true), $encoder
            );
        $header->setValue('attachment');
        $header->setParameters(array('filename' => $value));
        $header->setMaxLineLength(78);
        $header->setLanguage($this->_lang);
        $this->assertEquals(
            'attachment; filename*0*='.$this->_charset."'".$this->_lang."'".
            str_repeat('a', 20).'%8F'.str_repeat('a', 28).";\r\n ".
            'filename*1*='.str_repeat('a', 32),
            $header->getFieldBody()
            );
    }

    public function testToString()
    {
        $header = $this->_getHeader('Content-Type',
            $this->_getHeaderEncoder('Q', true), $this->_getParameterEncoder(true)
            );
        $header->setValue('text/html');
        $header->setParameters(array('charset' => 'utf-8'));
        $this->assertEquals('Content-Type: text/html; charset=utf-8'."\r\n",
            $header->toString()
            );
    }

    public function testValueCanBeEncodedIfNonAscii()
    {
        $value = 'fo'.pack('C', 0x8F).'bar';

        $encoder = $this->_getHeaderEncoder('Q');
        $encoder->shouldReceive('encodeString')
                ->once()
                ->with($value, \Mockery::any(), \Mockery::any(), \Mockery::any())
                ->andReturn('fo=8Fbar');

        $header = $this->_getHeader('X-Foo', $encoder, $this->_getParameterEncoder(true));
        $header->setValue($value);
        $header->setParameters(array('lookslike' => 'foobar'));
        $this->assertEquals('X-Foo: =?utf-8?Q?fo=8Fbar?=; lookslike=foobar'."\r\n",
            $header->toString()
            );
    }

    public function testValueAndParamCanBeEncodedIfNonAscii()
    {
        $value = 'fo'.pack('C', 0x8F).'bar';

        $encoder = $this->_getHeaderEncoder('Q');
        $encoder->shouldReceive('encodeString')
                ->once()
                ->with($value, \Mockery::any(), \Mockery::any(), \Mockery::any())
                ->andReturn('fo=8Fbar');

        $paramEncoder = $this->_getParameterEncoder();
        $paramEncoder->shouldReceive('encodeString')
                     ->once()
                     ->with($value, \Mockery::any(), \Mockery::any(), \Mockery::any())
                     ->andReturn('fo%8Fbar');

        $header = $this->_getHeader('X-Foo', $encoder, $paramEncoder);
        $header->setValue($value);
        $header->setParameters(array('says' => $value));
        $this->assertEquals("X-Foo: =?utf-8?Q?fo=8Fbar?=; says*=utf-8''fo%8Fbar\r\n",
            $header->toString()
            );
    }

    public function testParamsAreEncodedWithEncodedWordsIfNoParamEncoderSet()
    {
        $value = 'fo'.pack('C', 0x8F).'bar';

        $encoder = $this->_getHeaderEncoder('Q');
        $encoder->shouldReceive('encodeString')
                ->once()
                ->with($value, \Mockery::any(), \Mockery::any(), \Mockery::any())
                ->andReturn('fo=8Fbar');

        $header = $this->_getHeader('X-Foo', $encoder, null);
        $header->setValue('bar');
        $header->setParameters(array('says' => $value));
        $this->assertEquals("X-Foo: bar; says=\"=?utf-8?Q?fo=8Fbar?=\"\r\n",
            $header->toString()
            );
    }

    public function testLanguageInformationAppearsInEncodedWords()
    {
        /* -- RFC 2231, 5.
        5.  Language specification in Encoded Words

        RFC 2047 provides support for non-US-ASCII character sets in RFC 822
        message header comments, phrases, and any unstructured text field.
        This is done by defining an encoded word construct which can appear
        in any of these places.  Given that these are fields intended for
        display, it is sometimes necessary to associate language information
        with encoded words as well as just the character set.  This
        specification extends the definition of an encoded word to allow the
        inclusion of such information.  This is simply done by suffixing the
        character set specification with an asterisk followed by the language
        tag.  For example:

                    From: =?US-ASCII*EN?Q?Keith_Moore?= <moore@cs.utk.edu>
        */

        $value = 'fo'.pack('C', 0x8F).'bar';

        $encoder = $this->_getHeaderEncoder('Q');
        $encoder->shouldReceive('encodeString')
                ->once()
                ->with($value, \Mockery::any(), \Mockery::any(), \Mockery::any())
                ->andReturn('fo=8Fbar');

        $paramEncoder = $this->_getParameterEncoder();
        $paramEncoder->shouldReceive('encodeString')
                     ->once()
                     ->with($value, \Mockery::any(), \Mockery::any(), \Mockery::any())
                     ->andReturn('fo%8Fbar');

        $header = $this->_getHeader('X-Foo', $encoder, $paramEncoder);
        $header->setLanguage('en');
        $header->setValue($value);
        $header->setParameters(array('says' => $value));
        $this->assertEquals("X-Foo: =?utf-8*en?Q?fo=8Fbar?=; says*=utf-8'en'fo%8Fbar\r\n",
            $header->toString()
            );
    }

    public function testSetBodyModel()
    {
        $header = $this->_getHeader('Content-Type',
            $this->_getHeaderEncoder('Q', true), $this->_getParameterEncoder(true)
            );
        $header->setFieldBodyModel('text/html');
        $this->assertEquals('text/html', $header->getValue());
    }

    public function testGetBodyModel()
    {
        $header = $this->_getHeader('Content-Type',
            $this->_getHeaderEncoder('Q', true), $this->_getParameterEncoder(true)
            );
        $header->setValue('text/plain');
        $this->assertEquals('text/plain', $header->getFieldBodyModel());
    }

    public function testSetParameter()
    {
        $header = $this->_getHeader('Content-Type',
            $this->_getHeaderEncoder('Q', true), $this->_getParameterEncoder(true)
            );
        $header->setParameters(array('charset' => 'utf-8', 'delsp' => 'yes'));
        $header->setParameter('delsp', 'no');
        $this->assertEquals(array('charset' => 'utf-8', 'delsp' => 'no'),
            $header->getParameters()
            );
    }

    public function testGetParameter()
    {
        $header = $this->_getHeader('Content-Type',
            $this->_getHeaderEncoder('Q', true), $this->_getParameterEncoder(true)
            );
        $header->setParameters(array('charset' => 'utf-8', 'delsp' => 'yes'));
        $this->assertEquals('utf-8', $header->getParameter('charset'));
    }

    // -- Private helper

    private function _getHeader($name, $encoder, $paramEncoder)
    {
        $header = new Swift_Mime_Headers_ParameterizedHeader($name, $encoder,
            $paramEncoder, new Swift_Mime_Grammar()
            );
        $header->setCharset($this->_charset);

        return $header;
    }

    private function _getHeaderEncoder($type, $stub = false)
    {
        $encoder = $this->getMockery('Swift_Mime_HeaderEncoder')->shouldIgnoreMissing();
        $encoder->shouldReceive('getName')
                ->zeroOrMoreTimes()
                ->andReturn($type);

        return $encoder;
    }

    private function _getParameterEncoder($stub = false)
    {
        return $this->getMockery('Swift_Encoder')->shouldIgnoreMissing();
    }
}
