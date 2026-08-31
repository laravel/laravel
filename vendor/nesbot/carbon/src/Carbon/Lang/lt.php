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
 * - Philippe Vaucher
 * - Tsutomu Kuroda
 * - tjku
 * - valdas406
 * - Justas Palumickas
 * - Max Melentiev
 * - Andrius Janauskas
 * - Juanito Fatas
 * - Akira Matsuda
 * - Christopher Dell
 * - Enrique Vidal
 * - Simone Carletti
 * - Aaron Patterson
 * - Nicolás Hock Isaza
 * - Laurynas Butkus
 * - Sven Fuchs
 * - Dominykas Tijūnaitis
 * - Justinas Bolys
 * - Ričardas
 * - Kirill Chalkin
 * - Rolandas
 * - Justinas (Gamesh)
 * - Sam Axe
 */
return [
    'year' => ':count metai|:count metai|:count metų',
    'y' => ':count m.',
    'month' => ':count mėnuo|:count mėnesiai|:count mėnesį',
    'm' => ':count mėn.',
    'week' => ':count savaitė|:count savaitės|:count savaitę',
    'w' => ':count sav.',
    'day' => ':count diena|:count dienos|:count dienų',
    'd' => ':count d.',
    'hour' => ':count valanda|:count valandos|:count valandų',
    'h' => ':count val.',
    'minute' => ':count minutė|:count minutės|:count minutę',
    'min' => ':count min.',
    'second' => ':count sekundė|:count sekundės|:count sekundžių',
    's' => ':count sek.',

    'year_ago' => ':count metus|:count metus|:count metų',
    'month_ago' => ':count mėnesį|:count mėnesius|:count mėnesių',
    'week_ago' => ':count savaitę|:count savaites|:count savaičių',
    'day_ago' => ':count dieną|:count dienas|:count dienų',
    'hour_ago' => ':count valandą|:count valandas|:count valandų',
    'minute_ago' => ':count minutę|:count minutes|:count minučių',
    'second_ago' => ':count sekundę|:count sekundes|:count sekundžių',

    'year_from_now' => ':count metai|:count metai|:count metų',
    'month_from_now' => ':count mėnuo|:count mėnesiai|:count mėnesių',
    'week_from_now' => ':count savaitė|:count savaitės|:count savaičių',
    'day_from_now' => ':count diena|:count dienos|:count dienų',
    'hour_from_now' => ':count valanda|:count valandos|:count valandų',
    'minute_from_now' => ':count minutė|:count minutės|:count minučių',
    'second_from_now' => ':count sekundė|:count sekundės|:count sekundžių',

    'year_after' => ':count metai|:count metai|:count metų',
    'month_after' => ':count mėnuo|:count mėnesiai|:count mėnesių',
    'week_after' => ':count savaitė|:count savaitės|:count savaičių',
    'day_after' => ':count diena|:count dienos|:count dienų',
    'hour_after' => ':count valanda|:count valandos|:count valandų',
    'minute_after' => ':count minutė|:count minutės|:count minučių',
    'second_after' => ':count sekundė|:count sekundės|:count sekundžių',

    'year_before' => ':count metų',
    'month_before' => ':count mėnesio|:count mėnesių|:count mėnesių',
    'week_before' => ':count savaitės|:count savaičių|:count savaičių',
    'day_before' => ':count dienos|:count dienų|:count dienų',
    'hour_before' => ':count valandos|:count valandų|:count valandų',
    'minute_before' => ':count minutės|:count minučių|:count minučių',
    'second_before' => ':count sekundės|:count sekundžių|:count sekundžių',

    'ago' => 'prieš :time',
    'from_now' => ':time nuo dabar',
    'after' => 'po :time',
    'before' => 'už :time',

    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,

    'diff_now' => 'ką tik',
    'diff_today' => 'Šiandien',
    'diff_yesterday' => 'vakar',
    'diff_yesterday_regexp' => 'Vakar',
    'diff_tomorrow' => 'rytoj',
    'diff_tomorrow_regexp' => 'Rytoj',
    'diff_before_yesterday' => 'užvakar',
    'diff_after_tomorrow' => 'poryt',

    'period_recurrences' => 'kartą|:count kartų',
    'period_interval' => 'kiekvieną :interval',
    'period_start_date' => 'nuo :date',
    'period_end_date' => 'iki :date',

    'months' => ['sausio', 'vasario', 'kovo', 'balandžio', 'gegužės', 'birželio', 'liepos', 'rugpjūčio', 'rugsėjo', 'spalio', 'lapkričio', 'gruodžio'],
    'months_standalone' => ['sausis', 'vasaris', 'kovas', 'balandis', 'gegužė', 'birželis', 'liepa', 'rugpjūtis', 'rugsėjis', 'spalis', 'lapkritis', 'gruodis'],
    'months_regexp' => '/(L{2,4}|D[oD]?(\[[^\[\]]*\]|\s)+MMMM?|MMMM?(\[[^\[\]]*\]|\s)+D[oD]?)/',
    'months_short' => ['sau', 'vas', 'kov', 'bal', 'geg', 'bir', 'lie', 'rgp', 'rgs', 'spa', 'lap', 'gru'],
    'weekdays' => ['sekmadienį', 'pirmadienį', 'antradienį', 'trečiadienį', 'ketvirtadienį', 'penktadienį', 'šeštadienį'],
    'weekdays_standalone' => ['sekmadienis', 'pirmadienis', 'antradienis', 'trečiadienis', 'ketvirtadienis', 'penktadienis', 'šeštadienis'],
    'weekdays_short' => ['sek', 'pir', 'ant', 'tre', 'ket', 'pen', 'šeš'],
    'weekdays_min' => ['se', 'pi', 'an', 'tr', 'ke', 'pe', 'še'],
    'list' => [', ', ' ir '],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'YYYY-MM-DD',
        'LL' => 'MMMM DD, YYYY',
        'LLL' => 'DD MMM HH:mm',
        'LLLL' => 'MMMM DD, YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[Šiandien] LT',
        'nextDay' => '[Rytoj] LT',
        'nextWeek' => 'dddd LT',
        'lastDay' => '[Vakar] LT',
        'lastWeek' => '[Paskutinį] dddd LT',
        'sameElse' => 'L',
    ],
    'ordinal' => static function ($number) {
        return match ($number) {
            0 => '0-is',
            3 => '3-ias',
            default => "$number-as",
        };
    },
    'meridiem' => ['priešpiet', 'popiet'],
];
