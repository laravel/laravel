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
 * Partial intl implementation in pure PHP.
 *
 * Implemented:
 * - grapheme_extract  - Extract a sequence of grapheme clusters from a text buffer, which must be encoded in UTF-8
 * - grapheme_stripos  - Find position (in grapheme units) of first occurrence of a case-insensitive string
 * - grapheme_stristr  - Returns part of haystack string from the first occurrence of case-insensitive needle to the end of haystack
 * - grapheme_strlen   - Get string length in grapheme units
 * - grapheme_strpos   - Find position (in grapheme units) of first occurrence of a string
 * - grapheme_strripos - Find position (in grapheme units) of last occurrence of a case-insensitive string
 * - grapheme_strrpos  - Find position (in grapheme units) of last occurrence of a string
 * - grapheme_strstr   - Returns part of haystack string from the first occurrence of needle to the end of haystack
 * - grapheme_substr   - Return part of a string
 */
class Intl
{
    // (CRLF|([ZWNJ-ZWJ]|T+|L*(LV?V+|LV|LVT)T*|L+|[^Control])[Extend]*|[Control])
    // This regular expression is a work around for http://bugs.exim.org/1279
    const GRAPHEME_CLUSTER_RX = '(?:\r\n|(?:[ -~\x{200C}\x{200D}]|[ᆨ-ᇹ]+|[ᄀ-ᅟ]*(?:[가개갸걔거게겨계고과괘괴교구궈궤귀규그긔기까깨꺄꺠꺼께껴꼐꼬꽈꽤꾀꾜꾸꿔꿰뀌뀨끄끠끼나내냐냬너네녀녜노놔놰뇌뇨누눠눼뉘뉴느늬니다대댜댸더데뎌뎨도돠돼되됴두둬뒈뒤듀드듸디따때땨떄떠떼뗘뗴또똬뙈뙤뚀뚜뚸뛔뛰뜌뜨띄띠라래랴럐러레려례로롸뢔뢰료루뤄뤠뤼류르릐리마매먀먜머메며몌모뫄뫠뫼묘무뭐뭬뮈뮤므믜미바배뱌뱨버베벼볘보봐봬뵈뵤부붜붸뷔뷰브븨비빠빼뺘뺴뻐뻬뼈뼤뽀뽜뽸뾔뾰뿌뿨쀄쀠쀼쁘쁴삐사새샤섀서세셔셰소솨쇄쇠쇼수숴쉐쉬슈스싀시싸쌔쌰썌써쎄쎠쎼쏘쏴쐐쐬쑈쑤쒀쒜쒸쓔쓰씌씨아애야얘어에여예오와왜외요우워웨위유으의이자재쟈쟤저제져졔조좌좨죄죠주줘줴쥐쥬즈즤지짜째쨔쨰쩌쩨쪄쪠쪼쫘쫴쬐쬬쭈쭤쮀쮜쮸쯔쯰찌차채챠챼처체쳐쳬초촤쵀최쵸추춰췌취츄츠츼치카캐캬컈커케켜켸코콰쾌쾨쿄쿠쿼퀘퀴큐크킈키타태탸턔터테텨톄토톼퇘퇴툐투퉈퉤튀튜트틔티파패퍄퍠퍼페펴폐포퐈퐤푀표푸풔풰퓌퓨프픠피하해햐햬허헤혀혜호화홰회효후훠훼휘휴흐희히]?[ᅠ-ᆢ]+|[가-힣])[ᆨ-ᇹ]*|[ᄀ-ᅟ]+|[^\p{Cc}\p{Cf}\p{Zl}\p{Zp}])[\p{Mn}\p{Me}\x{09BE}\x{09D7}\x{0B3E}\x{0B57}\x{0BBE}\x{0BD7}\x{0CC2}\x{0CD5}\x{0CD6}\x{0D3E}\x{0D57}\x{0DCF}\x{0DDF}\x{200C}\x{200D}\x{1D165}\x{1D16E}-\x{1D172}]*|[\p{Cc}\p{Cf}\p{Zl}\p{Zp}])';

