<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Translation\PluralizationRules;

// @codeCoverageIgnoreStart
if (class_exists(PluralizationRules::class)) {
    PluralizationRules::set(static function ($number) {
        return PluralizationRules::get($number, 'sr');
    }, 'sr_Cyrl_BA');
}
// @codeCoverageIgnoreEnd

return array_replace_recursive(require __DIR__.'/sr_Cyrl.php', [
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'D.M.yy.',
        'LL' => 'DD.MM.YYYY.',
        'LLL' => 'DD. MMMM YYYY. HH:mm',
        'LLLL' => 'dddd, DD. MMMM YYYY. HH:mm',
    ],
    'weekdays' => ['недјеља', 'понедељак', 'уторак', 'сриједа', 'четвртак', 'петак', 'субота'],
    'weekdays_short' => ['нед.', 'пон.', 'ут.', 'ср.', 'чет.', 'пет.', 'суб.'],
]);
