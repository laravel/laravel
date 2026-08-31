<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Intl\Normalizer as p;

if (\PHP_VERSION_ID >= 80000) {
    return require __DIR__.'/bootstrap80.php';
}

if (!function_exists('normalizer_is_normalized')) {
    function normalizer_is_normalized($string, $form = p\Normalizer::FORM_C) { return p\Normalizer::isNormalized($string, $form); }
}
if (!function_exists('normalizer_normalize')) {
    function normalizer_normalize($string, $form = p\Normalizer::FORM_C) { return p\Normalizer::normalize($string, $form); }
}
if (!function_exists('normalizer_get_raw_decomposition')) {
    function normalizer_get_raw_decomposition(?string $string, ?int $form = p\Normalizer::FORM_C) { return p\Normalizer::getRawDecomposition((string) $string, (int) $form); }
}
