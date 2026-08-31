<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/gsw.php', [
    'meridiem' => ['vorm.', 'nam.'],
    'months' => ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'Auguscht', 'Septämber', 'Oktoober', 'Novämber', 'Dezämber'],
    'first_day_of_week' => 1,
    'formats' => [
        'LLL' => 'Do MMMM YYYY HH:mm',
        'LLLL' => 'dddd, Do MMMM YYYY HH:mm',
    ],
]);
