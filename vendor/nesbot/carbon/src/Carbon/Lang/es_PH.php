<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/es.php', [
    'first_day_of_week' => 0,
    'formats' => [
        'LT' => 'h:mm a',
        'LTS' => 'h:mm:ss a',
        'L' => 'D/M/yy',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D [de] MMMM [de] YYYY h:mm a',
        'LLLL' => 'dddd, D [de] MMMM [de] YYYY h:mm a',
    ],
]);
