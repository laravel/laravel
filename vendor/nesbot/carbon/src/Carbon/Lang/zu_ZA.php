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
 * - Zuza Software Foundation (Translate.org.za) Dwayne Bailey dwayne@translate.org.za
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
    'months' => ['Januwari', 'Februwari', 'Mashi', 'Ephreli', 'Meyi', 'Juni', 'Julayi', 'Agasti', 'Septhemba', 'Okthoba', 'Novemba', 'Disemba'],
    'months_short' => ['Jan', 'Feb', 'Mas', 'Eph', 'Mey', 'Jun', 'Jul', 'Aga', 'Sep', 'Okt', 'Nov', 'Dis'],
    'weekdays' => ['iSonto', 'uMsombuluko', 'uLwesibili', 'uLwesithathu', 'uLwesine', 'uLwesihlanu', 'uMgqibelo'],
    'weekdays_short' => ['Son', 'Mso', 'Bil', 'Tha', 'Sin', 'Hla', 'Mgq'],
    'weekdays_min' => ['Son', 'Mso', 'Bil', 'Tha', 'Sin', 'Hla', 'Mgq'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,

    'year' => 'kweminyaka engu-:count',
    'y' => 'kweminyaka engu-:count',
    'a_year' => 'kweminyaka engu-:count',

    'month' => 'izinyanga ezingu-:count',
    'm' => 'izinyanga ezingu-:count',
    'a_month' => 'izinyanga ezingu-:count',

    'week' => 'lwamasonto angu-:count',
    'w' => 'lwamasonto angu-:count',
    'a_week' => 'lwamasonto angu-:count',

    'day' => 'ezingaba ngu-:count',
    'd' => 'ezingaba ngu-:count',
    'a_day' => 'ezingaba ngu-:count',

    'hour' => 'amahora angu-:count',
    'h' => 'amahora angu-:count',
    'a_hour' => 'amahora angu-:count',

    'minute' => 'ngemizuzu engu-:count',
    'min' => 'ngemizuzu engu-:count',
    'a_minute' => 'ngemizuzu engu-:count',

    'second' => 'imizuzwana engu-:count',
    's' => 'imizuzwana engu-:count',
    'a_second' => 'imizuzwana engu-:count',
]);
