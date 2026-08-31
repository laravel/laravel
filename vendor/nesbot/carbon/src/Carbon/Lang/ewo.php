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
    'meridiem' => ['kíkíríg', 'ngəgógəle'],
    'weekdays' => ['sɔ́ndɔ', 'mɔ́ndi', 'sɔ́ndɔ məlú mə́bɛ̌', 'sɔ́ndɔ məlú mə́lɛ́', 'sɔ́ndɔ məlú mə́nyi', 'fúladé', 'séradé'],
    'weekdays_short' => ['sɔ́n', 'mɔ́n', 'smb', 'sml', 'smn', 'fúl', 'sér'],
    'weekdays_min' => ['sɔ́n', 'mɔ́n', 'smb', 'sml', 'smn', 'fúl', 'sér'],
    'months' => ['ngɔn osú', 'ngɔn bɛ̌', 'ngɔn lála', 'ngɔn nyina', 'ngɔn tána', 'ngɔn saməna', 'ngɔn zamgbála', 'ngɔn mwom', 'ngɔn ebulú', 'ngɔn awóm', 'ngɔn awóm ai dziá', 'ngɔn awóm ai bɛ̌'],
    'months_short' => ['ngo', 'ngb', 'ngl', 'ngn', 'ngt', 'ngs', 'ngz', 'ngm', 'nge', 'nga', 'ngad', 'ngab'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D/M/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],

    // Too unreliable
    /*
    'year' => ':count mbu', // less reliable
    'y' => ':count mbu', // less reliable
    'a_year' => ':count mbu', // less reliable

    'month' => ':count ngòn', // less reliable
    'm' => ':count ngòn', // less reliable
    'a_month' => ':count ngòn', // less reliable

    'week' => ':count mësë', // less reliable
    'w' => ':count mësë', // less reliable
    'a_week' => ':count mësë', // less reliable

    'day' => ':count mësë', // less reliable
    'd' => ':count mësë', // less reliable
    'a_day' => ':count mësë', // less reliable

    'hour' => ':count awola', // less reliable
    'h' => ':count awola', // less reliable
    'a_hour' => ':count awola', // less reliable

    'minute' => ':count awola', // less reliable
    'min' => ':count awola', // less reliable
    'a_minute' => ':count awola', // less reliable
    */
]);
