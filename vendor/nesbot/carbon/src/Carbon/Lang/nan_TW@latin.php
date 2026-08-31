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
 * - Arne Goetje arne@canonical.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'YYYY-MM-DD',
    ],
    'months' => ['1goe̍h', '2goe̍h', '3goe̍h', '4goe̍h', '5goe̍h', '6goe̍h', '7goe̍h', '8goe̍h', '9goe̍h', '10goe̍h', '11goe̍h', '12goe̍h'],
    'months_short' => ['1g', '2g', '3g', '4g', '5g', '6g', '7g', '8g', '9g', '10g', '11g', '12g'],
    'weekdays' => ['lé-pài-ji̍t', 'pài-it', 'pài-jī', 'pài-saⁿ', 'pài-sì', 'pài-gō͘', 'pài-la̍k'],
    'weekdays_short' => ['lp', 'p1', 'p2', 'p3', 'p4', 'p5', 'p6'],
    'weekdays_min' => ['lp', 'p1', 'p2', 'p3', 'p4', 'p5', 'p6'],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['téng-po͘', 'ē-po͘'],
]);
