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

use Psy\Exception\InvalidManualException;

/**
 * V3 manual format loader.
 *
 * Loads structured manual documentation from a single pre-built PHP file.
 *
 * The PHP file returns an object with a get($id) method that handles the data loading internally.
 */
class V3Manual implements ManualInterface
{
    /** @var object */
    private $data;

    /** @var array<string, object> */
    private static $cache = [];

    /**
     * Constructor.
     *
     * @param string $filePath Path to the PHP manual file
     *
     * @throws InvalidManualException if file doesn't return a valid manual data object
     */
    public function __construct(string $filePath)
    {
        // Avoid redefining __COMPILER_HALT_OFFSET__
        // TODO: Remove cache after dropping support for PHP 8.2.
        if (isset(self::$cache[$filePath])) {
            $data = self::$cache[$filePath];
        } else {
            // Suppress output from invalid/corrupted manual files
            \ob_start();
            try {
                /** @var mixed $data */
                $data = require $filePath;
            } finally {
                \ob_end_clean();
            }
            // @phan-suppress-next-line PhanPossiblyUndeclaredVariable $data is always set above
            self::$cache[$filePath] = $data;
        }

        // Validate that the file returned an object with the expected interface
        // @phan-suppress-next-line PhanPossiblyUndeclaredVariable $data is always set above
        if (!\is_object($data)) {
            throw new InvalidManualException(\sprintf('Manual file "%s" must return an object, got %s', $filePath, \gettype($data)), $filePath);
        }

        $requiredMethods = ['get', 'getMeta'];
        foreach ($requiredMethods as $method) {
            // @phan-suppress-next-line PhanPossiblyUndeclaredVariable $data is always set above
            if (!\method_exists($data, $method)) {
                throw new InvalidManualException(\sprintf('Manual data object must have a %s() method', $method), $filePath);
            }
        }

        // Verify the manual format version is v3.x
        $meta = $data->getMeta();
        if (!isset($meta['version']) || !\preg_match('/^3\./', (string) $meta['version'])) {
            $version = $meta['version'] ?? 'unknown';
            throw new InvalidManualException(\sprintf('Manual file "%s" must be v3.x format, got version %s', $filePath, $version), $filePath);
        }

        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id)
    {
        return $this->data->get($id);
    }

    /**
     * Get all available manual IDs when supported by the loaded manual file.
     *
     * @return string[]
     */
    public function getIds(): array
    {
        if (!\method_exists($this->data, 'getIds')) {
            return [];
        }

        return $this->data->getIds();
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): int
    {
        return 3;
    }

    /**
     * Get manual metadata (version, language, build date, etc).
     *
     * @return array
     */
    public function getMeta(): array
    {
        return $this->data->getMeta();
    }
}
