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
    'first_day_of_week' => 0,
    'meridiem' => ['Luma lwa K', 'luma lwa p'],
    'weekdays' => ['Ituku ja jumwa', 'Kuramuka jimweri', 'Kuramuka kawi', 'Kuramuka kadadu', 'Kuramuka kana', 'Kuramuka kasanu', 'Kifula nguwo'],
    'weekdays_short' => ['Jum', 'Jim', 'Kaw', 'Kad', 'Kan', 'Kas', 'Ngu'],
    'weekdays_min' => ['Jum', 'Jim', 'Kaw', 'Kad', 'Kan', 'Kas', 'Ngu'],
    'months' => ['Mori ghwa imbiri', 'Mori ghwa kawi', 'Mori ghwa kadadu', 'Mori ghwa kana', 'Mori ghwa kasanu', 'Mori ghwa karandadu', 'Mori ghwa mfungade', 'Mori ghwa wunyanya', 'Mori ghwa ikenda', 'Mori ghwa ikumi', 'Mori ghwa ikumi na imweri', 'Mori ghwa ikumi na iwi'],
    'months_short' => ['Imb', 'Kaw', 'Kad', 'Kan', 'Kas', 'Kar', 'Mfu', 'Wun', 'Ike', 'Iku', 'Imw', 'Iwi'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
]);
