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
 * - The Debian project Christian Perrier bubulle@debian.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'D-MM-YY',
    ],
    'months' => ['जनवरी', 'फ़रवरी', 'मार्च', 'अप्रेल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितम्बर', 'अक्टूबर', 'नवम्बर', 'दिसम्बर'],
    'months_short' => ['जनवरी', 'फ़रवरी', 'मार्च', 'अप्रेल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितम्बर', 'अक्टूबर', 'नवम्बर', 'दिसम्बर'],
    'weekdays' => ['रविवासर:', 'सोमवासर:', 'मंगलवासर:', 'बुधवासर:', 'बृहस्पतिवासरः', 'शुक्रवासर', 'शनिवासर:'],
    'weekdays_short' => ['रविः', 'सोम:', 'मंगल:', 'बुध:', 'बृहस्पतिः', 'शुक्र', 'शनि:'],
    'weekdays_min' => ['रविः', 'सोम:', 'मंगल:', 'बुध:', 'बृहस्पतिः', 'शुक्र', 'शनि:'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['पूर्वाह्न', 'अपराह्न'],

    'minute' => ':count होरा', // less reliable
    'min' => ':count होरा', // less reliable
    'a_minute' => ':count होरा', // less reliable

    'year' => ':count वर्ष',
    'y' => ':count वर्ष',
    'a_year' => ':count वर्ष',

    'month' => ':count मास',
    'm' => ':count मास',
    'a_month' => ':count मास',

    'week' => ':count सप्ताहः saptahaĥ',
    'w' => ':count सप्ताहः saptahaĥ',
    'a_week' => ':count सप्ताहः saptahaĥ',

    'day' => ':count दिन',
    'd' => ':count दिन',
    'a_day' => ':count दिन',

    'hour' => ':count घण्टा',
    'h' => ':count घण्टा',
    'a_hour' => ':count घण्टा',

    'second' => ':count द्वितीयः',
    's' => ':count द्वितीयः',
    'a_second' => ':count द्वितीयः',
]);
