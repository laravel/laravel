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
 * - Dieter Sting
 * - François B
 * - Maxime VALY
 * - JD Isaacks
 */
return array_replace_recursive(require __DIR__.'/fr.php', [
    'formats' => [
        'L' => 'YYYY-MM-DD',
    ],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
]);
