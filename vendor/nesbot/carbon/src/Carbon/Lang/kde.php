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
    'meridiem' => ['Muhi', 'Chilo'],
    'weekdays' => ['Liduva lyapili', 'Liduva lyatatu', 'Liduva lyanchechi', 'Liduva lyannyano', 'Liduva lyannyano na linji', 'Liduva lyannyano na mavili', 'Liduva litandi'],
    'weekdays_short' => ['Ll2', 'Ll3', 'Ll4', 'Ll5', 'Ll6', 'Ll7', 'Ll1'],
    'weekdays_min' => ['Ll2', 'Ll3', 'Ll4', 'Ll5', 'Ll6', 'Ll7', 'Ll1'],
    'months' => ['Mwedi Ntandi', 'Mwedi wa Pili', 'Mwedi wa Tatu', 'Mwedi wa Nchechi', 'Mwedi wa Nnyano', 'Mwedi wa Nnyano na Umo', 'Mwedi wa Nnyano na Mivili', 'Mwedi wa Nnyano na Mitatu', 'Mwedi wa Nnyano na Nchechi', 'Mwedi wa Nnyano na Nnyano', 'Mwedi wa Nnyano na Nnyano na U', 'Mwedi wa Nnyano na Nnyano na M'],
    'months_short' => ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ago', 'Sep', 'Okt', 'Nov', 'Des'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
]);
