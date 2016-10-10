<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2013 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

use Patchwork\PHP\Shim as s;

const MB_OVERLOAD_MAIL = 1;
const MB_OVERLOAD_STRING = 2;
const MB_OVERLOAD_REGEX = 4;
const MB_CASE_UPPER = 0;
const MB_CASE_LOWER = 1;
const MB_CASE_TITLE = 2;

function mb_convert_encoding($s, $to, $from = INF) {return s\Mbstring::mb_convert_encoding($s, $to, $from);};
function mb_decode_mimeheader($s) {return s\Mbstring::mb_decode_mimeheader($s);};
function mb_encode_mimeheader($s, $charset = INF, $transfer_enc = INF, $lf = INF, $indent = INF) {return s\Mbstring::mb_encode_mimeheader($s, $charset, $transfer_enc, $lf, $indent);};
function mb_convert_case($s, $mode, $enc = INF) {return s\Mbstring::mb_convert_case($s, $mode, $enc);};
function mb_internal_encoding($enc = INF) {return s\Mbstring::mb_internal_encoding($enc);};
function mb_language($lang = INF) {return s\Mbstring::mb_language($lang);}
function mb_list_encodings() {return s\Mbstring::mb_list_encodings();};
function mb_encoding_aliases($encoding) {return s\Mbstring::mb_encoding_aliases($encoding);}
function mb_check_encoding($var = INF, $encoding = INF) {return s\Mbstring::mb_check_encoding($var, $encoding);}
function mb_detect_encoding($str, $encoding_list = INF, $strict = false) {return s\Mbstring::mb_detect_encoding($str, $encoding_list, $strict);}
function mb_detect_order($encoding_list = INF) {return s\Mbstring::mb_detect_order($encoding_list);}
function mb_parse_str($s, &$result = array()) {return parse_str($s, $result);};
function mb_strlen($s, $enc = INF) {return s\Mbstring::mb_strlen($s, $enc);};
function mb_strpos($s, $needle, $offset = 0, $enc = INF) {return s\Mbstring::mb_strpos($s, $needle, $offset, $enc);};
function mb_strtolower($s, $enc = INF) {return s\Mbstring::mb_strtolower($s, $enc);};
function mb_strtoupper($s, $enc = INF) {return s\Mbstring::mb_strtoupper($s, $enc);};
function mb_substitute_character($char = INF) {return s\Mbstring::mb_substitute_character($char);};
function mb_substr_count($s, $needle) {return substr_count($s, $needle);};
function mb_substr($s, $start, $length = 2147483647, $enc = INF) {return s\Mbstring::mb_substr($s, $start, $length, $enc);};
function mb_stripos($s, $needle, $offset = 0, $enc = INF) {return s\Mbstring::mb_stripos($s, $needle, $offset, $enc);};
function mb_stristr($s, $needle, $part = false, $enc = INF) {return s\Mbstring::mb_stristr($s, $needle, $part, $enc);};
function mb_strrchr($s, $needle, $part = false, $enc = INF) {return s\Mbstring::mb_strrchr($s, $needle, $part, $enc);};
function mb_strrichr($s, $needle, $part = false, $enc = INF) {return s\Mbstring::mb_strrichr($s, $needle, $part, $enc);};
function mb_strripos($s, $needle, $offset = 0, $enc = INF) {return s\Mbstring::mb_strripos($s, $needle, $offset, $enc);};
function mb_strrpos($s, $needle, $offset = 0, $enc = INF) {return s\Mbstring::mb_strrpos($s, $needle, $offset, $enc);};
function mb_strstr($s, $needle, $part = false, $enc = INF) {return s\Mbstring::mb_strstr($s, $needle, $part, $enc);};
