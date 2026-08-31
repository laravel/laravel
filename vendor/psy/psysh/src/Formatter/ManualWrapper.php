<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Formatter;

use Psy\Readline\Interactive\Layout\DisplayString;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\Helper;

/**
 * A CJK and tag-aware text wrapper for manual content.
 *
 * Handles proper line breaking for:
 * - Full-width CJK characters (count as 2 characters wide)
 * - XML tags (ignored for width calculation)
 * - CJK typography rules (characters that can't start/end lines)
 */
class ManualWrapper
{
    // Full-width character ranges
    // via http://www.localizingjapan.com/blog/2012/01/20/regular-expressions-for-japanese-text/
    private const HIRAGANA = '\p{Hiragana}';
    private const KATAKANA = '\p{Katakana}';
    private const HAN = '\p{Han}';
    private const HANGUL = '\p{Hangul}';
    private const RADICALS = '\x{2E80}-\x{2FD5}';
    private const PUNCTUATION = '\x{3000}-\x{303F}';
    private const SYMBOLS = '\x{31F0}-\x{31FF}\x{3220}-\x{3243}\x{3280}-\x{337F}';
    private const ASCII = '\x{FF01}-\x{FF5E}';

    private static $fullWidthRanges = [
        self::HIRAGANA,
        self::KATAKANA,
        self::HAN,
        self::HANGUL,
        self::RADICALS,
        self::PUNCTUATION,
        self::SYMBOLS,
        self::ASCII,
    ];

    // CJK characters not allowed at the start of lines
    private static $notStart = [
        // mid-sentence and closing punctuation, hyphens, etc:
        //   !,.:;?‐–—―‧‼⁇⁈⁉╴、。〜゠・︰﹐﹒﹔﹕﹖﹗﹘！＂，．：；？～
        '!,\\.:;\\?\x{2010}\x{2013}\x{2014}\x{2015}\x{2027}\x{203C}\x{2047}\x{2048}\x{2049}\x{2574}\x{3001}\x{3002}'
        .'\x{301C}\x{30A0}\x{30FB}\x{FE30}\x{FE50}\x{FE52}\x{FE54}\x{FE55}\x{FE56}\x{FE57}\x{FE58}\x{FF01}\x{FF02}'
        .'\x{FF0C}\x{FF0E}\x{FF1A}\x{FF1B}\x{FF1F}\x{FF5E}',

        // closing brackets:
        //   )>]}»'"„›〃〉》」』】〕〗〙〞〟︶︸︺︼︾﹀﹂﹑﹚﹜＇）］｝｠､
        '\\)>\\]\\}\x{00BB}\x{2019}\x{201D}\x{201E}\x{203A}\x{3003}\x{3009}\x{300B}\x{300D}\x{300F}\x{3011}\x{3015}'
        .'\x{3017}\x{3019}\x{301E}\x{301F}\x{FE36}\x{FE38}\x{FE3A}\x{FE3C}\x{FE3E}\x{FE40}\x{FE42}\x{FE51}\x{FE5A}'
        .'\x{FE5C}\x{FF07}\x{FF09}\x{FF3D}\x{FF5D}\x{FF60}\x{FF64}',

        // misc symbols:
        //   %¢¨°·ˇˉ‖†‡•‥℃∶、〆︱︲︳﹓％｜
        '%\x{00A2}\x{00A8}\x{00B0}\x{00B7}\x{02C7}\x{02C9}\x{2016}\x{2020}\x{2021}\x{2022}\x{2025}\x{2103}\x{2236}'
        .'\x{3001}\x{3006}\x{FE31}\x{FE32}\x{FE33}\x{FE53}\x{FF05}\x{FF5C}',

        // misc japanese:
        //   ヽヾーァィゥェォッャュョヮヵヶぁぃぅぇぉっゃゅょゎゕゖㇰㇱㇲㇳㇴㇵㇶㇷㇸㇹㇺㇻㇼㇽㇾㇿ々〻
        '\x{30FD}\x{30FE}\x{30FC}\x{30A1}\x{30A3}\x{30A5}\x{30A7}\x{30A9}\x{30C3}\x{30E3}\x{30E5}\x{30E7}\x{30EE}'
        .'\x{30F5}\x{30F6}\x{3041}\x{3043}\x{3045}\x{3047}\x{3049}\x{3063}\x{3083}\x{3085}\x{3087}\x{308E}\x{3095}'
        .'\x{3096}\x{31F0}\x{31F1}\x{31F2}\x{31F3}\x{31F4}\x{31F5}\x{31F6}\x{31F7}\x{31F8}\x{31F9}\x{31FA}\x{31FB}'
        .'\x{31FC}\x{31FD}\x{31FE}\x{31FF}\x{3005}\x{303B}',
    ];

    // CJK characters not allowed at the end of lines
    private static $notEnd = [
        // misc symbols, currency, etc:
        //   #$*\£¥々〇＄￡￥￦
        '#\\$\\*\\\x{00A3}\x{00A5}\x{3005}\x{3007}\x{FF04}\x{FFE1}\x{FFE5}\x{FFE6}',

        // opening brackets:
        //   ([{'"‟‵〈《「『【〔〖〝︴︵︷︹︻︽︿﹁﹃﹏﹙﹛（［｀｛
        '\\(\\[\\{\x{2018}\x{201C}\x{201F}\x{2035}\x{3008}\x{300A}\x{300C}\x{300E}\x{3010}\x{3014}\x{3016}\x{301D}'
        .'\x{FE34}\x{FE35}\x{FE37}\x{FE39}\x{FE3B}\x{FE3D}\x{FE3F}\x{FE41}\x{FE43}\x{FE4F}\x{FE59}\x{FE5B}\x{FF08}'
        .'\x{FF3B}\x{FF40}\x{FF5B}',
    ];

