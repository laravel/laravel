<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Ctype as p;

if (\PHP_VERSION_ID >= 80000) {
    return require __DIR__.'/bootstrap80.php';
}

if (!function_exists('ctype_alnum')) {
    function ctype_alnum($text) { return p\Ctype::ctype_alnum($text); }
}
if (!function_exists('ctype_alpha')) {
    function ctype_alpha($text) { return p\Ctype::ctype_alpha($text); }
}
if (!function_exists('ctype_cntrl')) {
    function ctype_cntrl($text) { return p\Ctype::ctype_cntrl($text); }
}
if (!function_exists('ctype_digit')) {
    function ctype_digit($text) { return p\Ctype::ctype_digit($text); }
}
if (!function_exists('ctype_graph')) {
    function ctype_graph($text) { return p\Ctype::ctype_graph($text); }
}
if (!function_exists('ctype_lower')) {
    function ctype_lower($text) { return p\Ctype::ctype_lower($text); }
}
if (!function_exists('ctype_print')) {
    function ctype_print($text) { return p\Ctype::ctype_print($text); }
}
if (!function_exists('ctype_punct')) {
    function ctype_punct($text) { return p\Ctype::ctype_punct($text); }
}
if (!function_exists('ctype_space')) {
    function ctype_space($text) { return p\Ctype::ctype_space($text); }
}
if (!function_exists('ctype_upper')) {
    function ctype_upper($text) { return p\Ctype::ctype_upper($text); }
}
if (!function_exists('ctype_xdigit')) {
    function ctype_xdigit($text) { return p\Ctype::ctype_xdigit($text); }
}
