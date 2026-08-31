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
 * - Jor Teron    bug-glibc-locales@gnu.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'D/M/YY',
    ],
    'months' => ['Arkoi', 'Thangthang', 'There', 'Jangmi', 'Aru', 'Vosik', 'Jakhong', 'Paipai', 'Chiti', 'Phere', 'Phaikuni', 'Matijong'],
    'months_short' => ['Ark', 'Thang', 'The', 'Jang', 'Aru', 'Vos', 'Jak', 'Pai', 'Chi', 'Phe', 'Phai', 'Mati'],
    'weekdays' => ['Bhomkuru', 'Urmi', 'Durmi', 'Thelang', 'Theman', 'Bhomta', 'Bhomti'],
    'weekdays_short' => ['Bhom', 'Ur', 'Dur', 'Tkel', 'Tkem', 'Bhta', 'Bhti'],
    'weekdays_min' => ['Bhom', 'Ur', 'Dur', 'Tkel', 'Tkem', 'Bhta', 'Bhti'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
]);
