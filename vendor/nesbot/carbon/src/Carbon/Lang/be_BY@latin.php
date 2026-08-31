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
    'months' => ['studzienia', 'lutaha', 'sakavika', 'krasavika', 'maja', 'červienia', 'lipienia', 'žniŭnia', 'vieraśnia', 'kastryčnika', 'listapada', 'śniežnia'],
    'months_short' => ['Stu', 'Lut', 'Sak', 'Kra', 'Maj', 'Čer', 'Lip', 'Žni', 'Vie', 'Kas', 'Lis', 'Śni'],
    'weekdays' => ['Niadziela', 'Paniadziełak', 'Aŭtorak', 'Sierada', 'Čaćvier', 'Piatnica', 'Subota'],
    'weekdays_short' => ['Nia', 'Pan', 'Aŭt', 'Sie', 'Čać', 'Pia', 'Sub'],
    'weekdays_min' => ['Nia', 'Pan', 'Aŭt', 'Sie', 'Čać', 'Pia', 'Sub'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
]);
