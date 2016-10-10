<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * An abstract base MIME Header.
 *
 * @author Chris Corbyn
 */
abstract class Swift_Mime_Headers_AbstractHeader implements Swift_Mime_Header
{
    /**
     * The name of this Header.
     *
     * @var string
     */
    private $_name;

    /**
     * The Grammar used for this Header.
     *
     * @var Swift_Mime_Grammar
     */
    private $_grammar;

    /**
     * The Encoder used to encode this Header.
     *
     * @var Swift_Encoder
     */
    private $_encoder;

    /**
     * The maximum length of a line in the header.
     *
     * @var int
     */
    private $_lineLength = 78;

    /**
     * The language used in this Header.
     *
     * @var string
     */
    private $_lang;

    /**
     * The character set of the text in this Header.
     *
     * @var string
     */
    private $_charset = 'utf-8';

    /**
     * The value of this Header, cached.
     *
     * @var string
     */
    private $_cachedValue = null;

    /**
     * Creates a new Header.
     *
     * @param Swift_Mime_Grammar $grammar
     */
    public function __construct(Swift_Mime_Grammar $grammar)
    {
        $this->setGrammar($grammar);
    }

    /**
     * Set the character set used in this Header.
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->clearCachedValueIf($charset != $this->_charset);
        $this->_charset = $charset;
        if (isset($this->_encoder)) {
            $this->_encoder->charsetChanged($charset);
        }
    }

    /**
     * Get the character set used in this Header.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->_charset;
    }

    /**
     * Set the language used in this Header.
     *
     * For example, for US English, 'en-us'.
     * This can be unspecified.
     *
     * @param string $lang
     */
    public function setLanguage($lang)
    {
        $this->clearCachedValueIf($this->_lang != $lang);
        $this->_lang = $lang;
    }

    /**
     * Get the language used in this Header.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->_lang;
    }

    /**
     * Set the encoder used for encoding the header.
     *
     * @param Swift_Mime_HeaderEncoder $encoder
     */
    public function setEncoder(Swift_Mime_HeaderEncoder $encoder)
    {
        $this->_encoder = $encoder;
        $this->setCachedValue(null);
    }

    /**
     * Get the encoder used for encoding this Header.
     *
     * @return Swift_Mime_HeaderEncoder
     */
    public function getEncoder()
    {
        return $this->_encoder;
    }

    /**
     * Set the grammar used for the header.
     *
     * @param Swift_Mime_Grammar $grammar
     */
    public function setGrammar(Swift_Mime_Grammar $grammar)
    {
        $this->_grammar = $grammar;
        $this->setCachedValue(null);
    }

    /**
     * Get the grammar used for this Header.
     *
     * @return Swift_Mime_Grammar
     */
    public function getGrammar()
    {
        return $this->_grammar;
    }

    /**
     * Get the name of this header (e.g. charset).
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->_name;
    }

    /**
     * Set the maximum length of lines in the header (excluding EOL).
     *
     * @param int $lineLength
     */
    public function setMaxLineLength($lineLength)
    {
        $this->clearCachedValueIf($this->_lineLength != $lineLength);
        $this->_lineLength = $lineLength;
    }

    /**
     * Get the maximum permitted length of lines in this Header.
     *
     * @return int
     */
    public function getMaxLineLength()
    {
        return $this->_lineLength;
    }

