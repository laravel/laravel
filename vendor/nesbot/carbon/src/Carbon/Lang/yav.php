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
    'meridiem' => ['kiɛmɛ́ɛm', 'kisɛ́ndɛ'],
    'weekdays' => ['sɔ́ndiɛ', 'móndie', 'muányáŋmóndie', 'metúkpíápɛ', 'kúpélimetúkpiapɛ', 'feléte', 'séselé'],
    'weekdays_short' => ['sd', 'md', 'mw', 'et', 'kl', 'fl', 'ss'],
    'weekdays_min' => ['sd', 'md', 'mw', 'et', 'kl', 'fl', 'ss'],
    'months' => ['pikítíkítie, oólí ú kutúan', 'siɛyɛ́, oóli ú kándíɛ', 'ɔnsúmbɔl, oóli ú kátátúɛ', 'mesiŋ, oóli ú kénie', 'ensil, oóli ú kátánuɛ', 'ɔsɔn', 'efute', 'pisuyú', 'imɛŋ i puɔs', 'imɛŋ i putúk,oóli ú kátíɛ', 'makandikɛ', 'pilɔndɔ́'],
    'months_short' => ['o.1', 'o.2', 'o.3', 'o.4', 'o.5', 'o.6', 'o.7', 'o.8', 'o.9', 'o.10', 'o.11', 'o.12'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D/M/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],
]);
