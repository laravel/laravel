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
 * - Ge'ez Frontier Foundation    locales@geez.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
    'months' => ['Qunxa Garablu', 'Naharsi Kudo', 'Ciggilta Kudo', 'Agda Baxisso', 'Caxah Alsa', 'Qasa Dirri', 'Qado Dirri', 'Leqeeni', 'Waysu', 'Diteli', 'Ximoli', 'Kaxxa Garablu'],
    'months_short' => ['Qun', 'Nah', 'Cig', 'Agd', 'Cax', 'Qas', 'Qad', 'Leq', 'Way', 'Dit', 'Xim', 'Kax'],
    'weekdays' => ['Naba Sambat', 'Sani', 'Salus', 'Rabuq', 'Camus', 'Jumqata', 'Qunxa Sambat'],
    'weekdays_short' => ['Nab', 'San', 'Sal', 'Rab', 'Cam', 'Jum', 'Qun'],
    'weekdays_min' => ['Nab', 'San', 'Sal', 'Rab', 'Cam', 'Jum', 'Qun'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['saaku', 'carra'],
]);
