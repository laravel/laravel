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
    'meridiem' => ['wichishu', 'mchochil’l'],
    'weekdays' => ['Sabato', 'Jumatatu', 'Jumanne', 'Jumatano', 'Arahamisi', 'Ijumaa', 'Jumamosi'],
    'weekdays_short' => ['Sab', 'Jtt', 'Jnn', 'Jtn', 'Ara', 'Iju', 'Jmo'],
    'weekdays_min' => ['Sab', 'Jtt', 'Jnn', 'Jtn', 'Ara', 'Iju', 'Jmo'],
    'months' => ['Mweri wo kwanza', 'Mweri wo unayeli', 'Mweri wo uneraru', 'Mweri wo unecheshe', 'Mweri wo unethanu', 'Mweri wo thanu na mocha', 'Mweri wo saba', 'Mweri wo nane', 'Mweri wo tisa', 'Mweri wo kumi', 'Mweri wo kumi na moja', 'Mweri wo kumi na yel’li'],
    'months_short' => ['Kwa', 'Una', 'Rar', 'Che', 'Tha', 'Moc', 'Sab', 'Nan', 'Tis', 'Kum', 'Moj', 'Yel'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
]);
