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
 * - RFC 2319    bug-glibc-locales@gnu.org
 */
return array_replace_recursive(require __DIR__.'/ru.php', [
    'weekdays' => ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
    'weekdays_short' => ['вск', 'пнд', 'вто', 'срд', 'чтв', 'птн', 'суб'],
    'weekdays_min' => ['вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'су'],
]);
