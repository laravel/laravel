<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles Base64 (B) Header Encoding in Swift Mailer.
 *
 * @author Chris Corbyn
 */
class Swift_Mime_HeaderEncoder_Base64HeaderEncoder extends Swift_Encoder_Base64Encoder implements Swift_Mime_HeaderEncoder
{
    /**
     * Get the name of this encoding scheme.
     * Returns the string 'B'.
     *
     * @return string
     */
    public function getName()
    {
        return 'B';
    }

    /**
     * Takes an unencoded string and produces a Base64 encoded string from it.
     *
     * If the charset is iso-2022-jp, it uses mb_encode_mimeheader instead of
     * default encodeString, otherwise pass to the parent method.
     *
     * @param string $string          string to encode
     * @param int    $firstLineOffset
     * @param int    $maxLineLength   optional, 0 indicates the default of 76 bytes
     * @param string $charset
     *
     * @return string
     */
    public function encodeString($string, $firstLineOffset = 0, $maxLineLength = 0, $charset = 'utf-8')
    {
        if (strtolower($charset) === 'iso-2022-jp') {
            $old = mb_internal_encoding();
            mb_internal_encoding('utf-8');
            $newstring = mb_encode_mimeheader($string, $charset, $this->getName(), "\r\n");
            mb_internal_encoding($old);

            return $newstring;
        }

        return parent::encodeString($string, $firstLineOffset, $maxLineLength);
    }
}
