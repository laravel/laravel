<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon\Traits;

/**
 * Trait MagicParameter.
 *
 * Allows to retrieve parameter in magic calls by index or name.
 */
trait MagicParameter
{
    private function getMagicParameter(array $parameters, int $index, string $key, $default)
    {
        if (\array_key_exists($index, $parameters)) {
            return $parameters[$index];
        }

        if (\array_key_exists($key, $parameters)) {
            return $parameters[$key];
        }

        return $default;
    }
}
