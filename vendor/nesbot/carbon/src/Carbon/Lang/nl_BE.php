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
 * - Roy
 * - Stephan
 * - François B
 * - Tim Fish
 * - Kevin Huang
 * - Jacob Middag
 * - JD Isaacks
 * - Propaganistas
 */
return array_replace_recursive(require __DIR__.'/nl.php', [
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
]);
