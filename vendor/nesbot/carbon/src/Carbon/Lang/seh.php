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
    'weekdays' => ['Dimingu', 'Chiposi', 'Chipiri', 'Chitatu', 'Chinai', 'Chishanu', 'Sabudu'],
    'weekdays_short' => ['Dim', 'Pos', 'Pir', 'Tat', 'Nai', 'Sha', 'Sab'],
    'weekdays_min' => ['Dim', 'Pos', 'Pir', 'Tat', 'Nai', 'Sha', 'Sab'],
    'months' => ['Janeiro', 'Fevreiro', 'Marco', 'Abril', 'Maio', 'Junho', 'Julho', 'Augusto', 'Setembro', 'Otubro', 'Novembro', 'Decembro'],
    'months_short' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Aug', 'Set', 'Otu', 'Nov', 'Dec'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D/M/YYYY',
        'LL' => 'd [de] MMM [de] YYYY',
        'LLL' => 'd [de] MMMM [de] YYYY HH:mm',
        'LLLL' => 'dddd, d [de] MMMM [de] YYYY HH:mm',
    ],
]);
