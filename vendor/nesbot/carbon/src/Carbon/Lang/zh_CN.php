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
 * - monkeycon
 * - François B
 * - Jason Katz-Brown
 * - Serhan Apaydın
 * - Matt Johnson
 * - JD Isaacks
 * - Zeno Zeng
 * - Chris Hemp
 * - shankesgk2
 */
return array_merge(require __DIR__.'/zh.php', [
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'YYYY/MM/DD',
        'LL' => 'YYYY年M月D日',
        'LLL' => 'YYYY年M月D日Ah点mm分',
        'LLLL' => 'YYYY年M月D日ddddAh点mm分',
    ],
]);
