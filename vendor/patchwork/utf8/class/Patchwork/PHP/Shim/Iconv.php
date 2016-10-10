<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2013 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

namespace Patchwork\PHP\Shim;

/**
 * iconv implementation in pure PHP, UTF-8 centric.
 *
 * Implemented:
 * - iconv              - Convert string to requested character encoding
 * - iconv_mime_decode  - Decodes a MIME header field
 * - iconv_mime_decode_headers - Decodes multiple MIME header fields at once
 * - iconv_get_encoding - Retrieve internal configuration variables of iconv extension
 * - iconv_set_encoding - Set current setting for character encoding conversion
 * - iconv_mime_encode  - Composes a MIME header field
 * - ob_iconv_handler   - Convert character encoding as output buffer handler
 * - iconv_strlen       - Returns the character count of string
 * - iconv_strpos       - Finds position of first occurrence of a needle within a haystack
 * - iconv_strrpos      - Finds the last occurrence of a needle within a haystack
 * - iconv_substr       - Cut out part of a string
 *
 * Charsets available for convertion are defined by files
 * in the charset/ directory and by Iconv::$alias below.
 * You're welcome to send back any addition you make.
 */
class Iconv
{
    const

    ERROR_ILLEGAL_CHARACTER = 'iconv(): Detected an illegal character in input string',
    ERROR_WRONG_CHARSET     = 'iconv(): Wrong charset, conversion from `%s\' to `%s\' is not allowed';


    public static

    $input_encoding = 'utf-8',
    $output_encoding = 'utf-8',
    $internal_encoding = 'utf-8';

    protected static

    $alias = array(
        'utf8' => 'utf-8',
        'ascii' => 'us-ascii',
        'tis-620' => 'iso-8859-11',
        'cp1250' => 'windows-1250',
        'cp1251' => 'windows-1251',
        'cp1252' => 'windows-1252',
        'cp1253' => 'windows-1253',
        'cp1254' => 'windows-1254',
        'cp1255' => 'windows-1255',
        'cp1256' => 'windows-1256',
        'cp1257' => 'windows-1257',
        'cp1258' => 'windows-1258',
        'shift-jis' => 'cp932',
        'shift_jis' => 'cp932',
        'latin1' => 'iso-8859-1',
        'latin2' => 'iso-8859-2',
        'latin3' => 'iso-8859-3',
        'latin4' => 'iso-8859-4',
        'latin5' => 'iso-8859-9',
        'latin6' => 'iso-8859-10',
        'latin7' => 'iso-8859-13',
        'latin8' => 'iso-8859-14',
        'latin9' => 'iso-8859-15',
        'latin10' => 'iso-8859-16',
        'iso8859-1' => 'iso-8859-1',
        'iso8859-2' => 'iso-8859-2',
        'iso8859-3' => 'iso-8859-3',
        'iso8859-4' => 'iso-8859-4',
        'iso8859-5' => 'iso-8859-5',
        'iso8859-6' => 'iso-8859-6',
        'iso8859-7' => 'iso-8859-7',
        'iso8859-8' => 'iso-8859-8',
        'iso8859-9' => 'iso-8859-9',
        'iso8859-10' => 'iso-8859-10',
        'iso8859-11' => 'iso-8859-11',
        'iso8859-12' => 'iso-8859-12',
        'iso8859-13' => 'iso-8859-13',
        'iso8859-14' => 'iso-8859-14',
        'iso8859-15' => 'iso-8859-15',
        'iso8859-16' => 'iso-8859-16',
        'iso_8859-1' => 'iso-8859-1',
        'iso_8859-2' => 'iso-8859-2',
        'iso_8859-3' => 'iso-8859-3',
        'iso_8859-4' => 'iso-8859-4',
        'iso_8859-5' => 'iso-8859-5',
        'iso_8859-6' => 'iso-8859-6',
        'iso_8859-7' => 'iso-8859-7',
        'iso_8859-8' => 'iso-8859-8',
        'iso_8859-9' => 'iso-8859-9',
        'iso_8859-10' => 'iso-8859-10',
        'iso_8859-11' => 'iso-8859-11',
        'iso_8859-12' => 'iso-8859-12',
        'iso_8859-13' => 'iso-8859-13',
        'iso_8859-14' => 'iso-8859-14',
        'iso_8859-15' => 'iso-8859-15',
        'iso_8859-16' => 'iso-8859-16',
        'iso88591' => 'iso-8859-1',
        'iso88592' => 'iso-8859-2',
        'iso88593' => 'iso-8859-3',
        'iso88594' => 'iso-8859-4',
        'iso88595' => 'iso-8859-5',
        'iso88596' => 'iso-8859-6',
        'iso88597' => 'iso-8859-7',
        'iso88598' => 'iso-8859-8',
        'iso88599' => 'iso-8859-9',
        'iso885910' => 'iso-8859-10',
        'iso885911' => 'iso-8859-11',
        'iso885912' => 'iso-8859-12',
        'iso885913' => 'iso-8859-13',
        'iso885914' => 'iso-8859-14',
        'iso885915' => 'iso-8859-15',
        'iso885916' => 'iso-8859-16',
    ),

