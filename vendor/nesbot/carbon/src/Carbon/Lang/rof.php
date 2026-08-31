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
    'meridiem' => ['kang’ama', 'kingoto'],
    'weekdays' => ['Ijumapili', 'Ijumatatu', 'Ijumanne', 'Ijumatano', 'Alhamisi', 'Ijumaa', 'Ijumamosi'],
    'weekdays_short' => ['Ijp', 'Ijt', 'Ijn', 'Ijtn', 'Alh', 'Iju', 'Ijm'],
    'weekdays_min' => ['Ijp', 'Ijt', 'Ijn', 'Ijtn', 'Alh', 'Iju', 'Ijm'],
    'months' => ['Mweri wa kwanza', 'Mweri wa kaili', 'Mweri wa katatu', 'Mweri wa kaana', 'Mweri wa tanu', 'Mweri wa sita', 'Mweri wa saba', 'Mweri wa nane', 'Mweri wa tisa', 'Mweri wa ikumi', 'Mweri wa ikumi na moja', 'Mweri wa ikumi na mbili'],
    'months_short' => ['M1', 'M2', 'M3', 'M4', 'M5', 'M6', 'M7', 'M8', 'M9', 'M10', 'M11', 'M12'],
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
