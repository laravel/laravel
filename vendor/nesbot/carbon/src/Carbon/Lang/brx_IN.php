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
 * - Red Hat Pune    bug-glibc-locales@gnu.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'M/D/YY',
    ],
    'months' => ['जानुवारी', 'फेब्रुवारी', 'मार्स', 'एफ्रिल', 'मे', 'जुन', 'जुलाइ', 'आगस्थ', 'सेबथेज्ब़र', 'अखथबर', 'नबेज्ब़र', 'दिसेज्ब़र'],
    'months_short' => ['जानुवारी', 'फेब्रुवारी', 'मार्स', 'एप्रिल', 'मे', 'जुन', 'जुलाइ', 'आगस्थ', 'सेबथेज्ब़र', 'अखथबर', 'नबेज्ब़र', 'दिसेज्ब़र'],
    'weekdays' => ['रबिबार', 'सोबार', 'मंगलबार', 'बुदबार', 'बिसथिबार', 'सुखुरबार', 'सुनिबार'],
    'weekdays_short' => ['रबि', 'सम', 'मंगल', 'बुद', 'बिसथि', 'सुखुर', 'सुनि'],
    'weekdays_min' => ['रबि', 'सम', 'मंगल', 'बुद', 'बिसथि', 'सुखुर', 'सुनि'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['फुं.', 'बेलासे.'],
]);
