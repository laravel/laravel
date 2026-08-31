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
    'meridiem' => ['KI', 'UT'],
    'weekdays' => ['Kiumia', 'Njumatatu', 'Njumaine', 'Njumatano', 'Aramithi', 'Njumaa', 'NJumamothii'],
    'weekdays_short' => ['Kma', 'Tat', 'Ine', 'Tan', 'Arm', 'Maa', 'NMM'],
    'weekdays_min' => ['Kma', 'Tat', 'Ine', 'Tan', 'Arm', 'Maa', 'NMM'],
    'months' => ['Mweri wa mbere', 'Mweri wa kaĩri', 'Mweri wa kathatũ', 'Mweri wa kana', 'Mweri wa gatano', 'Mweri wa gatantatũ', 'Mweri wa mũgwanja', 'Mweri wa kanana', 'Mweri wa kenda', 'Mweri wa ikũmi', 'Mweri wa ikũmi na ũmwe', 'Mweri wa ikũmi na Kaĩrĩ'],
    'months_short' => ['Mbe', 'Kai', 'Kat', 'Kan', 'Gat', 'Gan', 'Mug', 'Knn', 'Ken', 'Iku', 'Imw', 'Igi'],
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd, D MMMM YYYY HH:mm',
    ],
]);
