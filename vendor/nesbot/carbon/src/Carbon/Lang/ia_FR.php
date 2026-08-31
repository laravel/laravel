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
 * - Fedora Project Nik Kalach nikka@fedoraproject.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD.MM.YYYY',
    ],
    'months' => ['januario', 'februario', 'martio', 'april', 'maio', 'junio', 'julio', 'augusto', 'septembre', 'octobre', 'novembre', 'decembre'],
    'months_short' => ['jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'],
    'weekdays' => ['dominica', 'lunedi', 'martedi', 'mercuridi', 'jovedi', 'venerdi', 'sabbato'],
    'weekdays_short' => ['dom', 'lun', 'mar', 'mer', 'jov', 'ven', 'sab'],
    'weekdays_min' => ['dom', 'lun', 'mar', 'mer', 'jov', 'ven', 'sab'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,

    'year' => 'anno :count',
    'y' => 'anno :count',
    'a_year' => 'anno :count',

    'month' => ':count mense',
    'm' => ':count mense',
    'a_month' => ':count mense',

    'week' => ':count septimana',
    'w' => ':count septimana',
    'a_week' => ':count septimana',

    'day' => ':count die',
    'd' => ':count die',
    'a_day' => ':count die',

    'hour' => ':count hora',
    'h' => ':count hora',
    'a_hour' => ':count hora',

    'minute' => ':count minuscule',
    'min' => ':count minuscule',
    'a_minute' => ':count minuscule',

    'second' => ':count secunda',
    's' => ':count secunda',
    'a_second' => ':count secunda',
]);
