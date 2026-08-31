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
 * - Pablo Saratxaga pablo@mandriva.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'MM/DD/YY',
    ],
    'months' => ['ᔮᓄᐊᓕ', 'ᕕᕗᐊᓕ', 'ᒪᔅᓯ', 'ᐃᐳᓗ', 'ᒪᐃ', 'ᔪᓂ', 'ᔪᓚᐃ', 'ᐊᒋᓯ', 'ᓯᑎᕙ', 'ᐊᑦᑐᕙ', 'ᓄᕕᕙ', 'ᑎᓯᕝᕙ'],
    'months_short' => ['ᔮᓄ', 'ᕕᕗ', 'ᒪᔅ', 'ᐃᐳ', 'ᒪᐃ', 'ᔪᓂ', 'ᔪᓚ', 'ᐊᒋ', 'ᓯᑎ', 'ᐊᑦ', 'ᓄᕕ', 'ᑎᓯ'],
    'weekdays' => ['ᓈᑦᑎᖑᔭᕐᕕᒃ', 'ᓇᒡᒐᔾᔭᐅ', 'ᓇᒡᒐᔾᔭᐅᓕᖅᑭᑦ', 'ᐱᖓᓲᓕᖅᓯᐅᑦ', 'ᕿᑎᖅᑰᑦ', 'ᐅᓪᓗᕈᓘᑐᐃᓇᖅ', 'ᓯᕙᑖᕕᒃ'],
    'weekdays_short' => ['ᓈ', 'ᓇ', 'ᓕ', 'ᐱ', 'ᕿ', 'ᐅ', 'ᓯ'],
    'weekdays_min' => ['ᓈ', 'ᓇ', 'ᓕ', 'ᐱ', 'ᕿ', 'ᐅ', 'ᓯ'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,

    'year' => ':count ᐅᑭᐅᖅ',
    'y' => ':count ᐅᑭᐅᖅ',
    'a_year' => ':count ᐅᑭᐅᖅ',

    'month' => ':count qaammat',
    'm' => ':count qaammat',
    'a_month' => ':count qaammat',

    'week' => ':count sapaatip akunnera',
    'w' => ':count sapaatip akunnera',
    'a_week' => ':count sapaatip akunnera',

    'day' => ':count ulloq',
    'd' => ':count ulloq',
    'a_day' => ':count ulloq',

    'hour' => ':count ikarraq',
    'h' => ':count ikarraq',
    'a_hour' => ':count ikarraq',

    'minute' => ':count titiqqaralaaq', // less reliable
    'min' => ':count titiqqaralaaq', // less reliable
    'a_minute' => ':count titiqqaralaaq', // less reliable

    'second' => ':count marluk', // less reliable
    's' => ':count marluk', // less reliable
    'a_second' => ':count marluk', // less reliable
]);
