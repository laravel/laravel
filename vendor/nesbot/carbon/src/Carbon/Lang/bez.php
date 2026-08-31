<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/en.php', [
    'meridiem' => ['pamilau', 'pamunyi'],
    'weekdays' => ['pa mulungu', 'pa shahuviluha', 'pa hivili', 'pa hidatu', 'pa hitayi', 'pa hihanu', 'pa shahulembela'],
    'weekdays_short' => ['Mul', 'Vil', 'Hiv', 'Hid', 'Hit', 'Hih', 'Lem'],
    'weekdays_min' => ['Mul', 'Vil', 'Hiv', 'Hid', 'Hit', 'Hih', 'Lem'],
    'months' => ['pa mwedzi gwa hutala', 'pa mwedzi gwa wuvili', 'pa mwedzi gwa wudatu', 'pa mwedzi gwa wutai', 'pa mwedzi gwa wuhanu', 'pa mwedzi gwa sita', 'pa mwedzi gwa saba', 'pa mwedzi gwa nane', 'pa mwedzi gwa tisa', 'pa mwedzi gwa kumi', 'pa mwedzi gwa kumi na moja', 'pa mwedzi gwa kumi na mbili'],
    'months_short' => ['Hut', 'Vil', 'Dat', 'Tai', 'Han', 'Sit', 'Sab', 'Nan', 'Tis', 'Kum', 'Kmj', 'Kmb'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
]);
