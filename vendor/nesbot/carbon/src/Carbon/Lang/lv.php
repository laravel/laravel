<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\CarbonInterface;

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
 * - pirminis
 * - Tsutomu Kuroda
 * - tjku
 * - Andris Zāģeris
 * - Max Melentiev
 * - Edgars Beigarts
 * - Juanito Fatas
 * - Vitauts Stočka
 * - Akira Matsuda
 * - Christopher Dell
 * - Enrique Vidal
 * - Simone Carletti
 * - Aaron Patterson
 * - Kaspars Bankovskis
 * - Nicolás Hock Isaza
 * - Viesturs Kavacs (Kavacky)
 * - zakse
 * - Janis Eglitis (janiseglitis)
 * - Guntars
 * - Juris Sudmalis
 */
$daysOfWeek = ['svētdiena', 'pirmdiena', 'otrdiena', 'trešdiena', 'ceturtdiena', 'piektdiena', 'sestdiena'];
$daysOfWeekLocativum = ['svētdien', 'pirmdien', 'otrdien', 'trešdien', 'ceturtdien', 'piektdien', 'sestdien'];

$transformDiff = static fn (string $input) => strtr($input, [
    // Nominative => "pirms/pēc" Dative
    'gads' => 'gada',
    'gadi' => 'gadiem',
    'gadu' => 'gadiem',
    'mēnesis' => 'mēneša',
    'mēneši' => 'mēnešiem',
    'mēnešu' => 'mēnešiem',
    'nedēļa' => 'nedēļas',
    'nedēļas' => 'nedēļām',
    'nedēļu' => 'nedēļām',
    'diena' => 'dienas',
    'dienas' => 'dienām',
    'dienu' => 'dienām',
    'stunda' => 'stundas',
    'stundas' => 'stundām',
    'stundu' => 'stundām',
    'minūte' => 'minūtes',
    'minūtes' => 'minūtēm',
    'minūšu' => 'minūtēm',
    'sekunde' => 'sekundes',
    'sekundes' => 'sekundēm',
    'sekunžu' => 'sekundēm',
]);

