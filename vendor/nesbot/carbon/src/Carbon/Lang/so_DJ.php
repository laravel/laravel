<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Authors:
 * - Ge'ez Frontier Foundation    locales@geez.org
 */
return array_replace_recursive(require __DIR__.'/so.php', [
    'formats' => [
        'L' => 'DD.MM.YYYY',
    ],
]);
