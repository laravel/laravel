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
 * - Free Software Foundation, Inc.    bug-glibc-locales@gnu.org
 */
return array_replace_recursive(require __DIR__.'/fy.php', [
    'formats' => [
        'L' => 'DD-MM-YY',
    ],
    'months' => ['Jannewaris', 'Febrewaris', 'Maart', 'April', 'Maaie', 'Juny', 'July', 'Augustus', 'Septimber', 'Oktober', 'Novimber', 'Desimber'],
    'months_short' => ['Jan', 'Feb', 'Mrt', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
    'weekdays' => ['Snein', 'Moandei', 'Tiisdei', 'Woansdei', 'Tongersdei', 'Freed', 'Sneon'],
    'weekdays_short' => ['Sn', 'Mo', 'Ti', 'Wo', 'To', 'Fr', 'Sn'],
    'weekdays_min' => ['Sn', 'Mo', 'Ti', 'Wo', 'To', 'Fr', 'Sn'],
]);
