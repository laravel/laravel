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
 * - ks-gnome-trans-commits@lists.code.indlinux.net
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'M/D/YY',
    ],
    'months' => ['जनवरी', 'फ़रवरी', 'मार्च', 'अप्रेल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितम्बर', 'अक्टूबर', 'नवम्बर', 'दिसम्बर'],
    'months_short' => ['जनवरी', 'फ़रवरी', 'मार्च', 'अप्रेल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितम्बर', 'अक्टूबर', 'नवम्बर', 'दिसम्बर'],
    'weekdays' => ['आथवार', 'चॅ़दुरवार', 'बोमवार', 'ब्वदवार', 'ब्रसवार', 'शोकुरवार', 'बटुवार'],
    'weekdays_short' => ['आथ ', 'चॅ़दुर', 'बोम', 'ब्वद', 'ब्रस', 'शोकुर', 'बटु'],
    'weekdays_min' => ['आथ ', 'चॅ़दुर', 'बोम', 'ब्वद', 'ब्रस', 'शोकुर', 'बटु'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['पूर्वाह्न', 'अपराह्न'],
]);
