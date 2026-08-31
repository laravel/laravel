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
    'first_day_of_week' => 0,
    'meridiem' => ['a', 'p'],
    'weekdays' => ['Svondo', 'Muvhuro', 'Chipiri', 'Chitatu', 'China', 'Chishanu', 'Mugovera'],
    'weekdays_short' => ['Svo', 'Muv', 'Chp', 'Cht', 'Chn', 'Chs', 'Mug'],
    'weekdays_min' => ['Sv', 'Mu', 'Cp', 'Ct', 'Cn', 'Cs', 'Mg'],
    'months' => ['Ndira', 'Kukadzi', 'Kurume', 'Kubvumbi', 'Chivabvu', 'Chikumi', 'Chikunguru', 'Nyamavhuvhu', 'Gunyana', 'Gumiguru', 'Mbudzi', 'Zvita'],
    'months_short' => ['Ndi', 'Kuk', 'Kur', 'Kub', 'Chv', 'Chk', 'Chg', 'Nya', 'Gun', 'Gum', 'Mbu', 'Zvi'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'YYYY-MM-dd',
        'LL' => 'YYYY MMM D',
        'LLL' => 'YYYY MMMM D HH:mm',
        'LLLL' => 'YYYY MMMM D, dddd HH:mm',
    ],

    'year' => 'makore :count',
    'y' => 'makore :count',
    'a_year' => 'makore :count',

    'month' => 'mwedzi :count',
    'm' => 'mwedzi :count',
    'a_month' => 'mwedzi :count',

    'week' => 'vhiki :count',
    'w' => 'vhiki :count',
    'a_week' => 'vhiki :count',

    'day' => 'mazuva :count',
    'd' => 'mazuva :count',
    'a_day' => 'mazuva :count',

    'hour' => 'maawa :count',
    'h' => 'maawa :count',
    'a_hour' => 'maawa :count',

    'minute' => 'minitsi :count',
    'min' => 'minitsi :count',
    'a_minute' => 'minitsi :count',

    'second' => 'sekonzi :count',
    's' => 'sekonzi :count',
    'a_second' => 'sekonzi :count',
]);
