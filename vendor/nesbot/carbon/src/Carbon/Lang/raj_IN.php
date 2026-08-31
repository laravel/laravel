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
 * - meghrajsuthar03@gmail.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'D/M/YY',
    ],
    'months' => ['जनवरी', 'फरवरी', 'मार्च', 'अप्रैल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितंबर', 'अक्टूबर', 'नवंबर', 'दिसंबर'],
    'months_short' => ['जन', 'फर', 'मार्च', 'अप्रै', 'मई', 'जून', 'जुल', 'अग', 'सित', 'अक्टू', 'नव', 'दिस'],
    'weekdays' => ['रविवार', 'सोमवार', 'मंगल्लवार', 'बुधवार', 'बृहस्पतिवार', 'शुक्रवार', 'शनिवार'],
    'weekdays_short' => ['रवि', 'सोम', 'मंगल', 'बुध', 'बृहस्पति', 'शुक्र', 'शनि'],
    'weekdays_min' => ['रवि', 'सोम', 'मंगल', 'बुध', 'बृहस्पति', 'शुक्र', 'शनि'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['पूर्वाह्न', 'अपराह्न'],

    'year' => ':count आंहू', // less reliable
    'y' => ':count आंहू', // less reliable
    'a_year' => ':count आंहू', // less reliable

    'month' => ':count सूरज', // less reliable
    'm' => ':count सूरज', // less reliable
    'a_month' => ':count सूरज', // less reliable

    'week' => ':count निवाज', // less reliable
    'w' => ':count निवाज', // less reliable
    'a_week' => ':count निवाज', // less reliable

    'day' => ':count अेक', // less reliable
    'd' => ':count अेक', // less reliable
    'a_day' => ':count अेक', // less reliable

    'hour' => ':count दुनियांण', // less reliable
    'h' => ':count दुनियांण', // less reliable
    'a_hour' => ':count दुनियांण', // less reliable
]);