return [
    'ago' => static fn (string $time) => 'pirms '.$transformDiff($time),
    'from_now' => static fn (string $time) => 'pēc '.$transformDiff($time),

    'year' => '0 gadu|:count gads|:count gadi',
    'y' => ':count g.',
    'a_year' => '{1}gads|0 gadu|:count gads|:count gadi',
    'month' => '0 mēnešu|:count mēnesis|:count mēneši',
    'm' => ':count mēn.',
    'a_month' => '{1}mēnesis|0 mēnešu|:count mēnesis|:count mēneši',
    'week' => '0 nedēļu|:count nedēļa|:count nedēļas',
    'w' => ':count ned.',
    'a_week' => '{1}nedēļa|0 nedēļu|:count nedēļa|:count nedēļas',
    'day' => '0 dienu|:count diena|:count dienas',
    'd' => ':count d.',
    'a_day' => '{1}diena|0 dienu|:count diena|:count dienas',
    'hour' => '0 stundu|:count stunda|:count stundas',
    'h' => ':count st.',
    'a_hour' => '{1}stunda|0 stundu|:count stunda|:count stundas',
    'minute' => '0 minūšu|:count minūte|:count minūtes',
    'min' => ':count min.',
    'a_minute' => '{1}minūte|0 minūšu|:count minūte|:count minūtes',
    'second' => '0 sekunžu|:count sekunde|:count sekundes',
    's' => ':count sek.',
    'a_second' => '{1}sekunde|0 sekunžu|:count sekunde|:count sekundes',

    'after' => ':time vēlāk',
    'year_after' => '0 gadus|:count gadu|:count gadus',
    'a_year_after' => '{1}gadu|0 gadus|:count gadu|:count gadus',
    'month_after' => '0 mēnešus|:count mēnesi|:count mēnešus',
    'a_month_after' => '{1}mēnesi|0 mēnešus|:count mēnesi|:count mēnešus',
    'week_after' => '0 nedēļas|:count nedēļu|:count nedēļas',
    'a_week_after' => '{1}nedēļu|0 nedēļas|:count nedēļu|:count nedēļas',
    'day_after' => '0 dienas|:count dienu|:count dienas',
    'a_day_after' => '{1}dienu|0 dienas|:count dienu|:count dienas',
    'hour_after' => '0 stundas|:count stundu|:count stundas',
    'a_hour_after' => '{1}stundu|0 stundas|:count stundu|:count stundas',
    'minute_after' => '0 minūtes|:count minūti|:count minūtes',
    'a_minute_after' => '{1}minūti|0 minūtes|:count minūti|:count minūtes',
    'second_after' => '0 sekundes|:count sekundi|:count sekundes',
    'a_second_after' => '{1}sekundi|0 sekundes|:count sekundi|:count sekundes',

    'before' => ':time agrāk',
    'year_before' => '0 gadus|:count gadu|:count gadus',
    'a_year_before' => '{1}gadu|0 gadus|:count gadu|:count gadus',
    'month_before' => '0 mēnešus|:count mēnesi|:count mēnešus',
    'a_month_before' => '{1}mēnesi|0 mēnešus|:count mēnesi|:count mēnešus',
    'week_before' => '0 nedēļas|:count nedēļu|:count nedēļas',
    'a_week_before' => '{1}nedēļu|0 nedēļas|:count nedēļu|:count nedēļas',
    'day_before' => '0 dienas|:count dienu|:count dienas',
    'a_day_before' => '{1}dienu|0 dienas|:count dienu|:count dienas',
    'hour_before' => '0 stundas|:count stundu|:count stundas',
    'a_hour_before' => '{1}stundu|0 stundas|:count stundu|:count stundas',
    'minute_before' => '0 minūtes|:count minūti|:count minūtes',
    'a_minute_before' => '{1}minūti|0 minūtes|:count minūti|:count minūtes',
    'second_before' => '0 sekundes|:count sekundi|:count sekundes',
    'a_second_before' => '{1}sekundi|0 sekundes|:count sekundi|:count sekundes',

    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => [', ', ' un '],

    'diff_now' => 'tagad',
    'diff_today' => 'šodien',
    'diff_yesterday' => 'vakar',
    'diff_before_yesterday' => 'aizvakar',
    'diff_tomorrow' => 'rīt',
    'diff_after_tomorrow' => 'parīt',

    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY.',
        'LL' => 'YYYY. [gada] D. MMMM',
        'LLL' => 'DD.MM.YYYY., HH:mm',
        'LLLL' => 'YYYY. [gada] D. MMMM, HH:mm',
    ],

    'calendar' => [
        'sameDay' => '[šodien] [plkst.] LT',
        'nextDay' => '[rīt] [plkst.] LT',
        'nextWeek' => static function (CarbonInterface $current, CarbonInterface $other) use ($daysOfWeekLocativum) {
            if ($current->week !== $other->week) {
                return '[nākošo] ['.$daysOfWeekLocativum[$current->dayOfWeek].'] [plkst.] LT';
            }

            return '['.$daysOfWeekLocativum[$current->dayOfWeek].'] [plkst.] LT';
        },
        'lastDay' => '[vakar] [plkst.] LT',
        'lastWeek' => static function (CarbonInterface $current) use ($daysOfWeekLocativum) {
            return '[pagājušo] ['.$daysOfWeekLocativum[$current->dayOfWeek].'] [plkst.] LT';
        },
        'sameElse' => 'L',
    ],

    'weekdays' => $daysOfWeek,
    'weekdays_short' => ['Sv.', 'P.', 'O.', 'T.', 'C.', 'Pk.', 'S.'],
    'weekdays_min' => ['Sv.', 'P.', 'O.', 'T.', 'C.', 'Pk.', 'S.'],
    'months' => ['janvāris', 'februāris', 'marts', 'aprīlis', 'maijs', 'jūnijs', 'jūlijs', 'augusts', 'septembris', 'oktobris', 'novembris', 'decembris'],
    'months_standalone' => ['janvārī', 'februārī', 'martā', 'aprīlī', 'maijā', 'jūnijā', 'jūlijā', 'augustā', 'septembrī', 'oktobrī', 'novembrī', 'decembrī'],
    'months_short' => ['janv.', 'febr.', 'martā', 'apr.', 'maijā', 'jūn.', 'jūl.', 'aug.', 'sept.', 'okt.', 'nov.', 'dec.'],
    'meridiem' => ['priekšpusdiena', 'pēcpusdiena'],
];
