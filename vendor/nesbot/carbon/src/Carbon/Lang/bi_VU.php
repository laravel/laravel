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
 * - Samsung Electronics Co., Ltd.    akhilesh.k@samsung.com & maninder1.s@samsung.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'first_day_of_week' => 0,
    'formats' => [
        'L' => 'dddd DD MMM YYYY',
    ],
    'months' => ['jenuware', 'febwari', 'maj', 'epril', 'mei', 'jun', 'julae', 'ogis', 'septemba', 'oktoba', 'novemba', 'disemba'],
    'months_short' => ['jen', 'feb', 'maj', 'epr', 'mei', 'jun', 'jul', 'ogi', 'sep', 'okt', 'nov', 'dis'],
    'weekdays' => ['sande', 'mande', 'maj', 'wota', 'fraede', 'sarede'],
    'weekdays_short' => ['san', 'man', 'maj', 'wot', 'fra', 'sar'],
    'weekdays_min' => ['san', 'man', 'maj', 'wot', 'fra', 'sar'],

    'year' => ':count seven', // less reliable
    'y' => ':count seven', // less reliable
    'a_year' => ':count seven', // less reliable

    'month' => ':count mi', // less reliable
    'm' => ':count mi', // less reliable
    'a_month' => ':count mi', // less reliable

    'week' => ':count sarede', // less reliable
    'w' => ':count sarede', // less reliable
    'a_week' => ':count sarede', // less reliable

    'day' => ':count betde', // less reliable
    'd' => ':count betde', // less reliable
    'a_day' => ':count betde', // less reliable

    'hour' => ':count klok', // less reliable
    'h' => ':count klok', // less reliable
    'a_hour' => ':count klok', // less reliable

    'minute' => ':count smol', // less reliable
    'min' => ':count smol', // less reliable
    'a_minute' => ':count smol', // less reliable

    'second' => ':count tu', // less reliable
    's' => ':count tu', // less reliable
    'a_second' => ':count tu', // less reliable
]);
