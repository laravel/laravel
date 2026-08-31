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
 * - François B
 * - Zhan Tong Zhang
 * - Mayank Badola
 * - JD Isaacks
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'from_now' => 'in :time',
    'formats' => [
        'LT' => 'h:mm A',
        'LTS' => 'h:mm:ss A',
        'L' => 'YYYY-MM-DD',
        'LL' => 'MMMM D, YYYY',
        'LLL' => 'MMMM D, YYYY h:mm A',
        'LLLL' => 'dddd, MMMM D, YYYY h:mm A',
    ],
    'first_day_of_week' => 0,
]);
