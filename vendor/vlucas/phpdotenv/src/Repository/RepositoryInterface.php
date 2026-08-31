<?php

declare(strict_types=1);

namespace Dotenv\Repository;

interface RepositoryInterface
{
    /**
     * Determine if the given environment variable is defined.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name);

    /**
     * Get an environment variable.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return string|null
     */
    public function get(string $name);

    /**
     * Set an environment variable.
     *
     * @param string $name
     * @param string $value
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function set(string $name, string $value);

    /**
     * Clear an environment variable.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function clear(string $name);
}
