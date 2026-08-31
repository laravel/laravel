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
    'months' => ['Pherekgong', 'Hlakola', 'Tlhakubele', 'Mmese', 'Motsheanong', 'Phupjane', 'Phupu', 'Phato', 'Leotse', 'Mphalane', 'Pudungwana', 'Tshitwe'],
    'months_short' => ['Phe', 'Hla', 'TlH', 'Mme', 'Mot', 'Jan', 'Upu', 'Pha', 'Leo', 'Mph', 'Pud', 'Tsh'],
    'weekdays' => ['Sontaha', 'Mantaha', 'Labobedi', 'Laboraro', 'Labone', 'Labohlano', 'Moqebelo'],
    'weekdays_short' => ['Son', 'Mma', 'Bed', 'Rar', 'Ne', 'Hla', 'Moq'],
    'weekdays_min' => ['Son', 'Mma', 'Bed', 'Rar', 'Ne', 'Hla', 'Moq'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,

    'week' => ':count Sontaha', // less reliable
    'w' => ':count Sontaha', // less reliable
    'a_week' => ':count Sontaha', // less reliable

    'day' => ':count letsatsi', // less reliable
    'd' => ':count letsatsi', // less reliable
    'a_day' => ':count letsatsi', // less reliable

    'hour' => ':count sešupanako', // less reliable
    'h' => ':count sešupanako', // less reliable
    'a_hour' => ':count sešupanako', // less reliable

    'minute' => ':count menyane', // less reliable
    'min' => ':count menyane', // less reliable
    'a_minute' => ':count menyane', // less reliable

    'second' => ':count thusa', // less reliable
    's' => ':count thusa', // less reliable
    'a_second' => ':count thusa', // less reliable

    'year' => ':count selemo',
    'y' => ':count selemo',
    'a_year' => ':count selemo',

    'month' => ':count kgwedi',
    'm' => ':count kgwedi',
    'a_month' => ':count kgwedi',
]);