    $translit_map = array(),
    $convert_map = array(),
    $error_handler,
    $last_error,

    $ulen_mask = array("\xC0" => 2, "\xD0" => 2, "\xE0" => 3, "\xF0" => 4),
    $is_valid_utf8;


    static function iconv($in_charset, $out_charset, $str)
    {
        if ('' === $str .= '') return '';

        // Prepare for //IGNORE and //TRANSLIT

        $TRANSLIT = $IGNORE = '';

        $out_charset = strtolower($out_charset);
        $in_charset  = strtolower($in_charset );

        '' === $out_charset && $out_charset = 'iso-8859-1';
        '' ===  $in_charset &&  $in_charset = 'iso-8859-1';

        if ('//translit' === substr($out_charset, -10))
        {
            $TRANSLIT = '//TRANSLIT';
            $out_charset = substr($out_charset, 0, -10);
        }

        if ('//ignore' === substr($out_charset, -8))
        {
            $IGNORE = '//IGNORE';
            $out_charset = substr($out_charset, 0, -8);
        }

        '//translit' === substr($in_charset, -10) && $in_charset = substr($in_charset, 0, -10);
        '//ignore'   === substr($in_charset,  -8) && $in_charset = substr($in_charset, 0,  -8);

        isset(self::$alias[ $in_charset]) &&  $in_charset = self::$alias[ $in_charset];
        isset(self::$alias[$out_charset]) && $out_charset = self::$alias[$out_charset];


        // Load charset maps

        if ( ('utf-8' !==  $in_charset && !static::loadMap('from.',  $in_charset,  $in_map))
          || ('utf-8' !== $out_charset && !static::loadMap(  'to.', $out_charset, $out_map)) )
        {
            user_error(sprintf(self::ERROR_WRONG_CHARSET, $in_charset, $out_charset));
            return false;
        }


        if ('utf-8' !== $in_charset)
        {
            // Convert input to UTF-8
            $result = '';
            if (self::map_to_utf8($result, $in_map, $str, $IGNORE)) $str = $result;
            else $str = false;
            self::$is_valid_utf8 = true;
        }
        else
        {
            self::$is_valid_utf8 = preg_match('//u', $str);

            if (!self::$is_valid_utf8 && !$IGNORE)
            {
                user_error(self::ERROR_ILLEGAL_CHARACTER);
                return false;
            }

            if ('utf-8' === $out_charset)
            {
                // UTF-8 validation
                $str = self::utf8_to_utf8($str, $IGNORE);
            }
        }

        if ('utf-8' !== $out_charset && false !== $str)
        {
            // Convert output to UTF-8
            $result = '';
            if (self::map_from_utf8($result, $out_map, $str, $IGNORE, $TRANSLIT)) return $result;
            else return false;
        }
        else return $str;
    }

    static function iconv_mime_decode_headers($str, $mode = 0, $charset = INF)
    {
        INF === $charset && $charset = self::$internal_encoding;

        false !== strpos($str, "\r") && $str = strtr(str_replace("\r\n", "\n", $str), "\r", "\n");
        $str = explode("\n\n", $str, 2);

        $headers = array();

        $str = preg_split('/\n(?![ \t])/', $str[0]);
        foreach ($str as $str)
        {
            $str = self::iconv_mime_decode($str, $mode, $charset);
            if (false === $str) return false;
            $str = explode(':', $str, 2);

            if (2 === count($str))
            {
                if (isset($headers[$str[0]]))
                {
                    is_array($headers[$str[0]]) || $headers[$str[0]] = array($headers[$str[0]]);
                    $headers[$str[0]][] = ltrim($str[1]);
                }
                else $headers[$str[0]] = ltrim($str[1]);
            }
        }

        return $headers;
    }

