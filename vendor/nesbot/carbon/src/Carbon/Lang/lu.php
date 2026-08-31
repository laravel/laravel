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
    'meridiem' => ['Dinda', 'Dilolo'],
    'weekdays' => ['Lumingu', 'Nkodya', 'Ndàayà', 'Ndangù', 'Njòwa', 'Ngòvya', 'Lubingu'],
    'weekdays_short' => ['Lum', 'Nko', 'Ndy', 'Ndg', 'Njw', 'Ngv', 'Lub'],
    'weekdays_min' => ['Lum', 'Nko', 'Ndy', 'Ndg', 'Njw', 'Ngv', 'Lub'],
    'months' => ['Ciongo', 'Lùishi', 'Lusòlo', 'Mùuyà', 'Lumùngùlù', 'Lufuimi', 'Kabàlàshìpù', 'Lùshìkà', 'Lutongolo', 'Lungùdi', 'Kaswèkèsè', 'Ciswà'],
    'months_short' => ['Cio', 'Lui', 'Lus', 'Muu', 'Lum', 'Luf', 'Kab', 'Lush', 'Lut', 'Lun', 'Kas', 'Cis'],
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
