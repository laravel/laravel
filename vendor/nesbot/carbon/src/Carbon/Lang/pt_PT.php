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
 * - RAP    bug-glibc-locales@gnu.org
 */
return array_replace_recursive(require __DIR__.'/pt.php', [
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
    'months' => ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'],
    'months_short' => ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'],
    'weekdays' => ['domingo', 'segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sábado'],
    'weekdays_short' => ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sáb'],
    'weekdays_min' => ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sáb'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
]);
