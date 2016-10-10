<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles Quoted Printable (Q) Header Encoding in Swift Mailer.
 *
 * @author Chris Corbyn
 */
class Swift_Mime_HeaderEncoder_QpHeaderEncoder extends Swift_Encoder_QpEncoder implements Swift_Mime_HeaderEncoder
{
    /**
     * Creates a new QpHeaderEncoder for the given CharacterStream.
     *
     * @param Swift_CharacterStream $charStream to use for reading characters
     */
    public function __construct(Swift_CharacterStream $charStream)
    {
        parent::__construct($charStream);
    }

    protected function initSafeMap()
    {
        foreach (array_merge(
            range(0x61, 0x7A), range(0x41, 0x5A),
            range(0x30, 0x39), array(0x20, 0x21, 0x2A, 0x2B, 0x2D, 0x2F)
        ) as $byte) {
            $this->_safeMap[$byte] = chr($byte);
        }
    }

    /**
     * Get the name of this encoding scheme.
     *
     * Returns the string 'Q'.
     *
     * @return string
     */
    public function getName()
    {
        return 'Q';
    }

    /**
     * Takes an unencoded string and produces a QP encoded string from it.
     *
     * @param string $string          string to encode
     * @param int    $firstLineOffset optional
     * @param int    $maxLineLength   optional, 0 indicates the default of 76 chars
     *
     * @return string
     */
    public function encodeString($string, $firstLineOffset = 0, $maxLineLength = 0)
    {
        return str_replace(array(' ', '=20', "=\r\n"), array('_', '_', "\r\n"),
            parent::encodeString($string, $firstLineOffset, $maxLineLength)
        );
    }
}
