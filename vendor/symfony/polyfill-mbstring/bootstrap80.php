<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Mbstring as p;

if (!function_exists('mb_convert_encoding')) {
    function mb_convert_encoding(array|string|null $string, ?string $to_encoding, array|string|null $from_encoding = null): array|string|false { return p\Mbstring::mb_convert_encoding($string ?? '', (string) $to_encoding, $from_encoding); }
}
if (!function_exists('mb_decode_mimeheader')) {
    function mb_decode_mimeheader(?string $string): string { return p\Mbstring::mb_decode_mimeheader((string) $string); }
}
if (!function_exists('mb_encode_mimeheader')) {
    function mb_encode_mimeheader(?string $string, ?string $charset = null, ?string $transfer_encoding = null, ?string $newline = "\r\n", ?int $indent = 0): string { return p\Mbstring::mb_encode_mimeheader((string) $string, $charset, $transfer_encoding, (string) $newline, (int) $indent); }
}
if (!function_exists('mb_decode_numericentity')) {
    function mb_decode_numericentity(?string $string, array $map, ?string $encoding = null): string { return p\Mbstring::mb_decode_numericentity((string) $string, $map, $encoding); }
}
if (!function_exists('mb_encode_numericentity')) {
    function mb_encode_numericentity(?string $string, array $map, ?string $encoding = null, ?bool $hex = false): string { return p\Mbstring::mb_encode_numericentity((string) $string, $map, $encoding, (bool) $hex); }
}
if (!function_exists('mb_convert_case')) {
    function mb_convert_case(?string $string, ?int $mode, ?string $encoding = null): string { return p\Mbstring::mb_convert_case((string) $string, (int) $mode, $encoding); }
}
if (!function_exists('mb_internal_encoding')) {
    function mb_internal_encoding(?string $encoding = null): string|bool { return p\Mbstring::mb_internal_encoding($encoding); }
}
if (!function_exists('mb_language')) {
    function mb_language(?string $language = null): string|bool { return p\Mbstring::mb_language($language); }
}
if (!function_exists('mb_list_encodings')) {
    function mb_list_encodings(): array { return p\Mbstring::mb_list_encodings(); }
}
if (!function_exists('mb_encoding_aliases')) {
    function mb_encoding_aliases(?string $encoding): array { return p\Mbstring::mb_encoding_aliases((string) $encoding); }
}
if (!function_exists('mb_check_encoding')) {
    function mb_check_encoding(array|string|null $value = null, ?string $encoding = null): bool { return p\Mbstring::mb_check_encoding($value, $encoding); }
}
if (!function_exists('mb_detect_encoding')) {
    function mb_detect_encoding(?string $string, array|string|null $encodings = null, ?bool $strict = false): string|false { return p\Mbstring::mb_detect_encoding((string) $string, $encodings, (bool) $strict); }
}
if (!function_exists('mb_detect_order')) {
    function mb_detect_order(array|string|null $encoding = null): array|bool { return p\Mbstring::mb_detect_order($encoding); }
}
if (!function_exists('mb_parse_str')) {
    function mb_parse_str(?string $string, &$result = []): bool { parse_str((string) $string, $result); return (bool) $result; }
}
if (!function_exists('mb_strlen')) {
    function mb_strlen(?string $string, ?string $encoding = null): int { return p\Mbstring::mb_strlen((string) $string, $encoding); }
}
if (!function_exists('mb_strpos')) {
    function mb_strpos(?string $haystack, ?string $needle, ?int $offset = 0, ?string $encoding = null): int|false { return p\Mbstring::mb_strpos((string) $haystack, (string) $needle, (int) $offset, $encoding); }
}
if (!function_exists('mb_strtolower')) {
    function mb_strtolower(?string $string, ?string $encoding = null): string { return p\Mbstring::mb_strtolower((string) $string, $encoding); }
}
if (!function_exists('mb_strtoupper')) {
    function mb_strtoupper(?string $string, ?string $encoding = null): string { return p\Mbstring::mb_strtoupper((string) $string, $encoding); }
}
if (!function_exists('mb_substitute_character')) {
    function mb_substitute_character(string|int|null $substitute_character = null): string|int|bool { return p\Mbstring::mb_substitute_character($substitute_character); }
}
if (!function_exists('mb_substr')) {
    function mb_substr(?string $string, ?int $start, ?int $length = null, ?string $encoding = null): string { return p\Mbstring::mb_substr((string) $string, (int) $start, $length, $encoding); }
}
if (!function_exists('mb_stripos')) {
    function mb_stripos(?string $haystack, ?string $needle, ?int $offset = 0, ?string $encoding = null): int|false { return p\Mbstring::mb_stripos((string) $haystack, (string) $needle, (int) $offset, $encoding); }
}
if (!function_exists('mb_stristr')) {
    function mb_stristr(?string $haystack, ?string $needle, ?bool $before_needle = false, ?string $encoding = null): string|false { return p\Mbstring::mb_stristr((string) $haystack, (string) $needle, (bool) $before_needle, $encoding); }
}
if (!function_exists('mb_strrchr')) {
    function mb_strrchr(?string $haystack, ?string $needle, ?bool $before_needle = false, ?string $encoding = null): string|false { return p\Mbstring::mb_strrchr((string) $haystack, (string) $needle, (bool) $before_needle, $encoding); }
}
if (!function_exists('mb_strrichr')) {
    function mb_strrichr(?string $haystack, ?string $needle, ?bool $before_needle = false, ?string $encoding = null): string|false { return p\Mbstring::mb_strrichr((string) $haystack, (string) $needle, (bool) $before_needle, $encoding); }
}
if (!function_exists('mb_strripos')) {
    function mb_strripos(?string $haystack, ?string $needle, ?int $offset = 0, ?string $encoding = null): int|false { return p\Mbstring::mb_strripos((string) $haystack, (string) $needle, (int) $offset, $encoding); }
}
if (!function_exists('mb_strrpos')) {
    function mb_strrpos(?string $haystack, ?string $needle, ?int $offset = 0, ?string $encoding = null): int|false { return p\Mbstring::mb_strrpos((string) $haystack, (string) $needle, (int) $offset, $encoding); }
}
if (!function_exists('mb_strstr')) {
    function mb_strstr(?string $haystack, ?string $needle, ?bool $before_needle = false, ?string $encoding = null): string|false { return p\Mbstring::mb_strstr((string) $haystack, (string) $needle, (bool) $before_needle, $encoding); }
}
if (!function_exists('mb_get_info')) {
    function mb_get_info(?string $type = 'all'): array|string|int|false|null { return p\Mbstring::mb_get_info((string) $type); }
}
if (!function_exists('mb_http_output')) {
    function mb_http_output(?string $encoding = null): string|bool { return p\Mbstring::mb_http_output($encoding); }
}
if (!function_exists('mb_strwidth')) {
    function mb_strwidth(?string $string, ?string $encoding = null): int { return p\Mbstring::mb_strwidth((string) $string, $encoding); }
}
if (!function_exists('mb_substr_count')) {
    function mb_substr_count(?string $haystack, ?string $needle, ?string $encoding = null): int { return p\Mbstring::mb_substr_count((string) $haystack, (string) $needle, $encoding); }
}
if (!function_exists('mb_output_handler')) {
    function mb_output_handler(?string $string, ?int $status): string { return p\Mbstring::mb_output_handler((string) $string, (int) $status); }
}
if (!function_exists('mb_http_input')) {
    function mb_http_input(?string $type = null): array|string|false { return p\Mbstring::mb_http_input($type); }
}

