<?php

namespace Illuminate\Database\Connectors;

use PDO;

class MariaDbConnector extends MySqlConnector
{
    /**
     * Get the sql_mode value.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return string|null
     */
    protected function getSqlMode(PDO $connection, array $config)
    {
        if (isset($config['modes'])) {
            return implode(',', $config['modes']);
        }

        if (! isset($config['strict'])) {
            return null;
        }

        if (! $config['strict']) {
            return 'NO_ENGINE_SUBSTITUTION';
        }

        return 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
    }
}
