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
 * - Jordi Mallach jordi@gnu.org
 * - Adolfo Jayme-Barrientos (fitojb)
 */
return array_replace_recursive(require __DIR__.'/es.php', [
    'formats' => [
        'L' => 'DD/MM/YY',
    ],
    'months' => ['de xineru', 'de febreru', 'de marzu', 'd’abril', 'de mayu', 'de xunu', 'de xunetu', 'd’agostu', 'de setiembre', 'd’ochobre', 'de payares', 'd’avientu'],
    'months_short' => ['xin', 'feb', 'mar', 'abr', 'may', 'xun', 'xnt', 'ago', 'set', 'och', 'pay', 'avi'],
    'weekdays' => ['domingu', 'llunes', 'martes', 'miércoles', 'xueves', 'vienres', 'sábadu'],
    'weekdays_short' => ['dom', 'llu', 'mar', 'mié', 'xue', 'vie', 'sáb'],
    'weekdays_min' => ['dom', 'llu', 'mar', 'mié', 'xue', 'vie', 'sáb'],

    'year' => ':count añu|:count años',
    'y' => ':count añu|:count años',
    'a_year' => 'un añu|:count años',

    'month' => ':count mes',
    'm' => ':count mes',
    'a_month' => 'un mes|:count mes',

    'week' => ':count selmana|:count selmanes',
    'w' => ':count selmana|:count selmanes',
    'a_week' => 'una selmana|:count selmanes',

    'day' => ':count día|:count díes',
    'd' => ':count día|:count díes',
    'a_day' => 'un día|:count díes',

    'hour' => ':count hora|:count hores',
    'h' => ':count hora|:count hores',
    'a_hour' => 'una hora|:count hores',

    'minute' => ':count minutu|:count minutos',
    'min' => ':count minutu|:count minutos',
    'a_minute' => 'un minutu|:count minutos',

    'second' => ':count segundu|:count segundos',
    's' => ':count segundu|:count segundos',
    'a_second' => 'un segundu|:count segundos',

    'ago' => 'hai :time',
    'from_now' => 'en :time',
    'after' => ':time dempués',
    'before' => ':time enantes',
]);
