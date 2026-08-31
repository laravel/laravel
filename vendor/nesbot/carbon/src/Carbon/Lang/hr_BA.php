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
 * - DarkoDevelop
 */
return array_replace_recursive(require __DIR__.'/hr.php', [
    'weekdays' => ['nedjelja', 'ponedjeljak', 'utorak', 'srijeda', 'četvrtak', 'petak', 'subota'],
    'weekdays_short' => ['ned', 'pon', 'uto', 'sri', 'čet', 'pet', 'sub'],
    'weekdays_min' => ['ned', 'pon', 'uto', 'sri', 'čet', 'pet', 'sub'],
    'months' => ['siječnja', 'veljače', 'ožujka', 'travnja', 'svibnja', 'lipnja', 'srpnja', 'kolovoza', 'rujna', 'listopada', 'studenoga', 'prosinca'],
    'months_short' => ['sij', 'velj', 'ožu', 'tra', 'svi', 'lip', 'srp', 'kol', 'ruj', 'lis', 'stu', 'pro'],
    'months_standalone' => ['siječanj', 'veljača', 'ožujak', 'travanj', 'svibanj', 'lipanj', 'srpanj', 'kolovoz', 'rujan', 'listopad', 'studeni', 'prosinac'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D. M. yy.',
        'LL' => 'D. MMM YYYY.',
        'LLL' => 'D. MMMM YYYY. HH:mm',
        'LLLL' => 'dddd, D. MMMM YYYY. HH:mm',
    ],
]);
