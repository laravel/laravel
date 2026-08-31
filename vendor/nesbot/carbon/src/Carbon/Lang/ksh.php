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
    'meridiem' => ['v.M.', 'n.M.'],
    'weekdays' => ['Sunndaach', 'Mohndaach', 'Dinnsdaach', 'Metwoch', 'Dunnersdaach', 'Friidaach', 'Samsdaach'],
    'weekdays_short' => ['Su.', 'Mo.', 'Di.', 'Me.', 'Du.', 'Fr.', 'Sa.'],
    'weekdays_min' => ['Su', 'Mo', 'Di', 'Me', 'Du', 'Fr', 'Sa'],
    'months' => ['Jannewa', 'Fäbrowa', 'Määz', 'Aprell', 'Mai', 'Juuni', 'Juuli', 'Oujoß', 'Septämber', 'Oktohber', 'Novämber', 'Dezämber'],
    'months_short' => ['Jan', 'Fäb', 'Mäz', 'Apr', 'Mai', 'Jun', 'Jul', 'Ouj', 'Säp', 'Okt', 'Nov', 'Dez'],
    'months_short_standalone' => ['Jan.', 'Fäb.', 'Mäz.', 'Apr.', 'Mai', 'Jun.', 'Jul.', 'Ouj.', 'Säp.', 'Okt.', 'Nov.', 'Dez.'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D. M. YYYY',
        'LL' => 'D. MMM. YYYY',
        'LLL' => 'D. MMMM YYYY HH:mm',
        'LLLL' => 'dddd, [dä] D. MMMM YYYY HH:mm',
    ],

    'year' => ':count Johr',
    'y' => ':count Johr',
    'a_year' => ':count Johr',

    'month' => ':count Moohnd',
    'm' => ':count Moohnd',
    'a_month' => ':count Moohnd',

    'week' => ':count woch',
    'w' => ':count woch',
    'a_week' => ':count woch',

    'day' => ':count Daach',
    'd' => ':count Daach',
    'a_day' => ':count Daach',

    'hour' => ':count Uhr',
    'h' => ':count Uhr',
    'a_hour' => ':count Uhr',

    'minute' => ':count Menutt',
    'min' => ':count Menutt',
    'a_minute' => ':count Menutt',

    'second' => ':count Sekůndt',
    's' => ':count Sekůndt',
    'a_second' => ':count Sekůndt',
]);
