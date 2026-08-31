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
 * - xuri
 * - sycuato
 * - bokideckonja
 * - Luo Ning
 * - William Yang (williamyang233)
 */
return array_merge(require __DIR__.'/zh_Hans.php', [
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'YYYY/MM/DD',
        'LL' => 'YYYY年M月D日',
        'LLL' => 'YYYY年M月D日 A h点mm分',
        'LLLL' => 'YYYY年M月D日dddd A h点mm分',
    ],
]);
