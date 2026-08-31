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
    'months' => ['eyoMqungu', 'eyoMdumba', 'eyoKwindla', 'uTshazimpuzi', 'uCanzibe', 'eyeSilimela', 'eyeKhala', 'eyeThupa', 'eyoMsintsi', 'eyeDwarha', 'eyeNkanga', 'eyoMnga'],
    'months_short' => ['Mqu', 'Mdu', 'Kwi', 'Tsh', 'Can', 'Sil', 'Kha', 'Thu', 'Msi', 'Dwa', 'Nka', 'Mng'],
    'weekdays' => ['iCawa', 'uMvulo', 'lwesiBini', 'lwesiThathu', 'ulweSine', 'lwesiHlanu', 'uMgqibelo'],
    'weekdays_short' => ['Caw', 'Mvu', 'Bin', 'Tha', 'Sin', 'Hla', 'Mgq'],
    'weekdays_min' => ['Caw', 'Mvu', 'Bin', 'Tha', 'Sin', 'Hla', 'Mgq'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,

    'year' => ':count ihlobo', // less reliable
    'y' => ':count ihlobo', // less reliable
    'a_year' => ':count ihlobo', // less reliable

    'hour' => ':count iwotshi', // less reliable
    'h' => ':count iwotshi', // less reliable
    'a_hour' => ':count iwotshi', // less reliable

    'minute' => ':count ingqalelo', // less reliable
    'min' => ':count ingqalelo', // less reliable
    'a_minute' => ':count ingqalelo', // less reliable

    'second' => ':count nceda', // less reliable
    's' => ':count nceda', // less reliable
    'a_second' => ':count nceda', // less reliable

    'month' => ':count inyanga',
    'm' => ':count inyanga',
    'a_month' => ':count inyanga',

    'week' => ':count veki',
    'w' => ':count veki',
    'a_week' => ':count veki',

    'day' => ':count imini',
    'd' => ':count imini',
    'a_day' => ':count imini',
]);
