<?php

class Swift_Mime_ContentEncoder_QpContentEncoderTest extends \SwiftMailerTestCase
{
    public function testNameIsQuotedPrintable()
    {
        $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder(
            $this->_createCharacterStream(true)
            );
        $this->assertEquals('quoted-printable', $encoder->getName());
    }

    /* -- RFC 2045, 6.7 --
    (1)   (General 8bit representation) Any octet, except a CR or
                    LF that is part of a CRLF line break of the canonical
                    (standard) form of the data being encoded, may be
                    represented by an "=" followed by a two digit
                    hexadecimal representation of the octet's value.  The
                    digits of the hexadecimal alphabet, for this purpose,
                    are "0123456789ABCDEF".  Uppercase letters must be
                    used; lowercase letters are not allowed.  Thus, for
                    example, the decimal value 12 (US-ASCII form feed) can
                    be represented by "=0C", and the decimal value 61 (US-
                    ASCII EQUAL SIGN) can be represented by "=3D".  This
                    rule must be followed except when the following rules
                    allow an alternative encoding.
                    */

    public function testPermittedCharactersAreNotEncoded()
    {
        /* -- RFC 2045, 6.7 --
        (2)   (Literal representation) Octets with decimal values of
                    33 through 60 inclusive, and 62 through 126, inclusive,
                    MAY be represented as the US-ASCII characters which
                    correspond to those octets (EXCLAMATION POINT through
                    LESS THAN, and GREATER THAN through TILDE,
                    respectively).
                    */

        foreach (array_merge(range(33, 60), range(62, 126)) as $ordinal) {
            $char = chr($ordinal);

            $os = $this->_createOutputByteStream(true);
            $charStream = $this->_createCharacterStream();
            $is = $this->_createInputByteStream();
            $collection = new Swift_StreamCollector();

            $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
            $charStream->shouldReceive('flushContents')
                       ->once();
            $charStream->shouldReceive('importByteStream')
                       ->once()
                       ->with($os);
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array($ordinal));
            $charStream->shouldReceive('readBytes')
                       ->zeroOrMoreTimes()
                       ->andReturn(false);

            $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);
            $encoder->encodeByteStream($os, $is);
            $this->assertIdenticalBinary($char, $collection->content);
        }
    }

    public function testLinearWhiteSpaceAtLineEndingIsEncoded()
    {
        /* -- RFC 2045, 6.7 --
        (3)   (White Space) Octets with values of 9 and 32 MAY be
                    represented as US-ASCII TAB (HT) and SPACE characters,
                    respectively, but MUST NOT be so represented at the end
                    of an encoded line.  Any TAB (HT) or SPACE characters
                    on an encoded line MUST thus be followed on that line
                    by a printable character.  In particular, an "=" at the
                    end of an encoded line, indicating a soft line break
                    (see rule #5) may follow one or more TAB (HT) or SPACE
                    characters.  It follows that an octet with decimal
                    value 9 or 32 appearing at the end of an encoded line
                    must be represented according to Rule #1.  This rule is
                    necessary because some MTAs (Message Transport Agents,
                    programs which transport messages from one user to
                    another, or perform a portion of such transfers) are
                    known to pad lines of text with SPACEs, and others are
                    known to remove "white space" characters from the end
                    of a line.  Therefore, when decoding a Quoted-Printable
                    body, any trailing white space on a line must be
                    deleted, as it will necessarily have been added by
                    intermediate transport agents.
                    */

        $HT = chr(0x09); //9
        $SPACE = chr(0x20); //32

        //HT
        $os = $this->_createOutputByteStream(true);
        $charStream = $this->_createCharacterStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
        $charStream->shouldReceive('flushContents')
                   ->once();
        $charStream->shouldReceive('importByteStream')
                   ->once()
                   ->with($os);
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(ord('a')));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x09));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x09));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x0D));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x0A));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(ord('b')));
        $charStream->shouldReceive('readBytes')
                   ->zeroOrMoreTimes()
                   ->andReturn(false);

        $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);
        $encoder->encodeByteStream($os, $is);

        $this->assertEquals("a\t=09\r\nb", $collection->content);

        //SPACE
        $os = $this->_createOutputByteStream(true);
        $charStream = $this->_createCharacterStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
        $charStream->shouldReceive('flushContents')
                   ->once();
        $charStream->shouldReceive('importByteStream')
                   ->once()
                   ->with($os);
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(ord('a')));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x20));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x20));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x0D));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x0A));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(ord('b')));
        $charStream->shouldReceive('readBytes')
                   ->zeroOrMoreTimes()
                   ->andReturn(false);

        $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);
        $encoder->encodeByteStream($os, $is);

        $this->assertEquals("a =20\r\nb", $collection->content);
    }

    public function testCRLFIsLeftAlone()
    {
        /*
        (4)   (Line Breaks) A line break in a text body, represented
                    as a CRLF sequence in the text canonical form, must be
                    represented by a (RFC 822) line break, which is also a
                    CRLF sequence, in the Quoted-Printable encoding.  Since
                    the canonical representation of media types other than
                    text do not generally include the representation of
                    line breaks as CRLF sequences, no hard line breaks
                    (i.e. line breaks that are intended to be meaningful
                    and to be displayed to the user) can occur in the
                    quoted-printable encoding of such types.  Sequences
                    like "=0D", "=0A", "=0A=0D" and "=0D=0A" will routinely
                    appear in non-text data represented in quoted-
                    printable, of course.

                    Note that many implementations may elect to encode the
                    local representation of various content types directly
                    rather than converting to canonical form first,
                    encoding, and then converting back to local
                    representation.  In particular, this may apply to plain
                    text material on systems that use newline conventions
                    other than a CRLF terminator sequence.  Such an
                    implementation optimization is permissible, but only
                    when the combined canonicalization-encoding step is
                    equivalent to performing the three steps separately.
                    */

        $os = $this->_createOutputByteStream(true);
        $charStream = $this->_createCharacterStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
        $charStream->shouldReceive('flushContents')
                   ->once();
        $charStream->shouldReceive('importByteStream')
                   ->once()
                   ->with($os);
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(ord('a')));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x0D));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x0A));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(ord('b')));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x0D));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x0A));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(ord('c')));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x0D));
        $charStream->shouldReceive('readBytes')
                   ->once()
                   ->andReturn(array(0x0A));
        $charStream->shouldReceive('readBytes')
                   ->zeroOrMoreTimes()
                   ->andReturn(false);

        $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);
        $encoder->encodeByteStream($os, $is);
        $this->assertEquals("a\r\nb\r\nc\r\n", $collection->content);
    }

    public function testLinesLongerThan76CharactersAreSoftBroken()
    {
        /*
        (5)   (Soft Line Breaks) The Quoted-Printable encoding
                    REQUIRES that encoded lines be no more than 76
                    characters long.  If longer lines are to be encoded
                    with the Quoted-Printable encoding, "soft" line breaks
                    must be used.  An equal sign as the last character on a
                    encoded line indicates such a non-significant ("soft")
                    line break in the encoded text.
                    */

        $os = $this->_createOutputByteStream(true);
        $charStream = $this->_createCharacterStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
           ->zeroOrMoreTimes()
           ->andReturnUsing($collection);
        $charStream->shouldReceive('flushContents')
                   ->once();
        $charStream->shouldReceive('importByteStream')
                   ->once()
                   ->with($os);

        for ($seq = 0; $seq <= 140; ++$seq) {
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array(ord('a')));
        }
        $charStream->shouldReceive('readBytes')
                   ->zeroOrMoreTimes()
                   ->andReturn(false);

        $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);
        $encoder->encodeByteStream($os, $is);
        $this->assertEquals(str_repeat('a', 75)."=\r\n".str_repeat('a', 66), $collection->content);
    }

    public function testMaxLineLengthCanBeSpecified()
    {
        $os = $this->_createOutputByteStream(true);
        $charStream = $this->_createCharacterStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
           ->zeroOrMoreTimes()
           ->andReturnUsing($collection);
        $charStream->shouldReceive('flushContents')
                   ->once();
        $charStream->shouldReceive('importByteStream')
                   ->once()
                   ->with($os);

        for ($seq = 0; $seq <= 100; ++$seq) {
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array(ord('a')));
        }
        $charStream->shouldReceive('readBytes')
                   ->zeroOrMoreTimes()
                   ->andReturn(false);

        $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);
        $encoder->encodeByteStream($os, $is, 0, 54);
        $this->assertEquals(str_repeat('a', 53)."=\r\n".str_repeat('a', 48), $collection->content);
    }

    public function testBytesBelowPermittedRangeAreEncoded()
    {
        /*
        According to Rule (1 & 2)
        */

        foreach (range(0, 32) as $ordinal) {
            $char = chr($ordinal);

            $os = $this->_createOutputByteStream(true);
            $charStream = $this->_createCharacterStream();
            $is = $this->_createInputByteStream();
            $collection = new Swift_StreamCollector();

            $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
            $charStream->shouldReceive('flushContents')
                       ->once();
            $charStream->shouldReceive('importByteStream')
                       ->once()
                       ->with($os);
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array($ordinal));
            $charStream->shouldReceive('readBytes')
                       ->zeroOrMoreTimes()
                       ->andReturn(false);

            $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);
            $encoder->encodeByteStream($os, $is);
            $this->assertEquals(sprintf('=%02X', $ordinal), $collection->content);
        }
    }

    public function testDecimalByte61IsEncoded()
    {
        /*
        According to Rule (1 & 2)
        */

        $char = chr(61);

        $os = $this->_createOutputByteStream(true);
        $charStream = $this->_createCharacterStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
        $charStream->shouldReceive('flushContents')
                       ->once();
        $charStream->shouldReceive('importByteStream')
                       ->once()
                       ->with($os);
        $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array(61));
        $charStream->shouldReceive('readBytes')
                       ->zeroOrMoreTimes()
                       ->andReturn(false);

        $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);
        $encoder->encodeByteStream($os, $is);
        $this->assertEquals(sprintf('=%02X', 61), $collection->content);
    }

    public function testBytesAbovePermittedRangeAreEncoded()
    {
        /*
        According to Rule (1 & 2)
        */

        foreach (range(127, 255) as $ordinal) {
            $char = chr($ordinal);

            $os = $this->_createOutputByteStream(true);
            $charStream = $this->_createCharacterStream();
            $is = $this->_createInputByteStream();
            $collection = new Swift_StreamCollector();

            $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
            $charStream->shouldReceive('flushContents')
                       ->once();
            $charStream->shouldReceive('importByteStream')
                       ->once()
                       ->with($os);
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array($ordinal));
            $charStream->shouldReceive('readBytes')
                       ->zeroOrMoreTimes()
                       ->andReturn(false);

            $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);
            $encoder->encodeByteStream($os, $is);
            $this->assertEquals(sprintf('=%02X', $ordinal), $collection->content);
        }
    }

    public function testFirstLineLengthCanBeDifferent()
    {
        $os = $this->_createOutputByteStream(true);
        $charStream = $this->_createCharacterStream();
        $is = $this->_createInputByteStream();
        $collection = new Swift_StreamCollector();

        $is->shouldReceive('write')
               ->zeroOrMoreTimes()
               ->andReturnUsing($collection);
        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importByteStream')
                    ->once()
                    ->with($os);

        for ($seq = 0; $seq <= 140; ++$seq) {
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array(ord('a')));
        }
        $charStream->shouldReceive('readBytes')
                    ->zeroOrMoreTimes()
                    ->andReturn(false);

        $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);
        $encoder->encodeByteStream($os, $is, 22);
        $this->assertEquals(
            str_repeat('a', 53)."=\r\n".str_repeat('a', 75)."=\r\n".str_repeat('a', 13),
            $collection->content
            );
    }

    public function testObserverInterfaceCanChangeCharset()
    {
        $stream = $this->_createCharacterStream();
        $stream->shouldReceive('setCharacterSet')
               ->once()
               ->with('windows-1252');

        $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($stream);
        $encoder->charsetChanged('windows-1252');
    }

    // -- Creation Methods

    private function _createCharacterStream($stub = false)
    {
        return $this->getMockery('Swift_CharacterStream')->shouldIgnoreMissing();
    }

    private function _createEncoder($charStream)
    {
        return new Swift_Mime_HeaderEncoder_QpHeaderEncoder($charStream);
    }

    private function _createOutputByteStream($stub = false)
    {
        return $this->getMockery('Swift_OutputByteStream')->shouldIgnoreMissing();
    }

    private function _createInputByteStream($stub = false)
    {
        return $this->getMockery('Swift_InputByteStream')->shouldIgnoreMissing();
    }
}
