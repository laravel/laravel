<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/uz.php', [
    'formats' => [
        'L' => 'DD/MM/yy',
        'LL' => 'D MMM, YYYY',
        'LLL' => 'D MMMM, YYYY HH:mm',
        'LLLL' => 'dddd, DD MMMM, YYYY HH:mm',
    ],
    'meridiem' => ['ТО', 'ТК'],
]);