    // CJK characters not allowed at the start _or_ end of lines
    private static $notSplit = [
        // misc japanese:
        //   —…‥〳〴〵
        '\x{2014}\x{2026}\x{2025}\x{3033}\x{3034}\x{3035}',
    ];

    private ?OutputFormatterInterface $outputFormatter;

    public function __construct(?OutputFormatterInterface $outputFormatter = null)
    {
        $this->outputFormatter = $outputFormatter;
    }

    /**
     * A tag-aware, CJK friendly version of wordwrap().
     *
     * @param string $text  The input string
     * @param int    $width The number of characters at which the string will be wrapped
     * @param string $break The line break character
     * @param bool   $cut   Wrap at or before the specified width (unused)
     *
     * @return string The wrapped text
     */
    public function wrap(string $text, int $width = 100, string $break = "\n", bool $cut = false): string
    {
        $lines = [];

        foreach (\explode($break, $text) as $line) {
            if ($this->width($line) <= $width) {
                $lines[] = $line;
                continue;
            }

            $buf = '';
            foreach ($this->words($line) as $word) {
                if ($this->width($buf.$word) <= $width) {
                    $buf .= $word;
                    continue;
                }

                $lines[] = \rtrim($buf);
                $buf = $word;
            }

            if ($this->width($buf) > 0) {
                $lines[] = $buf;
            }
        }

        return \implode($break, $lines);
    }

    public function longestWordWidth(string $line): int
    {
        $width = 1;
        foreach ($this->words($line) as $word) {
            $width = \max($width, $this->width($word));
        }

        return $width;
    }

    /**
     * Calculate the apparent length of a unicode string.
     *
     * Full-width CJK characters count as 2 characters wide.
     * Tags are ignored for width calculation.
     *
     * @deprecated use an instance of ManualWrapper so formatter-aware width can be used
     *
     * @param string $text The text to measure
     *
     * @return int The apparent width
     */
    public static function len(string $text): int
    {
        $text = \strip_tags($text);

        // Match all full-width characters and replace them with 'xx', so that we can compute the apparent length
        // of the rendered string.
        $isFullWidth = \sprintf('/[%s]/u', \implode('', self::$fullWidthRanges));

        return DisplayString::width(\rtrim(\preg_replace($isFullWidth, 'xx', $text)));
    }

    private function width(string $text): int
    {
        $text = $this->outputFormatter === null ? \strip_tags($text) : Helper::removeDecoration($this->outputFormatter, $text);
        // Symfony 3.4 removes escaped "<" but leaves escaped ">" in place.
        $text = \str_replace(['\\<', '\\>'], ['<', '>'], $text);

        // Match all full-width characters and replace them with 'xx', so that we can compute the apparent length
        // of the rendered string.
        $isFullWidth = \sprintf('/[%s]/u', \implode('', self::$fullWidthRanges));

        return DisplayString::width(\rtrim(\preg_replace($isFullWidth, 'xx', $text)));
    }

    /**
     * A word generator.
     *
     * Returns actual (space delimited) words. It also returns CJK characters as "words", as long as they can be split
     * between lines.
     *
     * @param string $line The line to split into words
     *
     * @return \Generator<string>
     */
    private function words(string $line): \Generator
    {
        $isCJK = \sprintf('/[%s]/u', \implode('', self::$fullWidthRanges));
        $notStart = \sprintf('/[%s%s]/u', \implode('', self::$notStart), \implode('', self::$notSplit));
        $notEnd = \sprintf('/[%s%s]/u', \implode('', self::$notEnd), \implode('', self::$notSplit));

        $line = \rtrim($line);
        $i = 0;
        $len = \mb_strlen($line);
        $inTag = false;

        do {
            $char = \mb_substr($line, $i, 1);
            switch ($char) {
                case '<':
                    $inTag = true;
                    break;

                case '>':
                    $inTag = false;
                    break;

                case ' ':
                    if (!$inTag) {
                        yield \rtrim(\mb_substr($line, 0, $i)).' ';
                        $line = \mb_substr($line, $i + 1);
                        $len = \mb_strlen($line);
                        $i = -1;
                    }
                    break;

                default:
                    // if this is a CJK character...
                    if (!$inTag && \preg_match($isCJK, $char)) {
                        // … and it can end a line
                        if (!\preg_match($notEnd, $char)) {
                            // … and the next one can start a line
                            $next = \mb_substr($line, $i + 1, 1);
                            if (!\preg_match($notStart, $next)) {
                                // we'll pretend it's a word :)
                                yield \rtrim(\mb_substr($line, 0, $i + 1));
                                $line = \mb_substr($line, $i + 1);
                                $len = \mb_strlen($line);
                                $i = -1;
                            }
                        }
                    }
                    break;
            }

            $i++;
        } while ($i < $len);

        yield \rtrim($line);
    }
}
