<?php

declare(strict_types=1);

/*
 * This file is part of the league/config package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Config;

/**
 * Provides read-only access to a given Configuration object
 */
final class ReadOnlyConfiguration implements ConfigurationInterface
{
    private Configuration $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        return $this->config->get($key);
    }

    public function exists(string $key): bool
    {
        return $this->config->exists($key);
    }
}
