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
 * - Rwanda Steve Murphy murf@e-tools.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD.MM.YYYY',
    ],
    'months' => ['Mutarama', 'Gashyantare', 'Werurwe', 'Mata', 'Gicuransi', 'Kamena', 'Nyakanga', 'Kanama', 'Nzeli', 'Ukwakira', 'Ugushyingo', 'Ukuboza'],
    'months_short' => ['Mut', 'Gas', 'Wer', 'Mat', 'Gic', 'Kam', 'Nya', 'Kan', 'Nze', 'Ukw', 'Ugu', 'Uku'],
    'weekdays' => ['Ku cyumweru', 'Kuwa mbere', 'Kuwa kabiri', 'Kuwa gatatu', 'Kuwa kane', 'Kuwa gatanu', 'Kuwa gatandatu'],
    'weekdays_short' => ['Mwe', 'Mbe', 'Kab', 'Gtu', 'Kan', 'Gnu', 'Gnd'],
    'weekdays_min' => ['Mwe', 'Mbe', 'Kab', 'Gtu', 'Kan', 'Gnu', 'Gnd'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,

    'second' => ':count vuna', // less reliable
    's' => ':count vuna', // less reliable
    'a_second' => ':count vuna', // less reliable

    'year' => 'aka :count',
    'y' => 'aka :count',
    'a_year' => 'aka :count',

    'month' => 'ezi :count',
    'm' => 'ezi :count',
    'a_month' => 'ezi :count',

    'week' => ':count icyumweru',
    'w' => ':count icyumweru',
    'a_week' => ':count icyumweru',

    'day' => ':count nsi',
    'd' => ':count nsi',
    'a_day' => ':count nsi',

    'hour' => 'saha :count',
    'h' => 'saha :count',
    'a_hour' => 'saha :count',

    'minute' => ':count -nzinya',
    'min' => ':count -nzinya',
    'a_minute' => ':count -nzinya',
]);
