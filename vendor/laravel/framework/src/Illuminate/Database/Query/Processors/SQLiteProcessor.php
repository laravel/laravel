<?php

namespace Illuminate\Database\Query\Processors;

class SQLiteProcessor extends Processor
{
    /** @inheritDoc */
    public function processColumns($results, $sql = '')
    {
        $hasPrimaryKey = array_sum(array_column($results, 'primary')) === 1;

        return array_map(function ($result) use ($hasPrimaryKey, $sql) {
            $result = (object) $result;

            $type = strtolower($result->type);

            $safeName = preg_quote($result->name, '/');

            $collation = preg_match(
                '/\b'.$safeName.'\b[^,(]+(?:\([^()]+\)[^,]*)?(?:(?:default|check|as)\s*(?:\(.*?\))?[^,]*)*collate\s+["\'`]?(\w+)/i',
                $sql,
                $matches
            ) === 1 ? strtolower($matches[1]) : null;

            $isGenerated = in_array($result->extra, [2, 3]);

            $expression = $isGenerated && preg_match(
                '/\b'.$safeName.'\b[^,]+\s+as\s+\(((?:[^()]+|\((?:[^()]+|\([^()]*\))*\))*)\)/i',
                $sql,
                $matches
            ) === 1 ? $matches[1] : null;

            return [
                'name' => $result->name,
                'type_name' => strtok($type, '(') ?: '',
                'type' => $type,
                'collation' => $collation,
                'nullable' => (bool) $result->nullable,
                'default' => $result->default,
                'auto_increment' => $hasPrimaryKey && $result->primary && $type === 'integer',
                'comment' => null,
                'generation' => $isGenerated ? [
                    'type' => match ((int) $result->extra) {
                        3 => 'stored',
                        2 => 'virtual',
                        default => null,
                    },
                    'expression' => $expression,
                ] : null,
            ];
        }, $results);
    }

    /** @inheritDoc */
    public function processIndexes($results)
    {
        $primaryCount = 0;

        $indexes = array_map(function ($result) use (&$primaryCount) {
            $result = (object) $result;

            if ($isPrimary = (bool) $result->primary) {
                $primaryCount += 1;
            }

            return [
                'name' => strtolower($result->name),
                'columns' => $result->columns ? explode(',', $result->columns) : [],
                'type' => null,
                'unique' => (bool) $result->unique,
                'primary' => $isPrimary,
            ];
        }, $results);

        if ($primaryCount > 1) {
            $indexes = array_filter($indexes, fn ($index) => $index['name'] !== 'primary');
        }

        return $indexes;
    }

    /** @inheritDoc */
    public function processForeignKeys($results)
    {
        return array_map(function ($result) {
            $result = (object) $result;

            return [
                'name' => null,
                'columns' => explode(',', $result->columns),
                'foreign_schema' => $result->foreign_schema,
                'foreign_table' => $result->foreign_table,
                'foreign_columns' => explode(',', $result->foreign_columns),
                'on_update' => strtolower($result->on_update),
                'on_delete' => strtolower($result->on_delete),
            ];
        }, $results);
    }
}
