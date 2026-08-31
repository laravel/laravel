<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Encoder;

/**
 * @author Chris Corbyn
 */
class Base64Encoder implements EncoderInterface
{
    /**
     * Takes an unencoded string and produces a Base64 encoded string from it.
     *
     * Base64 encoded strings have a maximum line length of 76 characters.
     * If the first line needs to be shorter, indicate the difference with
     * $firstLineOffset.
     */
    public function encodeString(string $string, ?string $charset = 'utf-8', int $firstLineOffset = 0, int $maxLineLength = 0): string
    {
        if (0 >= $maxLineLength || 76 < $maxLineLength) {
            $maxLineLength = 76;
        }

        $encodedString = base64_encode($string);
        $firstLine = '';
        if (0 !== $firstLineOffset) {
            $firstLine = substr($encodedString, 0, $maxLineLength - $firstLineOffset)."\r\n";
            $encodedString = substr($encodedString, $maxLineLength - $firstLineOffset);
        }

        return $firstLine.trim(chunk_split($encodedString, $maxLineLength, "\r\n"));
    }
}
