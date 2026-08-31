<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/se.php', [
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],
    'months' => ['ođđajagemánnu', 'guovvamánnu', 'njukčamánnu', 'cuoŋománnu', 'miessemánnu', 'geassemánnu', 'suoidnemánnu', 'borgemánnu', 'čakčamánnu', 'golggotmánnu', 'skábmamánnu', 'juovlamánnu'],
    'months_short' => ['ođđj', 'guov', 'njuk', 'cuoŋ', 'mies', 'geas', 'suoi', 'borg', 'čakč', 'golg', 'skáb', 'juov'],
    'weekdays' => ['sotnabeaivi', 'mánnodat', 'disdat', 'gaskavahkku', 'duorastat', 'bearjadat', 'lávvordat'],
    'weekdays_short' => ['so', 'má', 'di', 'ga', 'du', 'be', 'lá'],
    'weekdays_min' => ['so', 'má', 'di', 'ga', 'du', 'be', 'lá'],
    'meridiem' => ['i', 'e'],
]);
