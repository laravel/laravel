<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles Base 64 Encoding in Swift Mailer.
 *
 * @author Chris Corbyn
 */
class Swift_Encoder_Base64Encoder implements Swift_Encoder
{
    /**
     * Takes an unencoded string and produces a Base64 encoded string from it.
     *
     * Base64 encoded strings have a maximum line length of 76 characters.
     * If the first line needs to be shorter, indicate the difference with
     * $firstLineOffset.
     *
     * @param string $string          to encode
     * @param int    $firstLineOffset
     * @param int    $maxLineLength   optional, 0 indicates the default of 76 bytes
     *
     * @return string
     */
    public function encodeString($string, $firstLineOffset = 0, $maxLineLength = 0)
    {
        if (0 >= $maxLineLength || 76 < $maxLineLength) {
            $maxLineLength = 76;
        }

        $encodedString = base64_encode($string);
        $firstLine = '';

        if (0 != $firstLineOffset) {
            $firstLine = substr(
                $encodedString, 0, $maxLineLength - $firstLineOffset
                )."\r\n";
            $encodedString = substr(
                $encodedString, $maxLineLength - $firstLineOffset
                );
        }

        return $firstLine.trim(chunk_split($encodedString, $maxLineLength, "\r\n"));
    }

    /**
     * Does nothing.
     */
    public function charsetChanged($charset)
    {
    }
}
