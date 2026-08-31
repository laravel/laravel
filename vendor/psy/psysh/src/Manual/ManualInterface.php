<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Manual;

/**
 * Interface for PHP manual loaders.
 *
 * Provides a unified interface for accessing PHP manual documentation
 * regardless of the underlying storage format (SQLite, Phar, PHP file).
 */
interface ManualInterface
{
    /**
     * Get documentation for a given ID.
     *
     * @param string $id Documentation ID (e.g., 'strlen', 'PDO::query')
     *
     * @return string|array|null Formatted string (v2) or structured data (v3), or null if not found
     */
    public function get(string $id);

    /**
     * Get all available manual IDs, if the manual format exposes them.
     *
     * @return string[]
     */
    public function getIds(): array;

    /**
     * Get the manual format version.
     *
     * @return int Major version number
     */
    public function getVersion(): int;

    /**
     * Get manual metadata (version, language, build date, etc).
     *
     * @return array Manual metadata
     */
    public function getMeta(): array;
}
