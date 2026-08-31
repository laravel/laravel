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
    'months' => ['Ferikgong', 'Tlhakole', 'Mopitlwe', 'Moranang', 'Motsheganong', 'Seetebosigo', 'Phukwi', 'Phatwe', 'Lwetse', 'Diphalane', 'Ngwanatsele', 'Sedimonthole'],
    'months_short' => ['Fer', 'Tlh', 'Mop', 'Mor', 'Mot', 'See', 'Phu', 'Pha', 'Lwe', 'Dip', 'Ngw', 'Sed'],
    'weekdays' => ['laTshipi', 'Mosupologo', 'Labobedi', 'Laboraro', 'Labone', 'Labotlhano', 'Lamatlhatso'],
    'weekdays_short' => ['Tsh', 'Mos', 'Bed', 'Rar', 'Ne', 'Tlh', 'Mat'],
    'weekdays_min' => ['Tsh', 'Mos', 'Bed', 'Rar', 'Ne', 'Tlh', 'Mat'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,

    'year' => 'dingwaga di le :count',
    'y' => 'dingwaga di le :count',
    'a_year' => 'dingwaga di le :count',

    'month' => 'dikgwedi di le :count',
    'm' => 'dikgwedi di le :count',
    'a_month' => 'dikgwedi di le :count',

    'week' => 'dibeke di le :count',
    'w' => 'dibeke di le :count',
    'a_week' => 'dibeke di le :count',

    'day' => 'malatsi :count',
    'd' => 'malatsi :count',
    'a_day' => 'malatsi :count',

    'hour' => 'diura di le :count',
    'h' => 'diura di le :count',
    'a_hour' => 'diura di le :count',

    'minute' => 'metsotso e le :count',
    'min' => 'metsotso e le :count',
    'a_minute' => 'metsotso e le :count',

    'second' => 'metsotswana e le :count',
    's' => 'metsotswana e le :count',
    'a_second' => 'metsotswana e le :count',
]);