    static function iconv_mime_decode($str, $mode = 0, $charset = INF)
    {
        INF === $charset && $charset = self::$internal_encoding;
        if (ICONV_MIME_DECODE_CONTINUE_ON_ERROR & $mode) $charset .= '//IGNORE';

        false !== strpos($str, "\r") && $str = strtr(str_replace("\r\n", "\n", $str), "\r", "\n");
        $str = preg_split('/\n(?![ \t])/', rtrim($str), 2);
        $str = preg_replace('/[ \t]*\n[ \t]+/', ' ', rtrim($str[0]));
        $str = preg_split('/=\?([^?]+)\?([bqBQ])\?(.*?)\?=/', $str, -1, PREG_SPLIT_DELIM_CAPTURE);

        $result = self::iconv('utf-8', $charset, $str[0]);
        if (false === $result) return false;

        $i = 1;
        $len = count($str);

        while ($i < $len)
        {
            $c = strtolower($str[$i]);
            if ( (ICONV_MIME_DECODE_CONTINUE_ON_ERROR & $mode)
              && 'utf-8' !== $c
              && !isset(self::$alias[$c])
              && !static::loadMap('from.', $c,  $d) ) $d = false;
            else if ('B' === strtoupper($str[$i+1])) $d = base64_decode($str[$i+2]);
            else $d = rawurldecode(strtr(str_replace('%', '%25', $str[$i+2]), '=_', '% '));

            if (false !== $d)
            {
                $result .= self::iconv($c, $charset, $d);
                $d = self::iconv('utf-8' , $charset, $str[$i+3]);
                if ('' !== trim($d)) $result .= $d;
            }
            else if (ICONV_MIME_DECODE_CONTINUE_ON_ERROR & $mode)
            {
                $result .= "=?{$str[$i]}?{$str[$i+1]}?{$str[$i+2]}?={$str[$i+3]}";
            }
            else
            {
                $result = false;
                break;
            }

            $i += 4;
        }

        return $result;
    }

    static function iconv_get_encoding($type = 'all')
    {
        switch ($type)
        {
        case 'input_encoding'   : return self::$input_encoding;
        case 'output_encoding'  : return self::$output_encoding;
        case 'internal_encoding': return self::$internal_encoding;
        }

        return array(
            'input_encoding'    => self::$input_encoding,
            'output_encoding'   => self::$output_encoding,
            'internal_encoding' => self::$internal_encoding
        );
    }

    static function iconv_set_encoding($type, $charset)
    {
        switch ($type)
        {
        case 'input_encoding'   : self::$input_encoding    = $charset; break;
        case 'output_encoding'  : self::$output_encoding   = $charset; break;
        case 'internal_encoding': self::$internal_encoding = $charset; break;

        default: return false;
        }

        return true;
    }

    static function iconv_mime_encode($field_name, $field_value, $pref = INF)
    {
        is_array($pref) || $pref = array();

        $pref += array(
            'scheme'           => 'B',
            'input-charset'    => self::$internal_encoding,
            'output-charset'   => self::$internal_encoding,
            'line-length'      => 76,
            'line-break-chars' => "\r\n"
        );

        preg_match('/[\x80-\xFF]/', $field_name) && $field_name = '';

        $scheme = strtoupper(substr($pref['scheme'], 0, 1));
        $in  = strtolower($pref['input-charset']);
        $out = strtolower($pref['output-charset']);

        if ('utf-8' !== $in && false === $field_value = self::iconv($in, 'utf-8', $field_value)) return false;

        preg_match_all('/./us', $field_value, $chars);

        $chars = isset($chars[0]) ? $chars[0] : array();

        $line_break  = (int) $pref['line-length'];
        $line_start  = "=?{$pref['output-charset']}?{$scheme}?";
        $line_length = strlen($field_name) + 2 + strlen($line_start) + 2;
        $line_offset = strlen($line_start) + 3;
        $line_data   = '';

        $field_value = array();

        $Q = 'Q' === $scheme;

        foreach ($chars as $c)
        {
            if ('utf-8' !== $out && false === $c = self::iconv('utf-8', $out, $c)) return false;

            $o = $Q
                ? $c = preg_replace_callback(
                    '/[=_\?\x00-\x1F\x80-\xFF]/',
                    array(__CLASS__, 'qp_byte_callback'),
                    $c
                )
                : base64_encode($line_data . $c);

            if (isset($o[$line_break - $line_length]))
            {
                $Q || $line_data = base64_encode($line_data);
                $field_value[] = $line_start . $line_data . '?=';
                $line_length = $line_offset;
                $line_data = '';
            }

            $line_data .= $c;
            $Q && $line_length += strlen($c);
        }

        if ('' !== $line_data)
        {
            $Q || $line_data = base64_encode($line_data);
            $field_value[] = $line_start . $line_data . '?=';
        }

        return $field_name . ': ' . implode($pref['line-break-chars'] . ' ', $field_value);
    }

