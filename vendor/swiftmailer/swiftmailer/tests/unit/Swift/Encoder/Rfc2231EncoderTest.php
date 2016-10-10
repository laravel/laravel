<?php

class Swift_Encoder_Rfc2231EncoderTest extends \SwiftMailerTestCase
{
    private $_rfc2045Token = '/^[\x21\x23-\x27\x2A\x2B\x2D\x2E\x30-\x39\x41-\x5A\x5E-\x7E]+$/D';

    /* --
    This algorithm is described in RFC 2231, but is barely touched upon except
    for mentioning bytes can be represented as their octet values (e.g. %20 for
    the SPACE character).

    The tests here focus on how to use that representation to always generate text
    which matches RFC 2045's definition of "token".
    */

    public function testEncodingAsciiCharactersProducesValidToken()
    {
        $charStream = $this->getMockery('Swift_CharacterStream');

        $string = '';
        foreach (range(0x00, 0x7F) as $octet) {
            $char = pack('C', $octet);
            $string .= $char;
            $charStream->shouldReceive('read')
                       ->once()
                       ->andReturn($char);
        }

        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importString')
                    ->once()
                    ->with($string);
        $charStream->shouldReceive('read')
                    ->atLeast()->times(1)
                    ->andReturn(false);

        $encoder = new Swift_Encoder_Rfc2231Encoder($charStream);
        $encoded = $encoder->encodeString($string);

        foreach (explode("\r\n", $encoded) as $line) {
            $this->assertRegExp($this->_rfc2045Token, $line,
                '%s: Encoder should always return a valid RFC 2045 token.');
        }
    }

    public function testEncodingNonAsciiCharactersProducesValidToken()
    {
        $charStream = $this->getMockery('Swift_CharacterStream');

        $string = '';
        foreach (range(0x80, 0xFF) as $octet) {
            $char = pack('C', $octet);
            $string .= $char;
            $charStream->shouldReceive('read')
                       ->once()
                       ->andReturn($char);
        }
        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importString')
                    ->once()
                    ->with($string);
        $charStream->shouldReceive('read')
                    ->atLeast()->times(1)
                    ->andReturn(false);
        $encoder = new Swift_Encoder_Rfc2231Encoder($charStream);

        $encoded = $encoder->encodeString($string);

        foreach (explode("\r\n", $encoded) as $line) {
            $this->assertRegExp($this->_rfc2045Token, $line,
                '%s: Encoder should always return a valid RFC 2045 token.');
        }
    }

    public function testMaximumLineLengthCanBeSet()
    {
        $charStream = $this->getMockery('Swift_CharacterStream');

        $string = '';
        for ($x = 0; $x < 200; ++$x) {
            $char = 'a';
            $string .= $char;
            $charStream->shouldReceive('read')
                       ->once()
                       ->andReturn($char);
        }
        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importString')
                    ->once()
                    ->with($string);
        $charStream->shouldReceive('read')
                    ->atLeast()->times(1)
                    ->andReturn(false);
        $encoder = new Swift_Encoder_Rfc2231Encoder($charStream);

        $encoded = $encoder->encodeString($string, 0, 75);

        $this->assertEquals(
            str_repeat('a', 75)."\r\n".
            str_repeat('a', 75)."\r\n".
            str_repeat('a', 50),
            $encoded,
            '%s: Lines should be wrapped at each 75 characters'
            );
    }

    public function testFirstLineCanHaveShorterLength()
    {
        $charStream = $this->getMockery('Swift_CharacterStream');

        $string = '';
        for ($x = 0; $x < 200; ++$x) {
            $char = 'a';
            $string .= $char;
            $charStream->shouldReceive('read')
                       ->once()
                       ->andReturn($char);
        }
        $charStream->shouldReceive('flushContents')
                    ->once();
        $charStream->shouldReceive('importString')
                    ->once()
                    ->with($string);
        $charStream->shouldReceive('read')
                    ->atLeast()->times(1)
                    ->andReturn(false);
        $encoder = new Swift_Encoder_Rfc2231Encoder($charStream);
        $encoded = $encoder->encodeString($string, 25, 75);

        $this->assertEquals(
            str_repeat('a', 50)."\r\n".
            str_repeat('a', 75)."\r\n".
            str_repeat('a', 75),
            $encoded,
            '%s: First line should be 25 bytes shorter than the others.'
            );
    }
}
