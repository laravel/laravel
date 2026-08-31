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
    }, 'sr_Cyrl_XK');
}
// @codeCoverageIgnoreEnd

return array_replace_recursive(require __DIR__.'/sr_Cyrl_BA.php', [
    'weekdays' => ['недеља', 'понедељак', 'уторак', 'среда', 'четвртак', 'петак', 'субота'],
]);
