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
    'meridiem' => ['idiɓa', 'ebyámu'],
    'weekdays' => ['éti', 'mɔ́sú', 'kwasú', 'mukɔ́sú', 'ŋgisú', 'ɗónɛsú', 'esaɓasú'],
    'weekdays_short' => ['ét', 'mɔ́s', 'kwa', 'muk', 'ŋgi', 'ɗón', 'esa'],
    'weekdays_min' => ['ét', 'mɔ́s', 'kwa', 'muk', 'ŋgi', 'ɗón', 'esa'],
    'months' => ['dimɔ́di', 'ŋgɔndɛ', 'sɔŋɛ', 'diɓáɓá', 'emiasele', 'esɔpɛsɔpɛ', 'madiɓɛ́díɓɛ́', 'diŋgindi', 'nyɛtɛki', 'mayésɛ́', 'tiníní', 'eláŋgɛ́'],
    'months_short' => ['di', 'ŋgɔn', 'sɔŋ', 'diɓ', 'emi', 'esɔ', 'mad', 'diŋ', 'nyɛt', 'may', 'tin', 'elá'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D/M/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],

    'year' => ':count ma mbu', // less reliable
    'y' => ':count ma mbu', // less reliable
    'a_year' => ':count ma mbu', // less reliable

    'month' => ':count myo̱di', // less reliable
    'm' => ':count myo̱di', // less reliable
    'a_month' => ':count myo̱di', // less reliable

    'week' => ':count woki', // less reliable
    'w' => ':count woki', // less reliable
    'a_week' => ':count woki', // less reliable

    'day' => ':count buńa', // less reliable
    'd' => ':count buńa', // less reliable
    'a_day' => ':count buńa', // less reliable

    'hour' => ':count ma awa', // less reliable
    'h' => ':count ma awa', // less reliable
    'a_hour' => ':count ma awa', // less reliable

    'minute' => ':count minuti', // less reliable
    'min' => ':count minuti', // less reliable
    'a_minute' => ':count minuti', // less reliable

    'second' => ':count maba', // less reliable
    's' => ':count maba', // less reliable
    'a_second' => ':count maba', // less reliable
]);
