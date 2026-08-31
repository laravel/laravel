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
 * - Ge'ez Frontier Foundation    locales@geez.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
    'months' => ['ጃንዩወሪ', 'ፌብሩወሪ', 'ማርች', 'ኤፕረል', 'ሜይ', 'ጁን', 'ጁላይ', 'ኦገስት', 'ሴፕቴምበር', 'ኦክተውበር', 'ኖቬምበር', 'ዲሴምበር'],
    'months_short' => ['ጃንዩ', 'ፌብሩ', 'ማርች', 'ኤፕረ', 'ሜይ ', 'ጁን ', 'ጁላይ', 'ኦገስ', 'ሴፕቴ', 'ኦክተ', 'ኖቬም', 'ዲሴም'],
    'weekdays' => ['እኁድ', 'ሰኑይ', 'ሠሉስ', 'ራብዕ', 'ሐሙስ', 'ዓርበ', 'ቀዳሚት'],
    'weekdays_short' => ['እኁድ', 'ሰኑይ', 'ሠሉስ', 'ራብዕ', 'ሐሙስ', 'ዓርበ', 'ቀዳሚ'],
    'weekdays_min' => ['እኁድ', 'ሰኑይ', 'ሠሉስ', 'ራብዕ', 'ሐሙስ', 'ዓርበ', 'ቀዳሚ'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['ጽባሕ', 'ምሴት'],
]);
