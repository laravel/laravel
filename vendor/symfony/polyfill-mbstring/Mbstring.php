<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Mbstring;

/**
 * Partial mbstring implementation in PHP, iconv based, UTF-8 centric.
 *
 * Implemented:
 * - mb_chr                  - Returns a specific character from its Unicode code point
 * - mb_convert_encoding     - Convert character encoding
 * - mb_convert_variables    - Convert character code in variable(s)
 * - mb_decode_mimeheader    - Decode string in MIME header field
 * - mb_encode_mimeheader    - Encode string for MIME header XXX NATIVE IMPLEMENTATION IS REALLY BUGGED
 * - mb_decode_numericentity - Decode HTML numeric string reference to character
 * - mb_encode_numericentity - Encode character to HTML numeric string reference
 * - mb_convert_case         - Perform case folding on a string
 * - mb_detect_encoding      - Detect character encoding
 * - mb_get_info             - Get internal settings of mbstring
 * - mb_http_input           - Detect HTTP input character encoding
 * - mb_http_output          - Set/Get HTTP output character encoding
 * - mb_internal_encoding    - Set/Get internal character encoding
 * - mb_list_encodings       - Returns an array of all supported encodings
 * - mb_ord                  - Returns the Unicode code point of a character
 * - mb_output_handler       - Callback function converts character encoding in output buffer
 * - mb_scrub                - Replaces ill-formed byte sequences with substitute characters
 * - mb_strlen               - Get string length
 * - mb_strpos               - Find position of first occurrence of string in a string
 * - mb_strrpos              - Find position of last occurrence of a string in a string
 * - mb_str_split            - Convert a string to an array
 * - mb_strtolower           - Make a string lowercase
 * - mb_strtoupper           - Make a string uppercase
 * - mb_substitute_character - Set/Get substitution character
 * - mb_substr               - Get part of string
 * - mb_stripos              - Finds position of first occurrence of a string within another, case insensitive
 * - mb_stristr              - Finds first occurrence of a string within another, case insensitive
 * - mb_strrchr              - Finds the last occurrence of a character in a string within another
 * - mb_strrichr             - Finds the last occurrence of a character in a string within another, case insensitive
 * - mb_strripos             - Finds position of last occurrence of a string within another, case insensitive
 * - mb_strstr               - Finds first occurrence of a string within another
 * - mb_strwidth             - Return width of string
 * - mb_substr_count         - Count the number of substring occurrences
 * - mb_ucfirst              - Make a string's first character uppercase
 * - mb_lcfirst              - Make a string's first character lowercase
 * - mb_trim                 - Strip whitespace (or other characters) from the beginning and end of a string
 * - mb_ltrim                - Strip whitespace (or other characters) from the beginning of a string
 * - mb_rtrim                - Strip whitespace (or other characters) from the end of a string
 *
 * Not implemented:
 * - mb_convert_kana         - Convert "kana" one from another ("zen-kaku", "han-kaku" and more)
 * - mb_ereg_*               - Regular expression with multibyte support
 * - mb_parse_str            - Parse GET/POST/COOKIE data and set global variable
 * - mb_preferred_mime_name  - Get MIME charset string
 * - mb_regex_encoding       - Returns current encoding for multibyte regex as string
 * - mb_regex_set_options    - Set/Get the default options for mbregex functions
 * - mb_send_mail            - Send encoded mail
 * - mb_split                - Split multibyte string using regular expression
 * - mb_strcut               - Get part of string
 * - mb_strimwidth           - Get truncated string with specified width
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
final class Mbstring
{
    public const MB_CASE_FOLD = \PHP_INT_MAX;

    private const SIMPLE_CASE_FOLD = [
        ['µ', 'ſ', "\xCD\x85", 'ς', "\xCF\x90", "\xCF\x91", "\xCF\x95", "\xCF\x96", "\xCF\xB0", "\xCF\xB1", "\xCF\xB5", "\xE1\xBA\x9B", "\xE1\xBE\xBE"],
        ['μ', 's', 'ι',        'σ', 'β',        'θ',        'φ',        'π',        'κ',        'ρ',        'ε',        "\xE1\xB9\xA1", 'ι'],
    ];

    private static $encodingList = ['ASCII', 'UTF-8'];
    private static $language = 'neutral';
    private static $internalEncoding = 'UTF-8';
    private static $iconvSupportsIgnore;

    public static function mb_convert_encoding($s, $toEncoding, $fromEncoding = null)
    {
        if (\is_array($s)) {
            $r = [];
            foreach ($s as $str) {
                $r[] = self::mb_convert_encoding($str, $toEncoding, $fromEncoding);
            }

            return $r;
        }

        if (\is_array($fromEncoding) || (null !== $fromEncoding && false !== strpos($fromEncoding, ','))) {
            $fromEncoding = self::mb_detect_encoding($s, $fromEncoding);
        } else {
            $fromEncoding = self::getEncoding($fromEncoding);
        }

        $toEncoding = self::getEncoding($toEncoding);

        if ('BASE64' === $fromEncoding) {
            $s = base64_decode($s);
            $fromEncoding = $toEncoding;
        }

        if ('BASE64' === $toEncoding) {
            return base64_encode($s);
        }

        if ('HTML-ENTITIES' === $toEncoding || 'HTML' === $toEncoding) {
            if ('HTML-ENTITIES' === $fromEncoding || 'HTML' === $fromEncoding) {
                $fromEncoding = 'Windows-1252';
            }
            if ('UTF-8' !== $fromEncoding) {
                $s = self::iconv($fromEncoding, 'UTF-8', $s);
            }

            return preg_replace_callback('/[\x80-\xFF]+/', [__CLASS__, 'html_encoding_callback'], $s);
        }

        if ('HTML-ENTITIES' === $fromEncoding) {
            $decodeControlChars = static function ($m) {
                $code = '' !== ($m[2] ?? '') ? hexdec($m[2]) : (int) $m[1];

                if ($code < 32 || 127 === $code) {
                    return \chr($code);
                }
                if (128 <= $code && $code <= 159) {
                    return "\xC2".\chr(0x80 | ($code & 0x3F));
                }

                return $m[0];
            };

            if (\PHP_VERSION_ID >= 70400) {
                $s = html_entity_decode($s, \ENT_QUOTES, 'UTF-8');
                // html_entity_decode() leaves numeric entities for C0/C1 control
                // characters as-is (HTML spec), but mb_convert_encoding() decodes
                // them. Catch what html_entity_decode() missed.
                if (false !== strpos($s, '&#')) {
                    $s = preg_replace_callback('/&#(?:0*([0-9]++)|[xX]0*([0-9a-fA-F]++));/', $decodeControlChars, $s);
                }
            } else {
                // PHP < 7.4: html_entity_decode() truncates strings at NUL bytes,
                // so decode the control character entities first then call
                // html_entity_decode() on each NUL-delimited chunk independently.
                $s = preg_replace_callback('/&#(?:0*([0-9]++)|[xX]0*([0-9a-fA-F]++));/', $decodeControlChars, $s);
                $s = implode("\0", array_map(static function ($chunk) {
                    return html_entity_decode($chunk, \ENT_QUOTES, 'UTF-8');
                }, explode("\0", $s)));
            }
            $fromEncoding = 'UTF-8';
        }

        return self::iconv($fromEncoding, $toEncoding, $s);
    }

    public static function mb_convert_variables($toEncoding, $fromEncoding, &...$vars)
    {
        $ok = true;
        array_walk_recursive($vars, static function (&$v) use (&$ok, $toEncoding, $fromEncoding) {
            if (false === $v = self::mb_convert_encoding($v, $toEncoding, $fromEncoding)) {
                $ok = false;
            }
        });

        return $ok ? $fromEncoding : false;
    }

    public static function mb_decode_mimeheader($s)
    {
        return iconv_mime_decode($s, 2, self::$internalEncoding);
    }

    public static function mb_encode_mimeheader($s, $charset = null, $transferEncoding = null, $linefeed = null, $indent = null)
    {
        trigger_error('mb_encode_mimeheader() is bugged. Please use iconv_mime_encode() instead', \E_USER_WARNING);
    }

    public static function mb_decode_numericentity($s, $convmap, $encoding = null)
    {
        if (null !== $s && !\is_scalar($s) && !(\is_object($s) && method_exists($s, '__toString'))) {
            trigger_error('mb_decode_numericentity() expects parameter 1 to be string, '.\gettype($s).' given', \E_USER_WARNING);

            return null;
        }

        if (!\is_array($convmap) || (80000 > \PHP_VERSION_ID && !$convmap)) {
            return false;
        }

        if (null !== $encoding && !\is_scalar($encoding)) {
            trigger_error('mb_decode_numericentity() expects parameter 3 to be string, '.\gettype($s).' given', \E_USER_WARNING);

            return '';  // Instead of null (cf. mb_encode_numericentity).
        }

        $s = (string) $s;
        if ('' === $s) {
            return '';
        }

        $encoding = self::getEncoding($encoding);

        if ('UTF-8' === $encoding) {
            $encoding = null;
            if (!preg_match('//u', $s)) {
                $s = @self::iconv('UTF-8', 'UTF-8', $s);
            }
        } else {
            $s = self::iconv($encoding, 'UTF-8', $s);
        }

        $cnt = floor(\count($convmap) / 4) * 4;

        for ($i = 0; $i < $cnt; $i += 4) {
            // collector_decode_htmlnumericentity ignores $convmap[$i + 3]
            $convmap[$i] += $convmap[$i + 2];
            $convmap[$i + 1] += $convmap[$i + 2];
        }

        $s = preg_replace_callback('/&#(?:0*([0-9]+)|x0*([0-9a-fA-F]+))'.(\PHP_VERSION_ID >= 80200 ? '' : '(?!&)').';?/', static function (array $m) use ($cnt, $convmap) {
            $c = isset($m[2]) ? (int) hexdec($m[2]) : $m[1];
            for ($i = 0; $i < $cnt; $i += 4) {
                if ($c >= $convmap[$i] && $c <= $convmap[$i + 1]) {
                    return self::mb_chr($c - $convmap[$i + 2]);
                }
            }

            return $m[0];
        }, $s);

        if (null === $encoding) {
            return $s;
        }

        return self::iconv('UTF-8', $encoding, $s);
    }

    public static function mb_encode_numericentity($s, $convmap, $encoding = null, $is_hex = false)
    {
        if (null !== $s && !\is_scalar($s) && !(\is_object($s) && method_exists($s, '__toString'))) {
            trigger_error('mb_encode_numericentity() expects parameter 1 to be string, '.\gettype($s).' given', \E_USER_WARNING);

            return null;
        }

        if (!\is_array($convmap) || (80000 > \PHP_VERSION_ID && !$convmap)) {
            return false;
        }

        if (null !== $encoding && !\is_scalar($encoding)) {
            trigger_error('mb_encode_numericentity() expects parameter 3 to be string, '.\gettype($s).' given', \E_USER_WARNING);

            return null;  // Instead of '' (cf. mb_decode_numericentity).
        }

        if (null !== $is_hex && !\is_scalar($is_hex)) {
            trigger_error('mb_encode_numericentity() expects parameter 4 to be boolean, '.\gettype($s).' given', \E_USER_WARNING);

            return null;
        }

        $s = (string) $s;
        if ('' === $s) {
            return '';
        }

        $encoding = self::getEncoding($encoding);

        if ('UTF-8' === $encoding) {
            $encoding = null;
            if (!preg_match('//u', $s)) {
                $s = @self::iconv('UTF-8', 'UTF-8', $s);
            }
        } else {
            $s = self::iconv($encoding, 'UTF-8', $s);
        }

        static $ulenMask = ["\xC0" => 2, "\xD0" => 2, "\xE0" => 3, "\xF0" => 4];

        $cnt = floor(\count($convmap) / 4) * 4;
        $i = 0;
        $len = \strlen($s);
        $result = '';

        while ($i < $len) {
            $ulen = $s[$i] < "\x80" ? 1 : $ulenMask[$s[$i] & "\xF0"];
            $uchr = substr($s, $i, $ulen);
            $i += $ulen;
            $c = self::mb_ord($uchr);

            for ($j = 0; $j < $cnt; $j += 4) {
                if ($c >= $convmap[$j] && $c <= $convmap[$j + 1]) {
                    $cOffset = ($c + $convmap[$j + 2]) & $convmap[$j + 3];
                    $result .= $is_hex ? \sprintf('&#x%X;', $cOffset) : '&#'.$cOffset.';';
                    continue 2;
                }
            }
            $result .= $uchr;
        }

        if (null === $encoding) {
            return $result;
        }

        return self::iconv('UTF-8', $encoding, $result);
    }

    public static function mb_convert_case($s, $mode, $encoding = null)
    {
        $s = (string) $s;
        if ('' === $s) {
            return '';
        }

        $encoding = self::getEncoding($encoding);

        if ('UTF-8' === $encoding) {
            $encoding = null;
            if (!preg_match('//u', $s)) {
                $s = @self::iconv('UTF-8', 'UTF-8', $s);
            }
        } else {
            $s = self::iconv($encoding, 'UTF-8', $s);
        }

        if (\MB_CASE_TITLE == $mode) {
            static $titleRegexp = null;
            if (null === $titleRegexp) {
                $titleRegexp = self::getData('titleCaseRegexp');
            }
            $s = preg_replace_callback($titleRegexp, [__CLASS__, 'title_case'], $s);
        } else {
            if (\MB_CASE_UPPER == $mode) {
                static $upper = null;
                if (null === $upper) {
                    $upper = self::getData('upperCase');
                }
                $map = $upper;
            } else {
                if (self::MB_CASE_FOLD === $mode) {
                    static $caseFolding = null;
                    if (null === $caseFolding) {
                        $caseFolding = self::getData('caseFolding');
                    }
                    $s = strtr($s, $caseFolding);
                }

                static $lower = null;
                if (null === $lower) {
                    $lower = self::getData('lowerCase');
                }
                $map = $lower;
            }

            static $ulenMask = ["\xC0" => 2, "\xD0" => 2, "\xE0" => 3, "\xF0" => 4];

            $i = 0;
            $len = \strlen($s);

            while ($i < $len) {
                $ulen = $s[$i] < "\x80" ? 1 : $ulenMask[$s[$i] & "\xF0"];
                $uchr = substr($s, $i, $ulen);
                $i += $ulen;

                if (isset($map[$uchr])) {
                    $uchr = $map[$uchr];
                    $nlen = \strlen($uchr);

                    if ($nlen == $ulen) {
                        $nlen = $i;
                        do {
                            $s[--$nlen] = $uchr[--$ulen];
                        } while ($ulen);
                    } else {
                        $s = substr_replace($s, $uchr, $i - $ulen, $ulen);
                        $len += $nlen - $ulen;
                        $i += $nlen - $ulen;
                    }
                }
            }
        }

        if (null === $encoding) {
            return $s;
        }

        return self::iconv('UTF-8', $encoding, $s);
    }

    public static function mb_internal_encoding($encoding = null)
    {
        if (null === $encoding) {
            return self::$internalEncoding;
        }

        $normalizedEncoding = self::getEncoding($encoding);

        if ('UTF-8' === $normalizedEncoding || false !== @iconv($normalizedEncoding, $normalizedEncoding, ' ')) {
            self::$internalEncoding = $normalizedEncoding;

            return true;
        }

        if (80000 > \PHP_VERSION_ID) {
            return false;
        }

        throw new \ValueError(\sprintf('Argument #1 ($encoding) must be a valid encoding, "%s" given', $encoding));
    }

    public static function mb_language($lang = null)
    {
        if (null === $lang) {
            return self::$language;
        }

        switch ($normalizedLang = strtolower($lang)) {
            case 'uni':
            case 'neutral':
                self::$language = $normalizedLang;

                return true;
        }

        if (80000 > \PHP_VERSION_ID) {
            return false;
        }

        throw new \ValueError(\sprintf('Argument #1 ($language) must be a valid language, "%s" given', $lang));
    }

    public static function mb_list_encodings()
    {
        return ['UTF-8'];
    }

    public static function mb_encoding_aliases($encoding)
    {
        switch (strtoupper($encoding)) {
            case 'UTF8':
            case 'UTF-8':
                return ['utf8'];
        }

        return false;
    }

    public static function mb_check_encoding($var = null, $encoding = null)
    {
        if (null === $encoding) {
            if (null === $var) {
                return false;
            }
            $encoding = self::$internalEncoding;
        }

        if (!\is_array($var)) {
            return self::mb_detect_encoding($var, [$encoding]) || false !== @iconv($encoding, $encoding, $var);
        }

        foreach ($var as $key => $value) {
            if (!self::mb_check_encoding($key, $encoding)) {
                return false;
            }
            if (!self::mb_check_encoding($value, $encoding)) {
                return false;
            }
        }

        return true;
    }

    public static function mb_detect_encoding($str, $encodingList = null, $strict = false)
    {
        if (null === $encodingList) {
            $encodingList = self::$encodingList;
        } else {
            if (!\is_array($encodingList)) {
                $encodingList = array_map('trim', explode(',', $encodingList));
            }
            $encodingList = array_map('strtoupper', $encodingList);
        }

        foreach ($encodingList as $enc) {
            switch ($enc) {
                case 'ASCII':
                    if (!preg_match('/[\x80-\xFF]/', $str)) {
                        return $enc;
                    }
                    break;

                case 'UTF8':
                case 'UTF-8':
                    if (preg_match('//u', $str)) {
                        return 'UTF-8';
                    }
                    break;

                default:
                    if (0 === strncmp($enc, 'ISO-8859-', 9)) {
                        return $enc;
                    }
            }
        }

        return false;
    }

    public static function mb_detect_order($encodingList = null)
    {
        if (null === $encodingList) {
            return self::$encodingList;
        }

        if (!\is_array($encodingList)) {
            $encodingList = array_map('trim', explode(',', $encodingList));
        }
        $encodingList = array_map('strtoupper', $encodingList);

        foreach ($encodingList as $enc) {
            switch ($enc) {
                default:
                    if (strncmp($enc, 'ISO-8859-', 9)) {
                        return false;
                    }
                    // no break
                case 'ASCII':
                case 'UTF8':
                case 'UTF-8':
            }
        }

        self::$encodingList = $encodingList;

        return true;
    }

    public static function mb_strlen($s, $encoding = null)
    {
        $encoding = self::getEncoding($encoding);
        if ('CP850' === $encoding || 'ASCII' === $encoding) {
            return \strlen($s);
        }

        if (false !== $len = @iconv_strlen($s, $encoding)) {
            return $len;
        }

        if ('UTF-8' !== $encoding) {
            return $len;
        }

        return preg_match_all('/[\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]?|[\xE0-\xEF][\x80-\xBF]{0,2}|[\xF0-\xF7][\x80-\xBF]{0,3}|[\xF8-\xFB][\x80-\xBF]{0,4}|[\xFC-\xFD][\x80-\xBF]{0,5}|[\x80-\xBF\xFE\xFF]/s', $s);
    }

    public static function mb_strpos($haystack, $needle, $offset = 0, $encoding = null)
    {
        $encoding = self::getEncoding($encoding);
        if ('CP850' === $encoding || 'ASCII' === $encoding) {
            return strpos($haystack, $needle, $offset);
        }

        $needle = (string) $needle;
        if ('' === $needle) {
            if (80000 > \PHP_VERSION_ID) {
                trigger_error(__METHOD__.': Empty delimiter', \E_USER_WARNING);

                return false;
            }

            return 0;
        }

        return iconv_strpos($haystack, $needle, $offset, $encoding);
    }

    public static function mb_strrpos($haystack, $needle, $offset = 0, $encoding = null)
    {
        $encoding = self::getEncoding($encoding);
        if ('CP850' === $encoding || 'ASCII' === $encoding) {
            return strrpos($haystack, $needle, $offset);
        }

        if ($offset != (int) $offset) {
            $offset = 0;
        } elseif ($offset = (int) $offset) {
            if ($offset < 0) {
                if (0 > $offset += self::mb_strlen($needle)) {
                    $haystack = self::mb_substr($haystack, 0, $offset, $encoding);
                }
                $offset = 0;
            } else {
                $haystack = self::mb_substr($haystack, $offset, 2147483647, $encoding);
            }
        }

        $pos = '' !== $needle || 80000 > \PHP_VERSION_ID
            ? iconv_strrpos($haystack, $needle, $encoding)
            : self::mb_strlen($haystack, $encoding);

        return false !== $pos ? $offset + $pos : false;
    }

    public static function mb_str_split($string, $split_length = 1, $encoding = null)
    {
        if (null !== $string && !\is_scalar($string) && !(\is_object($string) && method_exists($string, '__toString'))) {
            trigger_error('mb_str_split() expects parameter 1 to be string, '.\gettype($string).' given', \E_USER_WARNING);

            return null;
        }

        if (1 > $split_length = (int) $split_length) {
            if (80000 > \PHP_VERSION_ID) {
                trigger_error('The length of each segment must be greater than zero', \E_USER_WARNING);

                return false;
            }

            throw new \ValueError('Argument #2 ($length) must be greater than 0');
        }

        if (null === $encoding) {
            $encoding = mb_internal_encoding();
        }

        if ('UTF-8' === $encoding = self::getEncoding($encoding)) {
            $rx = '/(';
            while (65535 < $split_length) {
                $rx .= '.{65535}';
                $split_length -= 65535;
            }
            $rx .= '.{'.$split_length.'})/us';

            return preg_split($rx, $string, -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);
        }

        $result = [];
        $length = mb_strlen($string, $encoding);

        for ($i = 0; $i < $length; $i += $split_length) {
            $result[] = mb_substr($string, $i, $split_length, $encoding);
        }

        return $result;
    }

    public static function mb_strtolower($s, $encoding = null)
    {
        return self::mb_convert_case($s, \MB_CASE_LOWER, $encoding);
    }

    public static function mb_strtoupper($s, $encoding = null)
    {
        return self::mb_convert_case($s, \MB_CASE_UPPER, $encoding);
    }

    public static function mb_substitute_character($c = null)
    {
        if (null === $c) {
            return 'none';
        }
        if (0 === strcasecmp($c, 'none')) {
            return true;
        }
        if (80000 > \PHP_VERSION_ID) {
            return false;
        }
        if (\is_int($c) || 'long' === $c || 'entity' === $c) {
            return false;
        }

        throw new \ValueError('Argument #1 ($substitute_character) must be "none", "long", "entity" or a valid codepoint');
    }

    public static function mb_substr($s, $start, $length = null, $encoding = null)
    {
        $encoding = self::getEncoding($encoding);
        if ('CP850' === $encoding || 'ASCII' === $encoding) {
            return (string) substr($s, $start, null === $length ? 2147483647 : $length);
        }

        if ($start < 0) {
            $start = iconv_strlen($s, $encoding) + $start;
            if ($start < 0) {
                $start = 0;
            }
        }

        if (null === $length) {
            $length = 2147483647;
        } elseif ($length < 0) {
            $length = iconv_strlen($s, $encoding) + $length - $start;
            if ($length < 0) {
                return '';
            }
        }

        return (string) iconv_substr($s, $start, $length, $encoding);
    }

    public static function mb_stripos($haystack, $needle, $offset = 0, $encoding = null)
    {
        [$haystack, $needle] = str_replace(self::SIMPLE_CASE_FOLD[0], self::SIMPLE_CASE_FOLD[1], [
            self::mb_convert_case($haystack, \MB_CASE_LOWER, $encoding),
            self::mb_convert_case($needle, \MB_CASE_LOWER, $encoding),
        ]);

        return self::mb_strpos($haystack, $needle, $offset, $encoding);
    }

    public static function mb_stristr($haystack, $needle, $part = false, $encoding = null)
    {
        $pos = self::mb_stripos($haystack, $needle, 0, $encoding);

        return self::getSubpart($pos, $part, $haystack, $encoding);
    }

    public static function mb_strrchr($haystack, $needle, $part = false, $encoding = null)
    {
        $encoding = self::getEncoding($encoding);
        if ('CP850' === $encoding || 'ASCII' === $encoding) {
            $pos = strrpos($haystack, $needle);
        } else {
            $needle = self::mb_substr($needle, 0, 1, $encoding);
            $pos = iconv_strrpos($haystack, $needle, $encoding);
        }

        return self::getSubpart($pos, $part, $haystack, $encoding);
    }

    public static function mb_strrichr($haystack, $needle, $part = false, $encoding = null)
    {
        $needle = self::mb_substr($needle, 0, 1, $encoding);
        $pos = self::mb_strripos($haystack, $needle, $encoding);

        return self::getSubpart($pos, $part, $haystack, $encoding);
    }

    public static function mb_strripos($haystack, $needle, $offset = 0, $encoding = null)
    {
        $haystack = self::mb_convert_case($haystack, \MB_CASE_LOWER, $encoding);
        $needle = self::mb_convert_case($needle, \MB_CASE_LOWER, $encoding);

        $haystack = str_replace(self::SIMPLE_CASE_FOLD[0], self::SIMPLE_CASE_FOLD[1], $haystack);
        $needle = str_replace(self::SIMPLE_CASE_FOLD[0], self::SIMPLE_CASE_FOLD[1], $needle);

        return self::mb_strrpos($haystack, $needle, $offset, $encoding);
    }

    public static function mb_strstr($haystack, $needle, $part = false, $encoding = null)
    {
        $pos = strpos($haystack, $needle);
        if (false === $pos) {
            return false;
        }
        if ($part) {
            return substr($haystack, 0, $pos);
        }

        return substr($haystack, $pos);
    }

    public static function mb_get_info($type = 'all')
    {
        $info = [
            'internal_encoding' => self::$internalEncoding,
            'http_output' => 'pass',
            'http_output_conv_mimetypes' => '^(text/|application/xhtml\+xml)',
            'func_overload' => 0,
            'func_overload_list' => 'no overload',
            'mail_charset' => 'UTF-8',
            'mail_header_encoding' => 'BASE64',
            'mail_body_encoding' => 'BASE64',
            'illegal_chars' => 0,
            'encoding_translation' => 'Off',
            'language' => self::$language,
            'detect_order' => self::$encodingList,
            'substitute_character' => 'none',
            'strict_detection' => 'Off',
        ];

        if ('all' === $type) {
            return $info;
        }
        if (isset($info[$type])) {
            return $info[$type];
        }

        return false;
    }

    public static function mb_http_input($type = '')
    {
        return false;
    }

    public static function mb_http_output($encoding = null)
    {
        return null !== $encoding ? 'pass' === $encoding : 'pass';
    }

    public static function mb_strwidth($s, $encoding = null)
    {
        $encoding = self::getEncoding($encoding);

        if ('UTF-8' !== $encoding) {
            $s = self::iconv($encoding, 'UTF-8', $s);
        }

        $s = preg_replace('/[\x{1100}-\x{115F}\x{2329}\x{232A}\x{2E80}-\x{303E}\x{3040}-\x{A4CF}\x{AC00}-\x{D7A3}\x{F900}-\x{FAFF}\x{FE10}-\x{FE19}\x{FE30}-\x{FE6F}\x{FF00}-\x{FF60}\x{FFE0}-\x{FFE6}\x{20000}-\x{2FFFD}\x{30000}-\x{3FFFD}]/u', '', $s, -1, $wide);

        return ($wide << 1) + iconv_strlen($s, 'UTF-8');
    }

    public static function mb_substr_count($haystack, $needle, $encoding = null)
    {
        return substr_count($haystack, $needle);
    }

    public static function mb_output_handler($contents, $status)
    {
        return $contents;
    }

    public static function mb_chr($code, $encoding = null)
    {
        if (0x80 > $code %= 0x200000) {
            $s = \chr($code);
        } elseif (0x800 > $code) {
            $s = \chr(0xC0 | $code >> 6).\chr(0x80 | $code & 0x3F);
        } elseif (0x10000 > $code) {
            $s = \chr(0xE0 | $code >> 12).\chr(0x80 | $code >> 6 & 0x3F).\chr(0x80 | $code & 0x3F);
        } else {
            $s = \chr(0xF0 | $code >> 18).\chr(0x80 | $code >> 12 & 0x3F).\chr(0x80 | $code >> 6 & 0x3F).\chr(0x80 | $code & 0x3F);
        }

        if ('UTF-8' !== $encoding = self::getEncoding($encoding)) {
            $s = mb_convert_encoding($s, $encoding, 'UTF-8');
        }

        return $s;
    }

    public static function mb_ord($s, $encoding = null)
    {
        if ('UTF-8' !== $encoding = self::getEncoding($encoding)) {
            $s = mb_convert_encoding($s, 'UTF-8', $encoding);
        }

        if (1 === \strlen($s)) {
            return \ord($s);
        }

        $code = ($s = unpack('C*', substr($s, 0, 4))) ? $s[1] : 0;
        if (0xF0 <= $code) {
            return (($code - 0xF0) << 18) + (($s[2] - 0x80) << 12) + (($s[3] - 0x80) << 6) + $s[4] - 0x80;
        }
        if (0xE0 <= $code) {
            return (($code - 0xE0) << 12) + (($s[2] - 0x80) << 6) + $s[3] - 0x80;
        }
        if (0xC0 <= $code) {
            return (($code - 0xC0) << 6) + $s[2] - 0x80;
        }

        return $code;
    }

    /** @return string|false */
    public static function mb_scrub(?string $string, ?string $encoding = null): string
    {
        if (null === $encoding) {
            $encoding = self::mb_internal_encoding();
        } elseif (!self::assertEncoding($encoding, 'mb_scrub(): Argument #2 ($encoding) must be a valid encoding, "%s" given')) {
            return false;
        }

        return self::mb_convert_encoding((string) $string, $encoding, $encoding);
    }

    /** @return string|false */
    public static function mb_str_pad(string $string, int $length, string $pad_string = ' ', int $pad_type = \STR_PAD_RIGHT, ?string $encoding = null)
    {
        if (null === $encoding) {
            $encoding = self::mb_internal_encoding();
        } elseif (!self::assertEncoding($encoding, 'mb_str_pad(): Argument #5 ($encoding) must be a valid encoding, "%s" given')) {
            return false;
        }

        if (self::mb_strlen($pad_string, $encoding) <= 0) {
            if (\PHP_VERSION_ID < 80000) {
                trigger_error('mb_str_pad(): Argument #3 ($pad_string) must be a non-empty string', \E_USER_WARNING);

                return false;
            }

            throw new \ValueError('mb_str_pad(): Argument #3 ($pad_string) must be a non-empty string');
        }

        if (!\in_array($pad_type, [\STR_PAD_RIGHT, \STR_PAD_LEFT, \STR_PAD_BOTH], true)) {
            if (\PHP_VERSION_ID < 80000) {
                trigger_error('mb_str_pad(): Argument #4 ($pad_type) must be STR_PAD_LEFT, STR_PAD_RIGHT, or STR_PAD_BOTH', \E_USER_WARNING);

                return false;
            }

            throw new \ValueError('mb_str_pad(): Argument #4 ($pad_type) must be STR_PAD_LEFT, STR_PAD_RIGHT, or STR_PAD_BOTH');
        }

        $paddingRequired = $length - self::mb_strlen($string, $encoding);

        if ($paddingRequired < 1) {
            return $string;
        }

        switch ($pad_type) {
            case \STR_PAD_LEFT:
                return self::mb_substr(str_repeat($pad_string, $paddingRequired), 0, $paddingRequired, $encoding).$string;
            case \STR_PAD_RIGHT:
                return $string.self::mb_substr(str_repeat($pad_string, $paddingRequired), 0, $paddingRequired, $encoding);
            default:
                $leftPaddingLength = floor($paddingRequired / 2);
                $rightPaddingLength = $paddingRequired - $leftPaddingLength;

                return self::mb_substr(str_repeat($pad_string, $leftPaddingLength), 0, $leftPaddingLength, $encoding).$string.self::mb_substr(str_repeat($pad_string, $rightPaddingLength), 0, $rightPaddingLength, $encoding);
        }
    }

    /** @return string|false */
    public static function mb_ucfirst(string $string, ?string $encoding = null)
    {
        if (null === $encoding) {
            $encoding = self::mb_internal_encoding();
        } elseif (!self::assertEncoding($encoding, 'mb_ucfirst(): Argument #2 ($encoding) must be a valid encoding, "%s" given')) {
            return false;
        }

        $firstChar = mb_substr($string, 0, 1, $encoding);
        $firstChar = mb_convert_case($firstChar, \MB_CASE_TITLE, $encoding);

        return $firstChar.mb_substr($string, 1, null, $encoding);
    }

    /** @return string|false */
    public static function mb_lcfirst(string $string, ?string $encoding = null)
    {
        if (null === $encoding) {
            $encoding = self::mb_internal_encoding();
        } elseif (!self::assertEncoding($encoding, 'mb_lcfirst(): Argument #2 ($encoding) must be a valid encoding, "%s" given')) {
            return false;
        }

        $firstChar = mb_substr($string, 0, 1, $encoding);
        $firstChar = mb_convert_case($firstChar, \MB_CASE_LOWER, $encoding);

        return $firstChar.mb_substr($string, 1, null, $encoding);
    }

    /** @return string|false */
    public static function mb_trim(string $string, ?string $characters = null, ?string $encoding = null)
    {
        return self::mb_internal_trim('{^[%s]+|[%1$s]+$}Du', $string, $characters, $encoding, __FUNCTION__);
    }

    /** @return string|false */
    public static function mb_ltrim(string $string, ?string $characters = null, ?string $encoding = null)
    {
        return self::mb_internal_trim('{^[%s]+}Du', $string, $characters, $encoding, __FUNCTION__);
    }

    /** @return string|false */
    public static function mb_rtrim(string $string, ?string $characters = null, ?string $encoding = null)
    {
        return self::mb_internal_trim('{[%s]+$}Du', $string, $characters, $encoding, __FUNCTION__);
    }

    private static function getSubpart($pos, $part, $haystack, $encoding)
    {
        if (false === $pos) {
            return false;
        }
        if ($part) {
            return self::mb_substr($haystack, 0, $pos, $encoding);
        }

        return self::mb_substr($haystack, $pos, null, $encoding);
    }

    private static function html_encoding_callback(array $m)
    {
        $i = 1;
        $entities = '';
        $m = unpack('C*', htmlentities($m[0], \ENT_COMPAT, 'UTF-8'));

        while (isset($m[$i])) {
            if (0x80 > $m[$i]) {
                $entities .= \chr($m[$i++]);
                continue;
            }
            if (0xF0 <= $m[$i]) {
                $c = (($m[$i++] - 0xF0) << 18) + (($m[$i++] - 0x80) << 12) + (($m[$i++] - 0x80) << 6) + $m[$i++] - 0x80;
            } elseif (0xE0 <= $m[$i]) {
                $c = (($m[$i++] - 0xE0) << 12) + (($m[$i++] - 0x80) << 6) + $m[$i++] - 0x80;
            } else {
                $c = (($m[$i++] - 0xC0) << 6) + $m[$i++] - 0x80;
            }

            $entities .= '&#'.$c.';';
        }

        return $entities;
    }

    private static function title_case(array $s)
    {
        return self::mb_convert_case($s[1], \MB_CASE_UPPER, 'UTF-8').self::mb_convert_case($s[2], \MB_CASE_LOWER, 'UTF-8');
    }

    private static function getData($file)
    {
        if (file_exists($file = __DIR__.'/Resources/unidata/'.$file.'.php')) {
            return require $file;
        }

        return false;
    }

    private static function getEncoding($encoding)
    {
        if (null === $encoding) {
            return self::$internalEncoding;
        }

        if ('UTF-8' === $encoding) {
            return 'UTF-8';
        }

        $encoding = strtoupper($encoding);

        if ('8BIT' === $encoding || 'BINARY' === $encoding) {
            return 'CP850';
        }

        if ('UTF8' === $encoding) {
            return 'UTF-8';
        }

        if ('UTF-32' === $encoding) {
            return 'UTF-32BE';
        }

        if ('UTF-16' === $encoding) {
            return 'UTF-16BE';
        }

        return $encoding;
    }

    private static function iconv($fromEncoding, $toEncoding, $s)
    {
        if (null === self::$iconvSupportsIgnore) {
            self::$iconvSupportsIgnore = false !== @iconv('UTF-8', 'UTF-8//IGNORE', '');
        }

        return self::$iconvSupportsIgnore
            ? iconv($fromEncoding, $toEncoding.'//IGNORE', $s)
            : iconv($fromEncoding, $toEncoding, $s);
    }

    /** @return string|false */
    private static function mb_internal_trim(string $regex, string $string, ?string $characters, ?string $encoding, string $function)
    {
        if (null === $encoding) {
            $encoding = self::mb_internal_encoding();
        } elseif (!self::assertEncoding($encoding, $function.'(): Argument #3 ($encoding) must be a valid encoding, "%s" given')) {
            return false;
        }

        if ('' === $characters) {
            return null === $encoding ? $string : self::mb_convert_encoding($string, $encoding);
        }

        if ('UTF-8' === $encoding) {
            $encoding = null;
            if (!preg_match('//u', $string)) {
                $string = @self::iconv('UTF-8', 'UTF-8', $string);
            }
            if (null !== $characters && !preg_match('//u', $characters)) {
                $characters = @self::iconv('UTF-8', 'UTF-8', $characters);
            }
        } else {
            $string = self::iconv($encoding, 'UTF-8', $string);

            if (null !== $characters) {
                $characters = self::iconv($encoding, 'UTF-8', $characters);
            }
        }

        if (null === $characters) {
            $characters = "\\0 \f\n\r\t\v\u{00A0}\u{1680}\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}\u{200A}\u{2028}\u{2029}\u{202F}\u{205F}\u{3000}\u{0085}\u{180E}";
        } else {
            $characters = preg_quote($characters);
        }

        $string = preg_replace(\sprintf($regex, $characters), '', $string);

        if (null === $encoding) {
            return $string;
        }

        return self::iconv('UTF-8', $encoding, $string);
    }

    private static function assertEncoding(string $encoding, string $errorFormat): bool
    {
        try {
            $validEncoding = @self::mb_check_encoding('', $encoding);
        } catch (\ValueError $e) {
            throw new \ValueError(\sprintf($errorFormat, $encoding));
        }

        if (!$validEncoding) {
            if (80000 > \PHP_VERSION_ID) {
                trigger_error(\sprintf($errorFormat, $encoding), \E_USER_WARNING);
            } else {
                throw new \ValueError(\sprintf($errorFormat, $encoding));
            }
        }

        return $validEncoding;
    }
}
