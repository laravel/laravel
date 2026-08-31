<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/en.php', [
    'meridiem' => ['Z.MU.', 'Z.MW.'],
    'weekdays' => ['Ku w’indwi', 'Ku wa mbere', 'Ku wa kabiri', 'Ku wa gatatu', 'Ku wa kane', 'Ku wa gatanu', 'Ku wa gatandatu'],
    'weekdays_short' => ['cu.', 'mbe.', 'kab.', 'gtu.', 'kan.', 'gnu.', 'gnd.'],
    'weekdays_min' => ['cu.', 'mbe.', 'kab.', 'gtu.', 'kan.', 'gnu.', 'gnd.'],
    'months' => ['Nzero', 'Ruhuhuma', 'Ntwarante', 'Ndamukiza', 'Rusama', 'Ruheshi', 'Mukakaro', 'Nyandagaro', 'Nyakanga', 'Gitugutu', 'Munyonyo', 'Kigarama'],
    'months_short' => ['Mut.', 'Gas.', 'Wer.', 'Mat.', 'Gic.', 'Kam.', 'Nya.', 'Kan.', 'Nze.', 'Ukw.', 'Ugu.', 'Uku.'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D/M/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],

    'year' => 'imyaka :count',
    'y' => 'imyaka :count',
    'a_year' => 'imyaka :count',

    'month' => 'amezi :count',
    'm' => 'amezi :count',
    'a_month' => 'amezi :count',

    'week' => 'indwi :count',
    'w' => 'indwi :count',
    'a_week' => 'indwi :count',

    'day' => 'imisi :count',
    'd' => 'imisi :count',
    'a_day' => 'imisi :count',

    'hour' => 'amasaha :count',
    'h' => 'amasaha :count',
    'a_hour' => 'amasaha :count',

    'minute' => 'iminuta :count',
    'min' => 'iminuta :count',
    'a_minute' => 'iminuta :count',

    'second' => 'inguvu :count', // less reliable
    's' => 'inguvu :count', // less reliable
    'a_second' => 'inguvu :count', // less reliable
]);
