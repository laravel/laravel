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

    'month' => ':count haŋwí', // less reliable
    'm' => ':count haŋwí', // less reliable
    'a_month' => ':count haŋwí', // less reliable

    'week' => ':count šakówiŋ', // less reliable
    'w' => ':count šakówiŋ', // less reliable
    'a_week' => ':count šakówiŋ', // less reliable

    'hour' => ':count maza škaŋškaŋ', // less reliable
    'h' => ':count maza škaŋškaŋ', // less reliable
    'a_hour' => ':count maza škaŋškaŋ', // less reliable

    'minute' => ':count číkʼala', // less reliable
    'min' => ':count číkʼala', // less reliable
    'a_minute' => ':count číkʼala', // less reliable

    'year' => ':count waníyetu',
    'y' => ':count waníyetu',
    'a_year' => ':count waníyetu',

    'day' => ':count aŋpétu',
    'd' => ':count aŋpétu',
    'a_day' => ':count aŋpétu',

    'second' => ':count icinuŋpa',
    's' => ':count icinuŋpa',
    'a_second' => ':count icinuŋpa',
]);
