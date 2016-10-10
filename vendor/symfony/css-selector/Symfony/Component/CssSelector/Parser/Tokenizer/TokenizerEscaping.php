<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Parser\Tokenizer;

/**
 * CSS selector tokenizer escaping applier.
 *
 * This component is a port of the Python cssselector library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class TokenizerEscaping
{
    /**
     * @var TokenizerPatterns
     */
    private $patterns;

    /**
     * @param TokenizerPatterns $patterns
     */
    public function __construct(TokenizerPatterns $patterns)
    {
        $this->patterns = $patterns;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function escapeUnicode($value)
    {
        $value = $this->replaceUnicodeSequences($value);

        return preg_replace($this->patterns->getSimpleEscapePattern(), '$1', $value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function escapeUnicodeAndNewLine($value)
    {
        $value = preg_replace($this->patterns->getNewLineEscapePattern(), '', $value);

        return $this->escapeUnicode($value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function replaceUnicodeSequences($value)
    {
        return preg_replace_callback($this->patterns->getUnicodeEscapePattern(), function ($match) {
            $c = hexdec($match[1]);

            if (0x80 > $c %= 0x200000) {
                return chr($c);
            }
            if (0x800 > $c) {
                return chr(0xC0 | $c >> 6).chr(0x80 | $c & 0x3F);
            }
            if (0x10000 > $c) {
                return chr(0xE0 | $c >> 12).chr(0x80 | $c >> 6 & 0x3F).chr(0x80 | $c & 0x3F);
            }
        }, $value);
    }
}
