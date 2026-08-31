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
    'first_day_of_week' => 0,
    'meridiem' => ['Tesiran', 'Teipa'],
    'weekdays' => ['Mderot ee are', 'Mderot ee kuni', 'Mderot ee ong’wan', 'Mderot ee inet', 'Mderot ee ile', 'Mderot ee sapa', 'Mderot ee kwe'],
    'weekdays_short' => ['Are', 'Kun', 'Ong', 'Ine', 'Ile', 'Sap', 'Kwe'],
    'weekdays_min' => ['Are', 'Kun', 'Ong', 'Ine', 'Ile', 'Sap', 'Kwe'],
    'months' => ['Lapa le obo', 'Lapa le waare', 'Lapa le okuni', 'Lapa le ong’wan', 'Lapa le imet', 'Lapa le ile', 'Lapa le sapa', 'Lapa le isiet', 'Lapa le saal', 'Lapa le tomon', 'Lapa le tomon obo', 'Lapa le tomon waare'],
    'months_short' => ['Obo', 'Waa', 'Oku', 'Ong', 'Ime', 'Ile', 'Sap', 'Isi', 'Saa', 'Tom', 'Tob', 'Tow'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
]);
