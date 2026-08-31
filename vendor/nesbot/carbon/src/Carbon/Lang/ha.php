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
 * - pablo@mandriva.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D/M/YYYY',
        'LL' => 'D MMM, YYYY',
        'LLL' => 'D MMMM, YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM, YYYY HH:mm',
    ],
    'months' => ['Janairu', 'Faburairu', 'Maris', 'Afirilu', 'Mayu', 'Yuni', 'Yuli', 'Agusta', 'Satumba', 'Oktoba', 'Nuwamba', 'Disamba'],
    'months_short' => ['Jan', 'Fab', 'Mar', 'Afi', 'May', 'Yun', 'Yul', 'Agu', 'Sat', 'Okt', 'Nuw', 'Dis'],
    'weekdays' => ['Lahadi', 'Litini', 'Talata', 'Laraba', 'Alhamis', 'Jumaʼa', 'Asabar'],
    'weekdays_short' => ['Lah', 'Lit', 'Tal', 'Lar', 'Alh', 'Jum', 'Asa'],
    'weekdays_min' => ['Lh', 'Li', 'Ta', 'Lr', 'Al', 'Ju', 'As'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,

    'year' => 'shekara :count',
    'y' => 'shekara :count',
    'a_year' => 'shekara :count',

    'month' => ':count wátàa',
    'm' => ':count wátàa',
    'a_month' => ':count wátàa',

    'week' => ':count mako',
    'w' => ':count mako',
    'a_week' => ':count mako',

    'day' => ':count rana',
    'd' => ':count rana',
    'a_day' => ':count rana',

    'hour' => ':count áwàa',
    'h' => ':count áwàa',
    'a_hour' => ':count áwàa',

    'minute' => 'minti :count',
    'min' => 'minti :count',
    'a_minute' => 'minti :count',

    'second' => ':count ná bíyú',
    's' => ':count ná bíyú',
    'a_second' => ':count ná bíyú',
]);
