<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2016 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

namespace Patchwork\Utf8;

use Normalizer as n;
use Patchwork\Utf8 as u;
use Patchwork\PHP\Shim as s;


class Bootup
{
    static function initAll()
    {
        ini_set('default_charset', 'UTF-8');

        self::initUtf8Encode();
        self::initIconv();
        self::initMbstring();
        self::initExif();
        self::initIntl();
        self::initLocale();
    }

    static function initUtf8Encode()
    {
        function_exists('utf8_encode') or require __DIR__ . '/Bootup/utf8_encode.php';
    }

    static function initMbstring()
    {
        if (extension_loaded('mbstring'))
        {
            if ( ((int) ini_get('mbstring.encoding_translation') || in_array(strtolower(ini_get('mbstring.encoding_translation')), array('on', 'yes', 'true')))
                && !in_array(strtolower(ini_get('mbstring.http_input')), array('pass', '8bit', 'utf-8')) )
            {
                user_error('php.ini settings: Please disable mbstring.encoding_translation or set mbstring.http_input to "pass"',  E_USER_WARNING);
            }

            if (MB_OVERLOAD_STRING & (int) ini_get('mbstring.func_overload'))
            {
                user_error('php.ini settings: Please disable mbstring.func_overload', E_USER_WARNING);
            }

            if (function_exists('mb_regex_encoding')) mb_regex_encoding('UTF-8');
            ini_set('mbstring.script_encoding', 'pass');

            if ('utf-8' !== strtolower(mb_internal_encoding()))
            {
                mb_internal_encoding('UTF-8');
            }

            if ('none' !== strtolower(mb_substitute_character()))
            {
                mb_substitute_character('none');
            }

            if (!in_array(strtolower(mb_http_output()), array('pass', '8bit')))
            {
                mb_http_output('pass');
            }

            if (!in_array(strtolower(mb_language()), array('uni', 'neutral')))
            {
                mb_language('uni');
            }
        }
        else if (!function_exists('mb_strlen'))
        {
            extension_loaded('iconv') or static::initIconv();

            require __DIR__ . '/Bootup/mbstring.php';
        }
    }

    static function initIconv()
    {
        if (extension_loaded('iconv'))
        {
            if ('UTF-8' !== strtoupper(iconv_get_encoding('input_encoding')))
            {
                iconv_set_encoding('input_encoding', 'UTF-8');
            }

            if ('UTF-8' !== strtoupper(iconv_get_encoding('internal_encoding')))
            {
                iconv_set_encoding('internal_encoding', 'UTF-8');
            }

            if ('UTF-8' !== strtoupper(iconv_get_encoding('output_encoding')))
            {
                iconv_set_encoding('output_encoding', 'UTF-8');
            }
        }
        else if (!function_exists('iconv'))
        {
            require __DIR__ . '/Bootup/iconv.php';
        }
    }

    static function initExif()
    {
        if (extension_loaded('exif'))
        {
            if (ini_get('exif.encode_unicode') && 'UTF-8' !== strtoupper(ini_get('exif.encode_unicode')))
            {
                ini_set('exif.encode_unicode', 'UTF-8');
            }

            if (ini_get('exif.encode_jis') && 'UTF-8' !== strtoupper(ini_get('exif.encode_jis')))
            {
                ini_set('exif.encode_jis', 'UTF-8');
            }
        }
    }

    static function initIntl()
    {
        if (defined('GRAPHEME_CLUSTER_RX')) return;

        define('GRAPHEME_CLUSTER_RX', PCRE_VERSION >= '8.32' ? '\X' : s\Intl::GRAPHEME_CLUSTER_RX);

        if (!function_exists('grapheme_strlen'))
        {
            extension_loaded('iconv') or static::initIconv();
            extension_loaded('mbstring') or static::initMbstring();

            require __DIR__ . '/Bootup/intl.php';
        }
    }

    static function initLocale()
    {
        // With non-UTF-8 locale, basename() bugs.
        // Be aware that setlocale() can be slow.
        // You'd better properly configure your LANG environment variable to an UTF-8 locale.

        if ('' === basename('§'))
        {
            setlocale(LC_ALL, 'C.UTF-8', 'C');
            setlocale(LC_CTYPE, 'en_US.UTF-8', 'fr_FR.UTF-8', 'es_ES.UTF-8', 'de_DE.UTF-8', 'ru_RU.UTF-8', 'pt_BR.UTF-8', 'it_IT.UTF-8', 'ja_JP.UTF-8', 'zh_CN.UTF-8', '0');
        }
    }

    static function filterRequestUri($uri = null, $exit = true)
    {
        if (! isset($uri))
        {
            if (! isset($_SERVER['REQUEST_URI'])) return;
            else $uri = $_SERVER['REQUEST_URI'];
        }

        // Ensures the URL is well formed UTF-8
        // When not, assumes Windows-1252 and redirects to the corresponding UTF-8 encoded URL

        if (! preg_match('//u', urldecode($uri)))
        {
            $uri = preg_replace_callback(
                '/[\x80-\xFF]+/',
                function($m) {return urlencode($m[0]);},
                $uri
            );

            $uri = preg_replace_callback(
                '/(?:%[89A-F][0-9A-F])+/i',
                function($m) {return urlencode(u::utf8_encode(urldecode($m[0])));},
                $uri
            );

            if ($exit)
            {
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $uri);

                exit; // TODO: remove this in 1.2 (BC)
            }
        }

        return $uri;
    }

    static function filterRequestInputs($normalization_form = 4 /* n::NFC */, $leading_combining = '◌')
    {
        // Ensures inputs are well formed UTF-8
        // When not, assumes Windows-1252 and converts to UTF-8
        // Tests only values, not keys

        $a = array(&$_FILES, &$_ENV, &$_GET, &$_POST, &$_COOKIE, &$_SERVER, &$_REQUEST);

        foreach ($a[0] as &$r) $a[] = array(&$r['name'], &$r['type']);
        unset($a[0]);

        $len = count($a) + 1;
        for ($i = 1; $i < $len; ++$i)
        {
            foreach ($a[$i] as &$r)
            {
                $s = $r; // $r is a ref, $s a copy
                if (is_array($s)) $a[$len++] =& $r;
                else $r = static::filterString($s, $normalization_form, $leading_combining);
            }

            unset($a[$i]);
        }
    }

    static function filterString($s, $normalization_form = 4 /* n::NFC */, $leading_combining = '◌')
    {
        if (false !== strpos($s, "\r"))
        {
            // Workaround https://bugs.php.net/65732
            $s = str_replace("\r\n", "\n", $s);
            $s = strtr($s, "\r", "\n");
        }

        if (preg_match('/[\x80-\xFF]/', $s))
        {
            if (n::isNormalized($s, $normalization_form)) $n = '-';
            else
            {
                $n = n::normalize($s, $normalization_form);
                if (isset($n[0])) $s = $n;
                else $s = u::utf8_encode($s);
            }

            if ($s[0] >= "\x80" && isset($n[0], $leading_combining[0]) && preg_match('/^\p{Mn}/u', $s))
            {
                // Prevent leading combining chars
                // for NFC-safe concatenations.
                $s = $leading_combining . $s;
            }
        }

        return $s;
    }
}
