<?php

namespace Illuminate\Validation\Rules;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use function Illuminate\Support\enum_value;

trait DatabaseRule
{
    /**
     * The table to run the query against.
     *
     * @var string
     */
    protected $table;

    /**
     * The column to check on.
     *
     * @var string
     */
    protected $column;

    /**
     * The extra where clauses for the query.
     *
     * @var array
     */
    protected $wheres = [];

    /**
     * The array of custom query callbacks.
     *
     * @var array
     */
    protected $using = [];

    /**
     * Create a new rule instance.
     *
     * @param  string  $table
     * @param  string  $column
     */
    public function __construct($table, $column = 'NULL')
    {
        $this->column = $column;

        $this->table = $this->resolveTableName($table);
    }

    /**
     * Resolves the name of the table from the given string.
     *
     * @param  string  $table
     * @return string
     */
    public function resolveTableName($table)
    {
        if (! str_contains($table, '\\') || ! class_exists($table)) {
            return $table;
        }

        if (is_subclass_of($table, Model::class)) {
            $model = new $table;

            if (str_contains($model->getTable(), '.')) {
                return $table;
            }

            return implode('.', array_map(function (string $part) {
                return trim($part, '.');
            }, array_filter([$model->getConnectionName(), $model->getTable()])));
        }

        return $table;
    }

    /**
     * Set a "where" constraint on the query.
     *
     * @param  \Closure|string  $column
     * @param  \Illuminate\Contracts\Support\Arrayable|\UnitEnum|\Closure|array|string|int|bool|null  $value
     * @return $this
     */
    public function where($column, $value = null)
    {
        if ($value instanceof Arrayable || is_array($value)) {
            return $this->whereIn($column, $value);
        }

        if ($column instanceof Closure) {
            return $this->using($column);
        }

        if (is_null($value)) {
            return $this->whereNull($column);
        }

        $value = enum_value($value);

        $this->wheres[] = compact('column', 'value');

        return $this;
    }

    /**
     * Set a "where not" constraint on the query.
     *
     * @param  string  $column
     * @param  \Illuminate\Contracts\Support\Arrayable|\UnitEnum|array|string|int  $value
     * @return $this
     */
    public function whereNot($column, $value)
    {
        if ($value instanceof Arrayable || is_array($value)) {
            return $this->whereNotIn($column, $value);
        }

        $value = enum_value($value);

        return $this->where($column, '!'.$value);
    }

    /**
     * Set a "where null" constraint on the query.
     *
     * @param  string  $column
     * @return $this
     */
    public function whereNull($column)
    {
        return $this->where($column, 'NULL');
    }

    /**
     * Set a "where not null" constraint on the query.
     *
     * @param  string  $column
     * @return $this
     */
    public function whereNotNull($column)
    {
        return $this->where($column, 'NOT_NULL');
    }

    /**
     * Set a "where in" constraint on the query.
     *
     * @param  string  $column
     * @param  \Illuminate\Contracts\Support\Arrayable|\BackedEnum|array  $values
     * @return $this
     */
    public function whereIn($column, $values)
    {
        return $this->where(function ($query) use ($column, $values) {
            $query->whereIn($column, $values);
        });
    }

    /**
     * Set a "where not in" constraint on the query.
     *
     * @param  string  $column
     * @param  \Illuminate\Contracts\Support\Arrayable|\BackedEnum|array  $values
     * @return $this
     */
    public function whereNotIn($column, $values)
    {
        return $this->where(function ($query) use ($column, $values) {
            $query->whereNotIn($column, $values);
        });
    }

    /**
     * Ignore soft deleted models during the existence check.
     *
     * @param  string  $deletedAtColumn
     * @return $this
     */
    public function withoutTrashed($deletedAtColumn = 'deleted_at')
    {
        $this->whereNull($deletedAtColumn);

        return $this;
    }

    /**
     * Only include soft deleted models during the existence check.
     *
     * @param  string  $deletedAtColumn
     * @return $this
     */
    public function onlyTrashed($deletedAtColumn = 'deleted_at')
    {
        $this->whereNotNull($deletedAtColumn);

        return $this;
    }

    /**
     * Register a custom query callback.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function using(Closure $callback)
    {
        $this->using[] = $callback;

        return $this;
    }

    /**
     * Get the custom query callbacks for the rule.
     *
     * @return array
     */
    public function queryCallbacks()
    {
        return $this->using;
    }

    /**
     * Format the where clauses.
     *
     * @return string
     */
    protected function formatWheres()
    {
        return (new Collection($this->wheres))
            ->map(fn ($where) => $where['column'].','.'"'.str_replace('"', '""', $where['value']).'"')
            ->implode(',');
    }
}
