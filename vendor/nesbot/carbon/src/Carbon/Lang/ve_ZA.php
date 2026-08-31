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
    'months' => ['Phando', 'Luhuhi', 'Ṱhafamuhwe', 'Lambamai', 'Shundunthule', 'Fulwi', 'Fulwana', 'Ṱhangule', 'Khubvumedzi', 'Tshimedzi', 'Ḽara', 'Nyendavhusiku'],
    'months_short' => ['Pha', 'Luh', 'Fam', 'Lam', 'Shu', 'Lwi', 'Lwa', 'Ngu', 'Khu', 'Tsh', 'Ḽar', 'Nye'],
    'weekdays' => ['Swondaha', 'Musumbuluwo', 'Ḽavhuvhili', 'Ḽavhuraru', 'Ḽavhuṋa', 'Ḽavhuṱanu', 'Mugivhela'],
    'weekdays_short' => ['Swo', 'Mus', 'Vhi', 'Rar', 'ṋa', 'Ṱan', 'Mug'],
    'weekdays_min' => ['Swo', 'Mus', 'Vhi', 'Rar', 'ṋa', 'Ṱan', 'Mug'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,

    // Too unreliable
    /*
    'day' => ':count vhege', // less reliable
    'd' => ':count vhege', // less reliable
    'a_day' => ':count vhege', // less reliable

    'hour' => ':count watshi', // less reliable
    'h' => ':count watshi', // less reliable
    'a_hour' => ':count watshi', // less reliable

    'minute' => ':count watshi', // less reliable
    'min' => ':count watshi', // less reliable
    'a_minute' => ':count watshi', // less reliable

    'second' => ':count Mu', // less reliable
    's' => ':count Mu', // less reliable
    'a_second' => ':count Mu', // less reliable

    'week' => ':count vhege',
    'w' => ':count vhege',
    'a_week' => ':count vhege',
    */
]);
