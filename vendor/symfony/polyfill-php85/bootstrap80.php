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

if (extension_loaded('intl') && !function_exists('grapheme_levenshtein')) {
    function grapheme_levenshtein(string $string1, string $string2, int $insertion_cost = 1, int $replacement_cost = 1, int $deletion_cost = 1, string $locale = ''): int|false { return p\Php85::grapheme_levenshtein($string1, $string2, $insertion_cost, $replacement_cost, $deletion_cost); }
}
