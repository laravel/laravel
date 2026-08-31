<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/ms.php', [
    'formats' => [
        'LT' => 'h:mm a',
        'LTS' => 'h:mm:ss a',
        'L' => 'D/MM/yy',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY, h:mm a',
        'LLLL' => 'dd MMMM YYYY, h:mm a',
    ],
    'meridiem' => ['a', 'p'],
]);
