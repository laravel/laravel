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
    'meridiem' => ['Adduha', 'Aluula'],
    'weekdays' => ['Alhadi', 'Atini', 'Atalata', 'Alarba', 'Alhamiisa', 'Aljuma', 'Assabdu'],
    'weekdays_short' => ['Alh', 'Ati', 'Ata', 'Ala', 'Alm', 'Alj', 'Ass'],
    'weekdays_min' => ['Alh', 'Ati', 'Ata', 'Ala', 'Alm', 'Alj', 'Ass'],
    'months' => ['Žanwiye', 'Feewiriye', 'Marsi', 'Awiril', 'Me', 'Žuweŋ', 'Žuyye', 'Ut', 'Sektanbur', 'Oktoobur', 'Noowanbur', 'Deesanbur'],
    'months_short' => ['Žan', 'Fee', 'Mar', 'Awi', 'Me', 'Žuw', 'Žuy', 'Ut', 'Sek', 'Okt', 'Noo', 'Dee'],
    'first_day_of_week' => 1,
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D/M/YYYY',
        'LL' => 'D MMM, YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],
]);
