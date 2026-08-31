<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy;

/**
 * Environment variables implementation via getenv().
 */
class SystemEnv implements EnvInterface
{
    /**
     * Get an environment variable by name.
     *
     * @return string|null
     */
    public function get(string $key)
    {
        if (isset($_SERVER[$key]) && $_SERVER[$key]) {
            return $_SERVER[$key];
        }

        $result = \getenv($key);

        return $result === false ? null : $result;
    }
}
