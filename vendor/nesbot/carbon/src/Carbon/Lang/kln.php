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
    'meridiem' => ['krn', 'koosk'],
    'weekdays' => ['Kotisap', 'Kotaai', 'Koaeng’', 'Kosomok', 'Koang’wan', 'Komuut', 'Kolo'],
    'weekdays_short' => ['Kts', 'Kot', 'Koo', 'Kos', 'Koa', 'Kom', 'Kol'],
    'weekdays_min' => ['Kts', 'Kot', 'Koo', 'Kos', 'Koa', 'Kom', 'Kol'],
    'months' => ['Mulgul', 'Ng’atyaato', 'Kiptaamo', 'Iwootkuut', 'Mamuut', 'Paagi', 'Ng’eiyeet', 'Rooptui', 'Bureet', 'Epeeso', 'Kipsuunde ne taai', 'Kipsuunde nebo aeng’'],
    'months_short' => ['Mul', 'Ngat', 'Taa', 'Iwo', 'Mam', 'Paa', 'Nge', 'Roo', 'Bur', 'Epe', 'Kpt', 'Kpa'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],

    'year' => ':count maghatiat', // less reliable
    'y' => ':count maghatiat', // less reliable
    'a_year' => ':count maghatiat', // less reliable
]);
