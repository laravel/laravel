<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/tr.php', [
    'weekdays_short' => ['Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt'],
    'weekdays_min' => ['Pa', 'Pt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct'],
    'formats' => [
        'LT' => 'h:mm a',
        'LTS' => 'h:mm:ss a',
        'L' => 'D.MM.YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY h:mm a',
        'LLLL' => 'D MMMM YYYY dddd h:mm a',
    ],
]);
