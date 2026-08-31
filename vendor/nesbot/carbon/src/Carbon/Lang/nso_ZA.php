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
    'months' => ['Janaware', 'Febereware', 'Matšhe', 'Aprele', 'Mei', 'June', 'Julae', 'Agostose', 'Setemere', 'Oktobere', 'Nofemere', 'Disemere'],
    'months_short' => ['Jan', 'Feb', 'Mat', 'Apr', 'Mei', 'Jun', 'Jul', 'Ago', 'Set', 'Okt', 'Nof', 'Dis'],
    'weekdays' => ['LaMorena', 'Mošupologo', 'Labobedi', 'Laboraro', 'Labone', 'Labohlano', 'Mokibelo'],
    'weekdays_short' => ['Son', 'Moš', 'Bed', 'Rar', 'Ne', 'Hla', 'Mok'],
    'weekdays_min' => ['Son', 'Moš', 'Bed', 'Rar', 'Ne', 'Hla', 'Mok'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,

    'year' => ':count ngwaga',
    'y' => ':count ngwaga',
    'a_year' => ':count ngwaga',

    'month' => ':count Kgwedi',
    'm' => ':count Kgwedi',
    'a_month' => ':count Kgwedi',

    'week' => ':count Beke',
    'w' => ':count Beke',
    'a_week' => ':count Beke',

    'day' => ':count Letšatši',
    'd' => ':count Letšatši',
    'a_day' => ':count Letšatši',

    'hour' => ':count Iri',
    'h' => ':count Iri',
    'a_hour' => ':count Iri',

    'minute' => ':count Motsotso',
    'min' => ':count Motsotso',
    'a_minute' => ':count Motsotso',

    'second' => ':count motsotswana',
    's' => ':count motsotswana',
    'a_second' => ':count motsotswana',
]);
