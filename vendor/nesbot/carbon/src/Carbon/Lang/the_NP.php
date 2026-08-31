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
 * - Chitwanix OS Development    info@chitwanix.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'dddd DD MMM YYYY',
    ],
    'months' => ['जनवरी', 'फ़रवरी', 'मार्च', 'अप्रेल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितम्बर', 'अक्टूबर', 'नवम्बर', 'दिसम्बर'],
    'months_short' => ['जनवरी', 'फ़रवरी', 'मार्च', 'अप्रेल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितम्बर', 'अक्टूबर', 'नवम्बर', 'दिसम्बर'],
    'weekdays' => ['आइतबार', 'सोमबार', 'मंगलबार', 'बुधबार', 'बिहिबार', 'शुक्रबार', 'शनिबार'],
    'weekdays_short' => ['आइत', 'सोम', 'मंगल', 'बुध', 'बिहि', 'शुक्र', 'शनि'],
    'weekdays_min' => ['आइत', 'सोम', 'मंगल', 'बुध', 'बिहि', 'शुक्र', 'शनि'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['पूर्वाह्न', 'अपराह्न'],
]);
