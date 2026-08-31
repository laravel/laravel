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
 * - bhashaghar@googlegroups.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'D/M/YY',
    ],
    'months' => ['जनवरी', 'फरवरी', 'मार्च', 'अप्रैल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितम्बर', 'अक्टूबर', 'नवम्बर', 'दिसम्बर"'],
    'months_short' => ['जनवरी', 'फरवरी', 'मार्च', 'अप्रैल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितम्बर', 'अक्टूबर', 'नवम्बर', 'दिसम्बर"'],
    'weekdays' => ['रविवार', 'सोमवार', 'मंगलवार', 'बुधवार', 'गुरुवार', 'शुक्रवार', 'शनिवार'],
    'weekdays_short' => ['रवि', 'सोम', 'मंगल', 'बुध', 'गुरु', 'शुक्र', 'शनि'],
    'weekdays_min' => ['रवि', 'सोम', 'मंगल', 'बुध', 'गुरु', 'शुक्र', 'शनि'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['पूर्वाह्न', 'अपराह्न'],

    'hour' => ':count मौसम',
    'h' => ':count मौसम',
    'a_hour' => ':count मौसम',

    'minute' => ':count कला',
    'min' => ':count कला',
    'a_minute' => ':count कला',

    'second' => ':count सोमार',
    's' => ':count सोमार',
    'a_second' => ':count सोमार',

    'year' => ':count साल',
    'y' => ':count साल',
    'a_year' => ':count साल',

    'month' => ':count महिना',
    'm' => ':count महिना',
    'a_month' => ':count महिना',

    'week' => ':count सप्ताह',
    'w' => ':count सप्ताह',
    'a_week' => ':count सप्ताह',

    'day' => ':count दिन',
    'd' => ':count दिन',
    'a_day' => ':count दिन',
]);
