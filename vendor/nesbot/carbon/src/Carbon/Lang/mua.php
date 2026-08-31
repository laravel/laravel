<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/en.php', [
    'meridiem' => ['comme', 'lilli'],
    'weekdays' => ['Com’yakke', 'Comlaaɗii', 'Comzyiiɗii', 'Comkolle', 'Comkaldǝɓlii', 'Comgaisuu', 'Comzyeɓsuu'],
    'weekdays_short' => ['Cya', 'Cla', 'Czi', 'Cko', 'Cka', 'Cga', 'Cze'],
    'weekdays_min' => ['Cya', 'Cla', 'Czi', 'Cko', 'Cka', 'Cga', 'Cze'],
    'months' => ['Fĩi Loo', 'Cokcwaklaŋne', 'Cokcwaklii', 'Fĩi Marfoo', 'Madǝǝuutǝbijaŋ', 'Mamǝŋgwãafahbii', 'Mamǝŋgwãalii', 'Madǝmbii', 'Fĩi Dǝɓlii', 'Fĩi Mundaŋ', 'Fĩi Gwahlle', 'Fĩi Yuru'],
    'months_short' => ['FLO', 'CLA', 'CKI', 'FMF', 'MAD', 'MBI', 'MLI', 'MAM', 'FDE', 'FMU', 'FGW', 'FYU'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D/M/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],
]);
