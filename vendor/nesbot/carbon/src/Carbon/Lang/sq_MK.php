<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/sq.php', [
    'formats' => [
        'L' => 'D.M.YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY, HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY, HH:mm',
    ],
]);
