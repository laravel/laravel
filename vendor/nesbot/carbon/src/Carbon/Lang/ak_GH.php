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
 * - Sugar Labs // OLPC sugarlabs.org libc-alpha@sourceware.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'YYYY/MM/DD',
    ],
    'months' => ['Sanda-Ɔpɛpɔn', 'Kwakwar-Ɔgyefuo', 'Ebɔw-Ɔbenem', 'Ebɔbira-Oforisuo', 'Esusow Aketseaba-Kɔtɔnimba', 'Obirade-Ayɛwohomumu', 'Ayɛwoho-Kitawonsa', 'Difuu-Ɔsandaa', 'Fankwa-Ɛbɔ', 'Ɔbɛsɛ-Ahinime', 'Ɔberɛfɛw-Obubuo', 'Mumu-Ɔpɛnimba'],
    'months_short' => ['S-Ɔ', 'K-Ɔ', 'E-Ɔ', 'E-O', 'E-K', 'O-A', 'A-K', 'D-Ɔ', 'F-Ɛ', 'Ɔ-A', 'Ɔ-O', 'M-Ɔ'],
    'weekdays' => ['Kwesida', 'Dwowda', 'Benada', 'Wukuda', 'Yawda', 'Fida', 'Memeneda'],
    'weekdays_short' => ['Kwe', 'Dwo', 'Ben', 'Wuk', 'Yaw', 'Fia', 'Mem'],
    'weekdays_min' => ['Kwe', 'Dwo', 'Ben', 'Wuk', 'Yaw', 'Fia', 'Mem'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['AN', 'EW'],

    'year' => ':count afe',
    'y' => ':count afe',
    'a_year' => ':count afe',

    'month' => ':count bosume',
    'm' => ':count bosume',
    'a_month' => ':count bosume',

    'day' => ':count ɛda',
    'd' => ':count ɛda',
    'a_day' => ':count ɛda',
]);
