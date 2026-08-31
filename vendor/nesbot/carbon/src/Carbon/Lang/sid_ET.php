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
 * - Ge'ez Frontier Foundation    locales@geez.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
    'months' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    'months_short' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    'weekdays' => ['Sambata', 'Sanyo', 'Maakisanyo', 'Roowe', 'Hamuse', 'Arbe', 'Qidaame'],
    'weekdays_short' => ['Sam', 'San', 'Mak', 'Row', 'Ham', 'Arb', 'Qid'],
    'weekdays_min' => ['Sam', 'San', 'Mak', 'Row', 'Ham', 'Arb', 'Qid'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['soodo', 'hawwaro'],
]);
