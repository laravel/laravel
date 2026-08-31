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
 * - Red Hat, Pune    bug-glibc-locales@gnu.org
 */
return array_replace_recursive(require __DIR__.'/sd.php', [
    'formats' => [
        'L' => 'D/M/YY',
    ],
    'months' => ['जनवरी', 'फबरवरी', 'मार्चि', 'अप्रेल', 'मे', 'जूनि', 'जूलाइ', 'आगस्टु', 'सेप्टेंबरू', 'आक्टूबरू', 'नवंबरू', 'ॾिसंबरू'],
    'months_short' => ['जनवरी', 'फबरवरी', 'मार्चि', 'अप्रेल', 'मे', 'जूनि', 'जूलाइ', 'आगस्टु', 'सेप्टेंबरू', 'आक्टूबरू', 'नवंबरू', 'ॾिसंबरू'],
    'weekdays' => ['आर्तवारू', 'सूमरू', 'मंगलू', 'ॿुधरू', 'विस्पति', 'जुमो', 'छंछस'],
    'weekdays_short' => ['आर्तवारू', 'सूमरू', 'मंगलू', 'ॿुधरू', 'विस्पति', 'जुमो', 'छंछस'],
    'weekdays_min' => ['आर्तवारू', 'सूमरू', 'मंगलू', 'ॿुधरू', 'विस्पति', 'जुमो', 'छंछस'],
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['म.पू.', 'म.नं.'],
]);