    static function ob_iconv_handler($buffer, $mode)
    {
        return self::iconv(self::$internal_encoding, self::$output_encoding, $buffer);
    }

    static function iconv_strlen($s, $encoding = INF)
    {
/**/    if (extension_loaded('xml'))
            return self::strlen1($s, $encoding);
/**/    else
            return self::strlen2($s, $encoding);
    }

    static function strlen1($s, $encoding = INF)
    {
        INF === $encoding && $encoding = self::$internal_encoding;
        if (0 !== strncasecmp($encoding, 'utf-8', 5) && false === $s = self::iconv($encoding, 'utf-8', $s)) return false;

        return strlen(utf8_decode($s));
    }

    static function strlen2($s, $encoding = INF)
    {
        INF === $encoding && $encoding = self::$internal_encoding;
        if (0 !== strncasecmp($encoding, 'utf-8', 5) && false === $s = self::iconv($encoding, 'utf-8', $s)) return false;

        $ulen_mask = self::$ulen_mask;

        $i = 0; $j = 0;
        $len = strlen($s);

        while ($i < $len)
        {
            $u = $s[$i] & "\xF0";
            $i += isset($ulen_mask[$u]) ? $ulen_mask[$u] : 1;
            ++$j;
        }

        return $j;
    }

    static function iconv_strpos($haystack, $needle, $offset = 0, $encoding = INF)
    {
        INF === $encoding && $encoding = self::$internal_encoding;

        if (0 !== strncasecmp($encoding, 'utf-8', 5))
        {
            if (false === $haystack = self::iconv($encoding, 'utf-8', $haystack)) return false;
            if (false === $needle = self::iconv($encoding, 'utf-8', $needle)) return false;
        }

        if ($offset = (int) $offset) $haystack = self::iconv_substr($haystack, $offset, 2147483647, 'utf-8');
        $pos = strpos($haystack, $needle);
        return false === $pos ? false : ($offset + ($pos ? self::iconv_strlen(substr($haystack, 0, $pos), 'utf-8') : 0));
    }

    static function iconv_strrpos($haystack, $needle, $encoding = INF)
    {
        INF === $encoding && $encoding = self::$internal_encoding;

        if (0 !== strncasecmp($encoding, 'utf-8', 5))
        {
            if (false === $haystack = self::iconv($encoding, 'utf-8', $haystack)) return false;
            if (false === $needle = self::iconv($encoding, 'utf-8', $needle)) return false;
        }

        $pos = isset($needle[0]) ? strrpos($haystack, $needle) : false;
        return false === $pos ? false : self::iconv_strlen($pos ? substr($haystack, 0, $pos) : $haystack, 'utf-8');
    }

    static function iconv_substr($s, $start, $length = 2147483647, $encoding = INF)
    {
        INF === $encoding && $encoding = self::$internal_encoding;
        if (0 === strncasecmp($encoding, 'utf-8', 5)) $encoding = INF;
        else if (false === $s = self::iconv($encoding, 'utf-8', $s)) return false;

        $s .= '';
        $slen = self::iconv_strlen($s, 'utf-8');
        $start = (int) $start;

        if (0 > $start) $start += $slen;
        if (0 > $start) return false;
        if ($start >= $slen) return false;

        $rx = $slen - $start;

        if (0 > $length) $length += $rx;
        if (0 === $length) return '';
        if (0 > $length) return false;

        if ($length > $rx) $length = $rx;

        $rx = '/^' . ($start ? self::preg_offset($start) : '') . '(' . self::preg_offset($length) . ')/u';

        $s = preg_match($rx, $s, $s) ? $s[1] : '';

        if (INF === $encoding) return $s;
        else return self::iconv('utf-8', $encoding, $s);
    }

