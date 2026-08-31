<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Php85 as p;

if (\PHP_VERSION_ID >= 80500) {
    return;
}

if (!function_exists('get_error_handler')) {
    function get_error_handler(): ?callable { return p\Php85::get_error_handler(); }
}

if (!function_exists('get_exception_handler')) {
    function get_exception_handler(): ?callable { return p\Php85::get_exception_handler(); }
}

if (!function_exists('array_first')) {
    function array_first(array $array) { return p\Php85::array_first($array); }
}

if (!function_exists('array_last')) {
    function array_last(array $array) { return p\Php85::array_last($array); }
}

if (extension_loaded('intl') && !function_exists('locale_is_right_to_left')) {
    function locale_is_right_to_left(string $locale): bool { return p\Php85::locale_is_right_to_left($locale); }
}

if (\PHP_VERSION_ID >= 80000) {
    require __DIR__.'/bootstrap80.php';

    return;
}

if (extension_loaded('intl') && !function_exists('grapheme_levenshtein')) {
    function grapheme_levenshtein(string $string1, string $string2, int $insertion_cost = 1, int $replacement_cost = 1, int $deletion_cost = 1, string $locale = '') { return p\Php85::grapheme_levenshtein($string1, $string2, $insertion_cost, $replacement_cost, $deletion_cost); }
}
