<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles Base 64 Transfer Encoding in Swift Mailer.
 *
 * @author Chris Corbyn
 */
class Swift_Mime_ContentEncoder_Base64ContentEncoder extends Swift_Encoder_Base64Encoder implements Swift_Mime_ContentEncoder
{
    /**
     * Encode stream $in to stream $out.
     *
     * @param Swift_OutputByteStream $os
     * @param Swift_InputByteStream  $is
     * @param int                    $firstLineOffset
     * @param int                    $maxLineLength,  optional, 0 indicates the default of 76 bytes
     */
    public function encodeByteStream(Swift_OutputByteStream $os, Swift_InputByteStream $is, $firstLineOffset = 0, $maxLineLength = 0)
    {
        if (0 >= $maxLineLength || 76 < $maxLineLength) {
            $maxLineLength = 76;
        }

        $remainder = 0;
        $base64ReadBufferRemainderBytes = null;

        // To reduce memory usage, the output buffer is streamed to the input buffer like so:
        //   Output Stream => base64encode => wrap line length => Input Stream
        // HOWEVER it's important to note that base64_encode() should only be passed whole triplets of data (except for the final chunk of data)
        // otherwise it will assume the input data has *ended* and it will incorrectly pad/terminate the base64 data mid-stream.
        // We use $base64ReadBufferRemainderBytes to carry over 1-2 "remainder" bytes from the each chunk from OutputStream and pre-pend those onto the
        // chunk of bytes read in the next iteration.
        // When the OutputStream is empty, we must flush any remainder bytes.
        while (true) {
            $readBytes = $os->read(8192);
            $atEOF = ($readBytes === false);

            if ($atEOF) {
                $streamTheseBytes = $base64ReadBufferRemainderBytes;
            } else {
                $streamTheseBytes = $base64ReadBufferRemainderBytes.$readBytes;
            }
            $base64ReadBufferRemainderBytes = null;
            $bytesLength = strlen($streamTheseBytes);

            if ($bytesLength === 0) { // no data left to encode
                break;
            }

            // if we're not on the last block of the ouput stream, make sure $streamTheseBytes ends with a complete triplet of data
            // and carry over remainder 1-2 bytes to the next loop iteration
            if (!$atEOF) {
                $excessBytes = $bytesLength % 3;
                if ($excessBytes !== 0) {
                    $base64ReadBufferRemainderBytes = substr($streamTheseBytes, -$excessBytes);
                    $streamTheseBytes = substr($streamTheseBytes, 0, $bytesLength - $excessBytes);
                }
            }

            $encoded = base64_encode($streamTheseBytes);
            $encodedTransformed = '';
            $thisMaxLineLength = $maxLineLength - $remainder - $firstLineOffset;

            while ($thisMaxLineLength < strlen($encoded)) {
                $encodedTransformed .= substr($encoded, 0, $thisMaxLineLength)."\r\n";
                $firstLineOffset = 0;
                $encoded = substr($encoded, $thisMaxLineLength);
                $thisMaxLineLength = $maxLineLength;
                $remainder = 0;
            }

            if (0 < $remainingLength = strlen($encoded)) {
                $remainder += $remainingLength;
                $encodedTransformed .= $encoded;
                $encoded = null;
            }

            $is->write($encodedTransformed);

            if ($atEOF) {
                break;
            }
        }
    }

    /**
     * Get the name of this encoding scheme.
     * Returns the string 'base64'.
     *
     * @return string
     */
    public function getName()
    {
        return 'base64';
    }
}
