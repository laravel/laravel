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
 * - Rene Torres Rene Torres, Pablo Saratxaga rgtorre@rocketmail.com, pablo@mandrakesoft.com
 * - Jaycee Mariano (alohajaycee)
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'MM/DD/YY',
    ],
    'months' => ['Enero', 'Pebrero', 'Marso', 'Abril', 'Mayo', 'Hunyo', 'Hulyo', 'Agosto', 'Setyembre', 'Oktubre', 'Nobyembre', 'Disyembre'],
    'months_short' => ['Ene', 'Peb', 'Mar', 'Abr', 'May', 'Hun', 'Hul', 'Ago', 'Set', 'Okt', 'Nob', 'Dis'],
    'weekdays' => ['Linggo', 'Lunes', 'Martes', 'Miyerkoles', 'Huwebes', 'Biyernes', 'Sabado'],
    'weekdays_short' => ['Lin', 'Lun', 'Mar', 'Miy', 'Huw', 'Biy', 'Sab'],
    'weekdays_min' => ['Lin', 'Lun', 'Mar', 'Miy', 'Huw', 'Biy', 'Sab'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['N.U.', 'N.H.'],

    'before' => ':time bago',
    'after' => ':time pagkatapos',

    'year' => ':count taon',
    'y' => ':count taon',
    'a_year' => ':count taon',

    'month' => ':count buwan',
    'm' => ':count buwan',
    'a_month' => ':count buwan',

    'week' => ':count linggo',
    'w' => ':count linggo',
    'a_week' => ':count linggo',

    'day' => ':count araw',
    'd' => ':count araw',
    'a_day' => ':count araw',

    'hour' => ':count oras',
    'h' => ':count oras',
    'a_hour' => ':count oras',

    'minute' => ':count minuto',
    'min' => ':count minuto',
    'a_minute' => ':count minuto',

    'second' => ':count segundo',
    's' => ':count segundo',
    'a_second' => ':count segundo',

    'ago' => ':time ang nakalipas',
    'from_now' => 'sa :time',
]);