    static function grapheme_extract($s, $size, $type = GRAPHEME_EXTR_COUNT, $start = 0, &$next = 0)
    {
        if (!is_scalar($s)) {
            $hasError = false;
            set_error_handler(function () use (&$hasError) {$hasError = true;});
            $next = substr($s, $start);
            restore_error_handler();
            if ($hasError) {
                substr($s, $start);
                $s = '';
            } else {
                $s = $next;
            }
        } else {
            $s = substr($s, $start);
        }
        $size  = (int) $size;
        $type  = (int) $type;
        $start = (int) $start;

        if (!isset($s[0]) || 0 > $size || 0 > $start || 0 > $type || 2 < $type) return false;
        if (0 === $size) return '';

        $next = $start;

        $s = preg_split('/(' . GRAPHEME_CLUSTER_RX . ')/u', "\r\n" .  $s, $size + 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        if (!isset($s[1])) return false;

        $i = 1;
        $ret = '';

        do
        {
            if (GRAPHEME_EXTR_COUNT === $type) --$size;
            else if (GRAPHEME_EXTR_MAXBYTES === $type) $size -= strlen($s[$i]);
            else $size -= iconv_strlen($s[$i], 'UTF-8//IGNORE');

            if ($size >= 0) $ret .= $s[$i];
        }
        while (isset($s[++$i]) && $size > 0);

        $next += strlen($ret);

        return $ret;
    }

    static function grapheme_strlen($s)
    {
        preg_replace('/' . GRAPHEME_CLUSTER_RX . '/u', '', $s, -1, $len);
        return 0 === $len && '' !== $s ? null : $len;
    }

    static function grapheme_substr($s, $start, $len = 2147483647)
    {
        preg_match_all('/' . GRAPHEME_CLUSTER_RX . '/u', $s, $s);

        $slen = count($s[0]);
        $start = (int) $start;

        if (0 > $start) $start += $slen;
        if (0 > $start) return false;
        if ($start >= $slen) return false;

        $rem = $slen - $start;

        if (0 > $len) $len += $rem;
        if (0 === $len) return '';
        if (0 > $len) return false;
        if ($len > $rem) $len = $rem;

        return implode('', array_slice($s[0], $start, $len));
    }

    static function grapheme_substr_workaround62759($s, $start, $len)
    {
        // Intl based http://bugs.php.net/62759 and 55562 workaround

        if (2147483647 == $len) return grapheme_substr($s, $start);

        $s .= '';
        $slen = grapheme_strlen($s);
        $start = (int) $start;

        if (0 > $start) $start += $slen;
        if (0 > $start) return false;
        if ($start >= $slen) return false;

        $rem = $slen - $start;

        if (0 > $len) $len += $rem;
        if (0 === $len) return '';
        if (0 > $len) return false;
        if ($len > $rem) $len = $rem;

        return grapheme_substr($s, $start, $len);
    }

    static function grapheme_strpos  ($s, $needle, $offset = 0) {return self::grapheme_position($s, $needle, $offset, 0);}
    static function grapheme_stripos ($s, $needle, $offset = 0) {return self::grapheme_position($s, $needle, $offset, 1);}
    static function grapheme_strrpos ($s, $needle, $offset = 0) {return self::grapheme_position($s, $needle, $offset, 2);}
    static function grapheme_strripos($s, $needle, $offset = 0) {return self::grapheme_position($s, $needle, $offset, 3);}
    static function grapheme_stristr ($s, $needle, $before_needle = false) {return mb_stristr($s, $needle, $before_needle, 'UTF-8');}
    static function grapheme_strstr  ($s, $needle, $before_needle = false) {return mb_strstr ($s, $needle, $before_needle, 'UTF-8');}


    protected static function grapheme_position($s, $needle, $offset, $mode)
    {
        if (! preg_match('/./us', $needle .= '')) return false;
        if (! preg_match('/./us', $s .= '')) return false;
        if ($offset > 0) $s = self::grapheme_substr($s, $offset);
        else if ($offset < 0) $offset = 0;

        switch ($mode)
        {
        case 0: $needle = iconv_strpos ($s, $needle, 0, 'UTF-8'); break;
        case 1: $needle = mb_stripos   ($s, $needle, 0, 'UTF-8'); break;
        case 2: $needle = iconv_strrpos($s, $needle,    'UTF-8'); break;
        default: $needle = mb_strripos ($s, $needle, 0, 'UTF-8'); break;
        }

        return $needle ? self::grapheme_strlen(iconv_substr($s, 0, $needle, 'UTF-8')) + $offset : $needle;
    }
}
