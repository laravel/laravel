<?php

class Swift_Encoder_QpEncoderTest extends \SwiftMailerTestCase
{
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

            $charStream = $this->_createCharStream();
            $charStream->shouldReceive('flushContents')
                       ->once();
            $charStream->shouldReceive('importString')
                       ->once()
                       ->with($char);
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array($ordinal));
            $charStream->shouldReceive('readBytes')
                       ->atLeast()->times(1)
                       ->andReturn(false);

            $encoder = new Swift_Encoder_QpEncoder($charStream);

            $this->assertIdenticalBinary($char, $encoder->encodeString($char));
        }
    }

    public function testWhiteSpaceAtLineEndingIsEncoded()
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
        $string = 'a'.$HT.$HT."\r\n".'b';

        $charStream = $this->_createCharStream();
        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importString')
                    ->once()
                    ->with($string);

        $charStream->shouldReceive('readBytes')->once()->andReturn(array(ord('a')));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x09));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x09));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x0D));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x0A));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(ord('b')));
        $charStream->shouldReceive('readBytes')->once()->andReturn(false);

        $encoder = new Swift_Encoder_QpEncoder($charStream);
        $this->assertEquals(
            'a'.$HT.'=09'."\r\n".'b',
            $encoder->encodeString($string)
            );

        //SPACE
        $string = 'a'.$SPACE.$SPACE."\r\n".'b';

        $charStream = $this->_createCharStream();
        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importString')
                    ->once()
                    ->with($string);

        $charStream->shouldReceive('readBytes')->once()->andReturn(array(ord('a')));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x20));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x20));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x0D));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x0A));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(ord('b')));
        $charStream->shouldReceive('readBytes')->once()->andReturn(false);

        $encoder = new Swift_Encoder_QpEncoder($charStream);
        $this->assertEquals(
            'a'.$SPACE.'=20'."\r\n".'b',
            $encoder->encodeString($string)
            );
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

        $string = 'a'."\r\n".'b'."\r\n".'c'."\r\n";

        $charStream = $this->_createCharStream();
        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importString')
                    ->once()
                    ->with($string);

        $charStream->shouldReceive('readBytes')->once()->andReturn(array(ord('a')));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x0D));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x0A));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(ord('b')));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x0D));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x0A));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(ord('c')));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x0D));
        $charStream->shouldReceive('readBytes')->once()->andReturn(array(0x0A));
        $charStream->shouldReceive('readBytes')->once()->andReturn(false);

        $encoder = new Swift_Encoder_QpEncoder($charStream);
        $this->assertEquals($string, $encoder->encodeString($string));
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

        $input = str_repeat('a', 140);

        $charStream = $this->_createCharStream();
        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importString')
                    ->once()
                    ->with($input);

        $output = '';
        for ($i = 0; $i < 140; ++$i) {
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array(ord('a')));

            if (75 == $i) {
                $output .= "=\r\n";
            }
            $output .= 'a';
        }

        $charStream->shouldReceive('readBytes')
                    ->once()
                    ->andReturn(false);

        $encoder = new Swift_Encoder_QpEncoder($charStream);
        $this->assertEquals($output, $encoder->encodeString($input));
    }

    public function testMaxLineLengthCanBeSpecified()
    {
        $input = str_repeat('a', 100);

        $charStream = $this->_createCharStream();
        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importString')
                    ->once()
                    ->with($input);

        $output = '';
        for ($i = 0; $i < 100; ++$i) {
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array(ord('a')));

            if (53 == $i) {
                $output .= "=\r\n";
            }
            $output .= 'a';
        }
        $charStream->shouldReceive('readBytes')
                    ->once()
                    ->andReturn(false);

        $encoder = new Swift_Encoder_QpEncoder($charStream);
        $this->assertEquals($output, $encoder->encodeString($input, 0, 54));
    }

    public function testBytesBelowPermittedRangeAreEncoded()
    {
        /*
        According to Rule (1 & 2)
        */

        foreach (range(0, 32) as $ordinal) {
            $char = chr($ordinal);

            $charStream = $this->_createCharStream();
            $charStream->shouldReceive('flushContents')
                       ->once();
            $charStream->shouldReceive('importString')
                       ->once()
                       ->with($char);
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array($ordinal));
            $charStream->shouldReceive('readBytes')
                       ->atLeast()->times(1)
                       ->andReturn(false);

            $encoder = new Swift_Encoder_QpEncoder($charStream);

            $this->assertEquals(
                sprintf('=%02X', $ordinal), $encoder->encodeString($char)
                );
        }
    }

    public function testDecimalByte61IsEncoded()
    {
        /*
        According to Rule (1 & 2)
        */

        $char = '=';

        $charStream = $this->_createCharStream();
        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importString')
                    ->once()
                    ->with($char);
        $charStream->shouldReceive('readBytes')
                    ->once()
                    ->andReturn(array(61));
        $charStream->shouldReceive('readBytes')
                    ->atLeast()->times(1)
                    ->andReturn(false);

        $encoder = new Swift_Encoder_QpEncoder($charStream);

        $this->assertEquals('=3D', $encoder->encodeString('='));
    }

    public function testBytesAbovePermittedRangeAreEncoded()
    {
        /*
        According to Rule (1 & 2)
        */

        foreach (range(127, 255) as $ordinal) {
            $char = chr($ordinal);

            $charStream = $this->_createCharStream();
            $charStream->shouldReceive('flushContents')
                       ->once();
            $charStream->shouldReceive('importString')
                       ->once()
                       ->with($char);
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array($ordinal));
            $charStream->shouldReceive('readBytes')
                       ->atLeast()->times(1)
                       ->andReturn(false);

            $encoder = new Swift_Encoder_QpEncoder($charStream);

            $this->assertEquals(
                sprintf('=%02X', $ordinal), $encoder->encodeString($char)
                );
        }
    }

    public function testFirstLineLengthCanBeDifferent()
    {
        $input = str_repeat('a', 140);

        $charStream = $this->_createCharStream();
        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importString')
                    ->once()
                    ->with($input);

        $output = '';
        for ($i = 0; $i < 140; ++$i) {
            $charStream->shouldReceive('readBytes')
                       ->once()
                       ->andReturn(array(ord('a')));

            if (53 == $i || 53 + 75 == $i) {
                $output .= "=\r\n";
            }
            $output .= 'a';
        }

        $charStream->shouldReceive('readBytes')
                    ->once()
                    ->andReturn(false);

        $encoder = new Swift_Encoder_QpEncoder($charStream);
        $this->assertEquals(
            $output, $encoder->encodeString($input, 22),
            '%s: First line should start at offset 22 so can only have max length 54'
            );
    }

    // -- Creation methods

    private function _createCharStream()
    {
        return $this->getMockery('Swift_CharacterStream')->shouldIgnoreMissing();
    }
}