    protected static function loadMap($type, $charset, &$map)
    {
        if (!isset(self::$convert_map[$type . $charset]))
        {
            if (false === $map = static::getData($type . $charset))
            {
                if ('to.' === $type && static::loadMap('from.', $charset, $map)) $map = array_flip($map);
                else return false;
            }

            self::$convert_map[$type . $charset] = $map;
        }
        else $map = self::$convert_map[$type . $charset];

        return true;
    }

    protected static function utf8_to_utf8($str, $IGNORE)
    {
        $ulen_mask = self::$ulen_mask;
        $valid     = self::$is_valid_utf8;

        $u = $str;
        $i = $j = 0;
        $len = strlen($str);

        while ($i < $len)
        {
            if ($str[$i] < "\x80") $u[$j++] = $str[$i++];
            else
            {
                $ulen = $str[$i] & "\xF0";
                $ulen = isset($ulen_mask[$ulen]) ? $ulen_mask[$ulen] : 1;
                $uchr = substr($str, $i, $ulen);

                if (1 === $ulen || !($valid || preg_match('/^.$/us', $uchr)))
                {
                    if ($IGNORE)
                    {
                        ++$i;
                        continue;
                    }

                    user_error(self::ERROR_ILLEGAL_CHARACTER);
                    return false;
                }
                else $i += $ulen;

                $u[$j++] = $uchr[0];

                   isset($uchr[1]) && 0 !== ($u[$j++] = $uchr[1])
                && isset($uchr[2]) && 0 !== ($u[$j++] = $uchr[2])
                && isset($uchr[3]) && 0 !== ($u[$j++] = $uchr[3]);
            }
        }

        return substr($u, 0, $j);
    }

    protected static function map_to_utf8(&$result, $map, $str, $IGNORE)
    {
        $len = strlen($str);
        for ($i = 0; $i < $len; ++$i)
        {
            if (isset($str[$i+1], $map[$str[$i] . $str[$i+1]])) $result .= $map[$str[$i] . $str[++$i]];
            else if (isset($map[$str[$i]])) $result .= $map[$str[$i]];
            else if (!$IGNORE)
            {
                user_error(self::ERROR_ILLEGAL_CHARACTER);
                return false;
            }
        }

        return true;
    }

    protected static function map_from_utf8(&$result, $map, $str, $IGNORE, $TRANSLIT)
    {
        $ulen_mask = self::$ulen_mask;
        $valid     = self::$is_valid_utf8;

        if ($TRANSLIT) self::$translit_map or self::$translit_map = static::getData('translit');

        $i = 0;
        $len = strlen($str);

        while ($i < $len)
        {
            if ($str[$i] < "\x80") $uchr = $str[$i++];
            else
            {
                $ulen = $str[$i] & "\xF0";
                $ulen = isset($ulen_mask[$ulen]) ? $ulen_mask[$ulen] : 1;
                $uchr = substr($str, $i, $ulen);

                if ($IGNORE && (1 === $ulen || !($valid || preg_match('/^.$/us', $uchr))))
                {
                    ++$i;
                    continue;
                }
                else $i += $ulen;
            }

            if (isset($map[$uchr]))
            {
                $result .= $map[$uchr];
            }
            else if ($TRANSLIT)
            {
                if (isset(self::$translit_map[$uchr]))
                {
                    $uchr = self::$translit_map[$uchr];
                }
                else if ($uchr >= "\xC3\x80")
                {
                    $uchr = \Normalizer::normalize($uchr, \Normalizer::NFD);

                    if ($uchr[0] < "\x80") $uchr = $uchr[0];
                    else if ($IGNORE) continue;
                    else return false;
                }

                $str = $uchr . substr($str, $i);
                $len = strlen($str);
                $i = 0;
            }
            else if (!$IGNORE)
            {
                return false;
            }
        }

        return true;
    }

    protected static function qp_byte_callback($m)
    {
        return '=' . strtoupper(dechex(ord($m[0])));
    }

    protected static function preg_offset($offset)
    {
        $rx = array();
        $offset = (int) $offset;

        while ($offset > 65535)
        {
            $rx[] = '.{65535}';
            $offset -= 65535;
        }

        return implode('', $rx) . '.{' . $offset . '}';
    }

    protected static function getData($file)
    {
        $file = __DIR__ . '/charset/' . $file . '.ser';
        if (file_exists($file)) return unserialize(file_get_contents($file));
        else return false;
    }
}