    /**
     * Get this Header rendered as a RFC 2822 compliant string.
     *
     * @throws Swift_RfcComplianceException
     *
     * @return string
     */
    public function toString()
    {
        return $this->_tokensToString($this->toTokens());
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     *
     * @see toString()
     */
    public function __toString()
    {
        return $this->toString();
    }

    // -- Points of extension

    /**
     * Set the name of this Header field.
     *
     * @param string $name
     */
    protected function setFieldName($name)
    {
        $this->_name = $name;
    }

    /**
     * Produces a compliant, formatted RFC 2822 'phrase' based on the string given.
     *
     * @param Swift_Mime_Header        $header
     * @param string                   $string  as displayed
     * @param string                   $charset of the text
     * @param Swift_Mime_HeaderEncoder $encoder
     * @param bool                     $shorten the first line to make remove for header name
     *
     * @return string
     */
    protected function createPhrase(Swift_Mime_Header $header, $string, $charset, Swift_Mime_HeaderEncoder $encoder = null, $shorten = false)
    {
        // Treat token as exactly what was given
        $phraseStr = $string;
        // If it's not valid
        if (!preg_match('/^'.$this->getGrammar()->getDefinition('phrase').'$/D', $phraseStr)) {
            // .. but it is just ascii text, try escaping some characters
            // and make it a quoted-string
            if (preg_match('/^'.$this->getGrammar()->getDefinition('text').'*$/D', $phraseStr)) {
                $phraseStr = $this->getGrammar()->escapeSpecials(
                    $phraseStr, array('"'), $this->getGrammar()->getSpecials()
                    );
                $phraseStr = '"'.$phraseStr.'"';
            } else {
                // ... otherwise it needs encoding
                // Determine space remaining on line if first line
                if ($shorten) {
                    $usedLength = strlen($header->getFieldName().': ');
                } else {
                    $usedLength = 0;
                }
                $phraseStr = $this->encodeWords($header, $string, $usedLength);
            }
        }

        return $phraseStr;
    }

    /**
     * Encode needed word tokens within a string of input.
     *
     * @param Swift_Mime_Header $header
     * @param string            $input
     * @param string            $usedLength optional
     *
     * @return string
     */
    protected function encodeWords(Swift_Mime_Header $header, $input, $usedLength = -1)
    {
        $value = '';

        $tokens = $this->getEncodableWordTokens($input);

        foreach ($tokens as $token) {
            // See RFC 2822, Sect 2.2 (really 2.2 ??)
            if ($this->tokenNeedsEncoding($token)) {
                // Don't encode starting WSP
                $firstChar = substr($token, 0, 1);
                switch ($firstChar) {
                    case ' ':
                    case "\t":
                        $value .= $firstChar;
                        $token = substr($token, 1);
                }

                if (-1 == $usedLength) {
                    $usedLength = strlen($header->getFieldName().': ') + strlen($value);
                }
                $value .= $this->getTokenAsEncodedWord($token, $usedLength);

                $header->setMaxLineLength(76); // Forcefully override
            } else {
                $value .= $token;
            }
        }

        return $value;
    }

    /**
     * Test if a token needs to be encoded or not.
     *
     * @param string $token
     *
     * @return bool
     */
    protected function tokenNeedsEncoding($token)
    {
        return preg_match('~[\x00-\x08\x10-\x19\x7F-\xFF\r\n]~', $token);
    }

    /**
     * Splits a string into tokens in blocks of words which can be encoded quickly.
     *
     * @param string $string
     *
     * @return string[]
     */
    protected function getEncodableWordTokens($string)
    {
        $tokens = array();

        $encodedToken = '';
        // Split at all whitespace boundaries
        foreach (preg_split('~(?=[\t ])~', $string) as $token) {
            if ($this->tokenNeedsEncoding($token)) {
                $encodedToken .= $token;
            } else {
                if (strlen($encodedToken) > 0) {
                    $tokens[] = $encodedToken;
                    $encodedToken = '';
                }
                $tokens[] = $token;
            }
        }
        if (strlen($encodedToken)) {
            $tokens[] = $encodedToken;
        }

        return $tokens;
    }

    /**
     * Get a token as an encoded word for safe insertion into headers.
     *
     * @param string $token           token to encode
     * @param int    $firstLineOffset optional
     *
     * @return string
     */
    protected function getTokenAsEncodedWord($token, $firstLineOffset = 0)
    {
        // Adjust $firstLineOffset to account for space needed for syntax
        $charsetDecl = $this->_charset;
        if (isset($this->_lang)) {
            $charsetDecl .= '*'.$this->_lang;
        }
        $encodingWrapperLength = strlen(
            '=?'.$charsetDecl.'?'.$this->_encoder->getName().'??='
            );

        if ($firstLineOffset >= 75) {
            //Does this logic need to be here?
            $firstLineOffset = 0;
        }

        $encodedTextLines = explode("\r\n",
            $this->_encoder->encodeString(
                $token, $firstLineOffset, 75 - $encodingWrapperLength, $this->_charset
                )
        );

        if (strtolower($this->_charset) !== 'iso-2022-jp') {
            // special encoding for iso-2022-jp using mb_encode_mimeheader
            foreach ($encodedTextLines as $lineNum => $line) {
                $encodedTextLines[$lineNum] = '=?'.$charsetDecl.
                    '?'.$this->_encoder->getName().
                    '?'.$line.'?=';
            }
        }

        return implode("\r\n ", $encodedTextLines);
    }

    /**
     * Generates tokens from the given string which include CRLF as individual tokens.
     *
     * @param string $token
     *
     * @return string[]
     */
    protected function generateTokenLines($token)
    {
        return preg_split('~(\r\n)~', $token, -1, PREG_SPLIT_DELIM_CAPTURE);
    }

    /**
     * Set a value into the cache.
     *
     * @param string $value
     */
    protected function setCachedValue($value)
    {
        $this->_cachedValue = $value;
    }

    /**
     * Get the value in the cache.
     *
     * @return string
     */
    protected function getCachedValue()
    {
        return $this->_cachedValue;
    }

    /**
     * Clear the cached value if $condition is met.
     *
     * @param bool $condition
     */
    protected function clearCachedValueIf($condition)
    {
        if ($condition) {
            $this->setCachedValue(null);
        }
    }

    /**
     * Generate a list of all tokens in the final header.
     *
     * @param string $string The string to tokenize
     *
     * @return array An array of tokens as strings
     */
    protected function toTokens($string = null)
    {
        if (is_null($string)) {
            $string = $this->getFieldBody();
        }

        $tokens = array();

        // Generate atoms; split at all invisible boundaries followed by WSP
        foreach (preg_split('~(?=[ \t])~', $string) as $token) {
            $newTokens = $this->generateTokenLines($token);
            foreach ($newTokens as $newToken) {
                $tokens[] = $newToken;
            }
        }

        return $tokens;
    }

    /**
     * Takes an array of tokens which appear in the header and turns them into
     * an RFC 2822 compliant string, adding FWSP where needed.
     *
     * @param string[] $tokens
     *
     * @return string
     */
    private function _tokensToString(array $tokens)
    {
        $lineCount = 0;
        $headerLines = array();
        $headerLines[] = $this->_name.': ';
        $currentLine = &$headerLines[$lineCount++];

        // Build all tokens back into compliant header
        foreach ($tokens as $i => $token) {
            // Line longer than specified maximum or token was just a new line
            if (("\r\n" == $token) ||
                ($i > 0 && strlen($currentLine.$token) > $this->_lineLength)
                && 0 < strlen($currentLine)) {
                $headerLines[] = '';
                $currentLine = &$headerLines[$lineCount++];
            }

            // Append token to the line
            if ("\r\n" != $token) {
                $currentLine .= $token;
            }
        }

        // Implode with FWS (RFC 2822, 2.2.3)
        return implode("\r\n", $headerLines)."\r\n";
    }
}
