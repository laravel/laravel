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

const GRAPHEME_EXTR_COUNT = 0;
const GRAPHEME_EXTR_MAXBYTES = 1;
const GRAPHEME_EXTR_MAXCHARS = 2;

function normalizer_is_normalized($s, $form = s\Normalizer::NFC) {return s\Normalizer::isNormalized($s, $form);};
function normalizer_normalize($s, $form = s\Normalizer::NFC) {return s\Normalizer::normalize($s, $form);};

function grapheme_extract($s, $size, $type = 0, $start = 0, &$next = 0) {return s\Intl::grapheme_extract($s, $size, $type, $start, $next);};
function grapheme_stripos($s, $needle, $offset = 0) {return s\Intl::grapheme_stripos($s, $needle, $offset);};
function grapheme_stristr($s, $needle, $before_needle = false) {return s\Intl::grapheme_stristr($s, $needle, $before_needle);};
function grapheme_strlen($s) {return s\Intl::grapheme_strlen($s);};
function grapheme_strpos($s, $needle, $offset = 0) {return s\Intl::grapheme_strpos($s, $needle, $offset);};
function grapheme_strripos($s, $needle, $offset = 0) {return s\Intl::grapheme_strripos($s, $needle, $offset);};
function grapheme_strrpos($s, $needle, $offset = 0) {return s\Intl::grapheme_strrpos($s, $needle, $offset);};
function grapheme_strstr($s, $needle, $before_needle = false) {return s\Intl::grapheme_strstr($s, $needle, $before_needle);};
function grapheme_substr($s, $start, $len = 2147483647) {return s\Intl::grapheme_substr($s, $start, $len);};
