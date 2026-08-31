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
return array_replace_recursive(require __DIR__.'/nl.php', [
    'months' => ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
    'months_short' => ['jan', 'feb', 'mrt', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
    'weekdays' => ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
    'weekdays_short' => ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
    'weekdays_min' => ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
]);
