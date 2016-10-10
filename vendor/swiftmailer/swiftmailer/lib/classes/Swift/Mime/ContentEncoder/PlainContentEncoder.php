<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles binary/7/8-bit Transfer Encoding in Swift Mailer.
 *
 * @author Chris Corbyn
 */
class Swift_Mime_ContentEncoder_PlainContentEncoder implements Swift_Mime_ContentEncoder
{
    /**
     * The name of this encoding scheme (probably 7bit or 8bit).
     *
     * @var string
     */
    private $_name;

    /**
     * True if canonical transformations should be done.
     *
     * @var bool
     */
    private $_canonical;

    /**
     * Creates a new PlainContentEncoder with $name (probably 7bit or 8bit).
     *
     * @param string $name
     * @param bool   $canonical If canonicalization transformation should be done.
     */
    public function __construct($name, $canonical = false)
    {
        $this->_name = $name;
        $this->_canonical = $canonical;
    }

    /**
     * Encode a given string to produce an encoded string.
     *
     * @param string $string
     * @param int    $firstLineOffset ignored
     * @param int    $maxLineLength   - 0 means no wrapping will occur
     *
     * @return string
     */
    public function encodeString($string, $firstLineOffset = 0, $maxLineLength = 0)
    {
        if ($this->_canonical) {
            $string = $this->_canonicalize($string);
        }

        return $this->_safeWordWrap($string, $maxLineLength, "\r\n");
    }

    /**
     * Encode stream $in to stream $out.
     *
     * @param Swift_OutputByteStream $os
     * @param Swift_InputByteStream  $is
     * @param int                    $firstLineOffset ignored
     * @param int                    $maxLineLength   optional, 0 means no wrapping will occur
     */
    public function encodeByteStream(Swift_OutputByteStream $os, Swift_InputByteStream $is, $firstLineOffset = 0, $maxLineLength = 0)
    {
        $leftOver = '';
        while (false !== $bytes = $os->read(8192)) {
            $toencode = $leftOver.$bytes;
            if ($this->_canonical) {
                $toencode = $this->_canonicalize($toencode);
            }
            $wrapped = $this->_safeWordWrap($toencode, $maxLineLength, "\r\n");
            $lastLinePos = strrpos($wrapped, "\r\n");
            $leftOver = substr($wrapped, $lastLinePos);
            $wrapped = substr($wrapped, 0, $lastLinePos);

            $is->write($wrapped);
        }
        if (strlen($leftOver)) {
            $is->write($leftOver);
        }
    }

    /**
     * Get the name of this encoding scheme.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Not used.
     */
    public function charsetChanged($charset)
    {
    }

    /**
     * A safer (but weaker) wordwrap for unicode.
     *
     * @param string $string
     * @param int    $length
     * @param string $le
     *
     * @return string
     */
    private function _safeWordwrap($string, $length = 75, $le = "\r\n")
    {
        if (0 >= $length) {
            return $string;
        }

        $originalLines = explode($le, $string);

        $lines = array();
        $lineCount = 0;

        foreach ($originalLines as $originalLine) {
            $lines[] = '';
            $currentLine = &$lines[$lineCount++];

            //$chunks = preg_split('/(?<=[\ \t,\.!\?\-&\+\/])/', $originalLine);
            $chunks = preg_split('/(?<=\s)/', $originalLine);

            foreach ($chunks as $chunk) {
                if (0 != strlen($currentLine)
                    && strlen($currentLine.$chunk) > $length) {
                    $lines[] = '';
                    $currentLine = &$lines[$lineCount++];
                }
                $currentLine .= $chunk;
            }
        }

        return implode("\r\n", $lines);
    }

    /**
     * Canonicalize string input (fix CRLF).
     *
     * @param string $string
     *
     * @return string
     */
    private function _canonicalize($string)
    {
        return str_replace(
            array("\r\n", "\r", "\n"),
            array("\n", "\n", "\r\n"),
            $string
            );
    }
}