if (!function_exists('mb_convert_variables')) {
    function mb_convert_variables(?string $to_encoding, array|string|null $from_encoding, mixed &$var, mixed &...$vars): string|false { return p\Mbstring::mb_convert_variables((string) $to_encoding, $from_encoding ?? '', $var, ...$vars); }
}

if (!function_exists('mb_ord')) {
    function mb_ord(?string $string, ?string $encoding = null): int|false { return p\Mbstring::mb_ord((string) $string, $encoding); }
}
if (!function_exists('mb_chr')) {
    function mb_chr(?int $codepoint, ?string $encoding = null): string|false { return p\Mbstring::mb_chr((int) $codepoint, $encoding); }
}
if (!function_exists('mb_scrub')) {
    function mb_scrub(?string $string, ?string $encoding = null): string { return p\Mbstring::mb_scrub($string, $encoding); }
}
if (!function_exists('mb_str_split')) {
    function mb_str_split(?string $string, ?int $length = 1, ?string $encoding = null): array { return p\Mbstring::mb_str_split((string) $string, (int) $length, $encoding); }
}

if (!function_exists('mb_str_pad')) {
    function mb_str_pad(?string $string, ?int $length, ?string $pad_string = ' ', ?int $pad_type = STR_PAD_RIGHT, ?string $encoding = null): string { return p\Mbstring::mb_str_pad((string) $string, (int) $length, (string) $pad_string, (int) $pad_type, $encoding); }
}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst(?string $string, ?string $encoding = null): string { return p\Mbstring::mb_ucfirst((string) $string, $encoding); }
}

if (!function_exists('mb_lcfirst')) {
    function mb_lcfirst(?string $string, ?string $encoding = null): string { return p\Mbstring::mb_lcfirst((string) $string, $encoding); }
}

if (!function_exists('mb_trim')) {
    function mb_trim(?string $string, ?string $characters = null, ?string $encoding = null): string { return p\Mbstring::mb_trim((string) $string, $characters, $encoding); }
}

if (!function_exists('mb_ltrim')) {
    function mb_ltrim(?string $string, ?string $characters = null, ?string $encoding = null): string { return p\Mbstring::mb_ltrim((string) $string, $characters, $encoding); }
}

if (!function_exists('mb_rtrim')) {
    function mb_rtrim(?string $string, ?string $characters = null, ?string $encoding = null): string { return p\Mbstring::mb_rtrim((string) $string, $characters, $encoding); }
}

if (extension_loaded('mbstring')) {
    return;
}

if (!defined('MB_CASE_UPPER')) {
    define('MB_CASE_UPPER', 0);
}
if (!defined('MB_CASE_LOWER')) {
    define('MB_CASE_LOWER', 1);
}
if (!defined('MB_CASE_TITLE')) {
    define('MB_CASE_TITLE', 2);
}
