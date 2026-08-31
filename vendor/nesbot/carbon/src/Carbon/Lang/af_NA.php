<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/af.php', [
    'meridiem' => ['v', 'n'],
    'weekdays' => ['Sondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrydag', 'Saterdag'],
    'weekdays_short' => ['So.', 'Ma.', 'Di.', 'Wo.', 'Do.', 'Vr.', 'Sa.'],
    'weekdays_min' => ['So.', 'Ma.', 'Di.', 'Wo.', 'Do.', 'Vr.', 'Sa.'],
    'months' => ['Januarie', 'Februarie', 'Maart', 'April', 'Mei', 'Junie', 'Julie', 'Augustus', 'September', 'Oktober', 'November', 'Desember'],
    'months_short' => ['Jan.', 'Feb.', 'Mrt.', 'Apr.', 'Mei', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Okt.', 'Nov.', 'Des.'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'YYYY-MM-DD',
        'LL' => 'DD MMM YYYY',
        'LLL' => 'DD MMMM YYYY HH:mm',
        'LLLL' => 'dddd, DD MMMM YYYY HH:mm',
    ],
]);
