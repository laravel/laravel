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
 * V2 manual format loader.
 *
 * Loads pre-formatted manual documentation from SQLite databases.
 */
class V2Manual implements ManualInterface
{
    private \PDO $db;
    /** @var string[]|null */
    private ?array $ids = null;

    /**
     * Constructor.
     *
     * @param \PDO $db SQLite database connection
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id)
    {
        $result = $this->db->query(\sprintf('SELECT doc FROM php_manual WHERE id = %s', $this->db->quote($id)));
        if ($result !== false) {
            return $result->fetchColumn(0) ?: null;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getIds(): array
    {
        if ($this->ids !== null) {
            return $this->ids;
        }

        $result = $this->db->query('SELECT id FROM php_manual ORDER BY id');
        if ($result === false) {
            return $this->ids = [];
        }

        return $this->ids = $result->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): int
    {
        return 2;
    }

    /**
     * Get manual metadata (version, language, build date, etc).
     *
     * Reads metadata from the meta table in the SQLite database.
     *
     * @return array
     */
    public function getMeta(): array
    {
        $meta = ['format' => 'sqlite'];

        $result = $this->db->query('SELECT id, value FROM meta');
        if ($result !== false) {
            while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
                $key = $row['id'];
                $value = $row['value'];

                // Convert numeric strings to integers
                if (\in_array($key, ['git_timestamp', 'built_at'], true)) {
                    $value = (int) $value;
                }

                $meta[$key] = $value;
            }
        }

        return $meta;
    }
}
