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
 * - Cassiano Montanari
 * - Matt Pope
 * - François B
 * - Prodis
 * - JD Isaacks
 * - Raphael Amorim
 * - João Magalhães
 * - victortobias
 * - Paulo Freitas
 * - Sebastian Thierer
 * - Claudson Martins (claudsonm)
 */

use Carbon\CarbonInterface;

return [
    'year' => ':count ano|:count anos',
    'a_year' => 'um ano|:count anos',
    'y' => ':counta',
    'month' => ':count mês|:count meses',
    'a_month' => 'um mês|:count meses',
    'm' => ':countm',
    'week' => ':count semana|:count semanas',
    'a_week' => 'uma semana|:count semanas',
    'w' => ':countsem',
    'day' => ':count dia|:count dias',
    'a_day' => 'um dia|:count dias',
    'd' => ':countd',
    'hour' => ':count hora|:count horas',
    'a_hour' => 'uma hora|:count horas',
    'h' => ':counth',
    'minute' => ':count minuto|:count minutos',
    'a_minute' => 'um minuto|:count minutos',
    'min' => ':countmin',
    'second' => ':count segundo|:count segundos',
    'a_second' => 'alguns segundos|:count segundos',
    's' => ':counts',
    'millisecond' => ':count milissegundo|:count milissegundos',
    'a_millisecond' => 'um milissegundo|:count milissegundos',
    'ms' => ':countms',
    'microsecond' => ':count microssegundo|:count microssegundos',
    'a_microsecond' => 'um microssegundo|:count microssegundos',
    'µs' => ':countµs',
    'ago' => 'há :time',
    'from_now' => 'em :time',
    'after' => ':time depois',
    'before' => ':time antes',
    'diff_now' => 'agora',
    'diff_today' => 'Hoje',
    'diff_today_regexp' => 'Hoje(?:\\s+às)?',
    'diff_yesterday' => 'ontem',
    'diff_yesterday_regexp' => 'Ontem(?:\\s+às)?',
    'diff_tomorrow' => 'amanhã',
    'diff_tomorrow_regexp' => 'Amanhã(?:\\s+às)?',
    'diff_before_yesterday' => 'anteontem',
    'diff_after_tomorrow' => 'depois de amanhã',
    'period_recurrences' => 'uma vez|:count vezes',
    'period_interval' => 'cada :interval',
    'period_start_date' => 'de :date',
    'period_end_date' => 'até :date',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D [de] MMMM [de] YYYY',
        'LLL' => 'D [de] MMMM [de] YYYY HH:mm',
        'LLLL' => 'dddd, D [de] MMMM [de] YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Hoje às] LT',
        'nextDay' => '[Amanhã às] LT',
        'nextWeek' => 'dddd [às] LT',
        'lastDay' => '[Ontem às] LT',
        'lastWeek' => static fn (CarbonInterface $date) => match ($date->dayOfWeek) {
            0, 6 => '[Último] dddd [às] LT',
            default => '[Última] dddd [às] LT',
        },
        'sameElse' => 'L',
    ],
    'ordinal' => ':numberº',
    'months' => ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'],
    'months_short' => ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'],
    'weekdays' => ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'],
    'weekdays_short' => ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sáb'],
    'weekdays_min' => ['Do', '2ª', '3ª', '4ª', '5ª', '6ª', 'Sá'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' e '],
    'ordinal_words' => [
        'of' => 'de',
        'first' => 'primeira',
        'second' => 'segunda',
        'third' => 'terceira',
        'fourth' => 'quarta',
        'fifth' => 'quinta',
        'last' => 'última',
    ],
];
