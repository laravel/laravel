<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Intl\Grapheme as p;

if (!function_exists('grapheme_str_split')) {
    function grapheme_str_split(string $string, int $length = 1): array|false { return p\Grapheme::grapheme_str_split($string, $length); }
}
if (!function_exists('grapheme_levenshtein')) {
    function grapheme_levenshtein(string $string1, string $string2, int $insertion_cost = 1, int $replacement_cost = 1, int $deletion_cost = 1, string $locale = ''): int|false { return p\Grapheme::grapheme_levenshtein($string1, $string2, $insertion_cost, $replacement_cost, $deletion_cost); }
}
if (!function_exists('grapheme_strrev')) {
    function grapheme_strrev(string $string): string|false { return p\Grapheme::grapheme_strrev($string); }
}

if (extension_loaded('intl')) {
    return;
}

if (!defined('GRAPHEME_EXTR_COUNT')) {
    define('GRAPHEME_EXTR_COUNT', 0);
}
if (!defined('GRAPHEME_EXTR_MAXBYTES')) {
    define('GRAPHEME_EXTR_MAXBYTES', 1);
}
if (!defined('GRAPHEME_EXTR_MAXCHARS')) {
    define('GRAPHEME_EXTR_MAXCHARS', 2);
}

if (!function_exists('grapheme_extract')) {
    function grapheme_extract(?string $haystack, ?int $size, ?int $type = GRAPHEME_EXTR_COUNT, ?int $offset = 0, &$next = null): string|false { return p\Grapheme::grapheme_extract((string) $haystack, (int) $size, (int) $type, (int) $offset, $next); }
}
if (\PHP_VERSION_ID >= 80500) {
    return require __DIR__.'/bootstrap85.php';
}

if (!function_exists('grapheme_stripos')) {
    function grapheme_stripos(?string $haystack, ?string $needle, ?int $offset = 0): int|false { return p\Grapheme::grapheme_stripos((string) $haystack, (string) $needle, (int) $offset); }
}
if (!function_exists('grapheme_stristr')) {
    function grapheme_stristr(?string $haystack, ?string $needle, ?bool $beforeNeedle = false): string|false { return p\Grapheme::grapheme_stristr((string) $haystack, (string) $needle, (bool) $beforeNeedle); }
}
if (!function_exists('grapheme_strlen')) {
    function grapheme_strlen(?string $string): int|false|null { return p\Grapheme::grapheme_strlen((string) $string); }
}
if (!function_exists('grapheme_strpos')) {
    function grapheme_strpos(?string $haystack, ?string $needle, ?int $offset = 0): int|false { return p\Grapheme::grapheme_strpos((string) $haystack, (string) $needle, (int) $offset); }
}
if (!function_exists('grapheme_strripos')) {
    function grapheme_strripos(?string $haystack, ?string $needle, ?int $offset = 0): int|false { return p\Grapheme::grapheme_strripos((string) $haystack, (string) $needle, (int) $offset); }
}
if (!function_exists('grapheme_strrpos')) {
    function grapheme_strrpos(?string $haystack, ?string $needle, ?int $offset = 0): int|false { return p\Grapheme::grapheme_strrpos((string) $haystack, (string) $needle, (int) $offset); }
}
if (!function_exists('grapheme_strstr')) {
    function grapheme_strstr(?string $haystack, ?string $needle, ?bool $beforeNeedle = false): string|false { return p\Grapheme::grapheme_strstr((string) $haystack, (string) $needle, (bool) $beforeNeedle); }
}
if (!function_exists('grapheme_substr')) {
    function grapheme_substr(?string $string, ?int $offset, ?int $length = null): string|false { return p\Grapheme::grapheme_substr((string) $string, (int) $offset, $length); }
}
