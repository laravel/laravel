<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/pa.php', [
    'formats' => [
        'LT' => 'h:mm a',
        'LTS' => 'h:mm:ss a',
        'L' => 'D/M/yy',
        'LL' => 'D MMM YYYY',
        'LLL' => 'D MMMM YYYY, h:mm a',
        'LLLL' => 'dddd, D MMMM YYYY, h:mm a',
    ],
    'months' => ['ਜਨਵਰੀ', 'ਫ਼ਰਵਰੀ', 'ਮਾਰਚ', 'ਅਪ੍ਰੈਲ', 'ਮਈ', 'ਜੂਨ', 'ਜੁਲਾਈ', 'ਅਗਸਤ', 'ਸਤੰਬਰ', 'ਅਕਤੂਬਰ', 'ਨਵੰਬਰ', 'ਦਸੰਬਰ'],
    'months_short' => ['ਜਨ', 'ਫ਼ਰ', 'ਮਾਰਚ', 'ਅਪ੍ਰੈ', 'ਮਈ', 'ਜੂਨ', 'ਜੁਲਾ', 'ਅਗ', 'ਸਤੰ', 'ਅਕਤੂ', 'ਨਵੰ', 'ਦਸੰ'],
    'weekdays' => ['ਐਤਵਾਰ', 'ਸੋਮਵਾਰ', 'ਮੰਗਲਵਾਰ', 'ਬੁੱਧਵਾਰ', 'ਵੀਰਵਾਰ', 'ਸ਼ੁੱਕਰਵਾਰ', 'ਸ਼ਨਿੱਚਰਵਾਰ'],
    'weekdays_short' => ['ਐਤ', 'ਸੋਮ', 'ਮੰਗਲ', 'ਬੁੱਧ', 'ਵੀਰ', 'ਸ਼ੁੱਕਰ', 'ਸ਼ਨਿੱਚਰ'],
    'weekdays_min' => ['ਐਤ', 'ਸੋਮ', 'ਮੰਗ', 'ਬੁੱਧ', 'ਵੀਰ', 'ਸ਼ੁੱਕ', 'ਸ਼ਨਿੱ'],
    'weekend' => [0, 0],
]);
