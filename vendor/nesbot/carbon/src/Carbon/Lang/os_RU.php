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
 * - bug-glibc-locales@gnu.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD.MM.YYYY',
    ],
    'months' => ['январы', 'февралы', 'мартъийы', 'апрелы', 'майы', 'июны', 'июлы', 'августы', 'сентябры', 'октябры', 'ноябры', 'декабры'],
    'months_short' => ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
    'weekdays' => ['Хуыцаубон', 'Къуырисæр', 'Дыццæг', 'Æртыццæг', 'Цыппæрæм', 'Майрæмбон', 'Сабат'],
    'weekdays_short' => ['Хцб', 'Крс', 'Дцг', 'Æрт', 'Цпр', 'Мрб', 'Сбт'],
    'weekdays_min' => ['Хцб', 'Крс', 'Дцг', 'Æрт', 'Цпр', 'Мрб', 'Сбт'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,

    'minute' => ':count гыццыл', // less reliable
    'min' => ':count гыццыл', // less reliable
    'a_minute' => ':count гыццыл', // less reliable

    'second' => ':count æндæр', // less reliable
    's' => ':count æндæр', // less reliable
    'a_second' => ':count æндæр', // less reliable

    'year' => ':count аз',
    'y' => ':count аз',
    'a_year' => ':count аз',

    'month' => ':count мӕй',
    'm' => ':count мӕй',
    'a_month' => ':count мӕй',

    'week' => ':count къуыри',
    'w' => ':count къуыри',
    'a_week' => ':count къуыри',

    'day' => ':count бон',
    'd' => ':count бон',
    'a_day' => ':count бон',

    'hour' => ':count сахат',
    'h' => ':count сахат',
    'a_hour' => ':count сахат',
]);
