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
 * - Kunal Marwaha
 * - Josh Soref
 * - Jørn Ølmheim
 * - Craig Patik
 * - bustta
 * - François B
 * - Tim Fish
 * - Claire Coloma
 * - Steven Heinrich
 * - JD Isaacks
 * - Raphael Amorim
 */
return array_replace_recursive(require __DIR__.'/es.php', [
    'diff_before_yesterday' => 'anteayer',
    'formats' => [
        'LT' => 'h:mm A',
        'LTS' => 'h:mm:ss A',
        'L' => 'MM/DD/YYYY',
        'LL' => 'MMMM [de] D [de] YYYY',
        'LLL' => 'MMMM [de] D [de] YYYY h:mm A',
        'LLLL' => 'dddd, MMMM [de] D [de] YYYY h:mm A',
    ],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
]);
