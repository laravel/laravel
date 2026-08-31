<?php

namespace Illuminate\Database\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Arr;

trait BuildsWhereDateClauses
{
    /**
     * Add a where clause to determine if a "date" column is in the past to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function wherePast($columns)
    {
        return $this->wherePastOrFuture($columns, '<', 'and');
    }

    /**
     * Add a where clause to determine if a "date" column is in the past or now to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function whereNowOrPast($columns)
    {
        return $this->wherePastOrFuture($columns, '<=', 'and');
    }

    /**
     * Add an "or where" clause to determine if a "date" column is in the past to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function orWherePast($columns)
    {
        return $this->wherePastOrFuture($columns, '<', 'or');
    }

    /**
     * Add a where clause to determine if a "date" column is in the past or now to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function orWhereNowOrPast($columns)
    {
        return $this->wherePastOrFuture($columns, '<=', 'or');
    }

    /**
     * Add a where clause to determine if a "date" column is in the future to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function whereFuture($columns)
    {
        return $this->wherePastOrFuture($columns, '>', 'and');
    }

    /**
     * Add a where clause to determine if a "date" column is in the future or now to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function whereNowOrFuture($columns)
    {
        return $this->wherePastOrFuture($columns, '>=', 'and');
    }

    /**
     * Add an "or where" clause to determine if a "date" column is in the future to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function orWhereFuture($columns)
    {
        return $this->wherePastOrFuture($columns, '>', 'or');
    }

    /**
     * Add an "or where" clause to determine if a "date" column is in the future or now to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function orWhereNowOrFuture($columns)
    {
        return $this->wherePastOrFuture($columns, '>=', 'or');
    }

    /**
     * Add an "where" clause to determine if a "date" column is in the past or future.
     *
     * @param  array|string  $columns
     * @param  string  $operator
     * @param  string  $boolean
     * @return $this
     */
    protected function wherePastOrFuture($columns, $operator, $boolean)
    {
        $type = 'Basic';
        $value = Carbon::now();

        foreach (Arr::wrap($columns) as $column) {
            $this->wheres[] = compact('type', 'column', 'boolean', 'operator', 'value');

            $this->addBinding($value);
        }

        return $this;
    }

    /**
     * Add a "where date" clause to determine if a "date" column is today to the query.
     *
     * @param  array|string  $columns
     * @param  string  $boolean
     * @return $this
     */
    public function whereToday($columns, $boolean = 'and')
    {
        return $this->whereTodayBeforeOrAfter($columns, '=', $boolean);
    }

    /**
     * Add a "where date" clause to determine if a "date" column is before today.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function whereBeforeToday($columns)
    {
        return $this->whereTodayBeforeOrAfter($columns, '<', 'and');
    }

    /**
     * Add a "where date" clause to determine if a "date" column is today or before to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function whereTodayOrBefore($columns)
    {
        return $this->whereTodayBeforeOrAfter($columns, '<=', 'and');
    }

    /**
     * Add a "where date" clause to determine if a "date" column is after today.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function whereAfterToday($columns)
    {
        return $this->whereTodayBeforeOrAfter($columns, '>', 'and');
    }

    /**
     * Add a "where date" clause to determine if a "date" column is today or after to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function whereTodayOrAfter($columns)
    {
        return $this->whereTodayBeforeOrAfter($columns, '>=', 'and');
    }

    /**
     * Add an "or where date" clause to determine if a "date" column is today to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function orWhereToday($columns)
    {
        return $this->whereToday($columns, 'or');
    }

    /**
     * Add an "or where date" clause to determine if a "date" column is before today.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function orWhereBeforeToday($columns)
    {
        return $this->whereTodayBeforeOrAfter($columns, '<', 'or');
    }

    /**
     * Add an "or where date" clause to determine if a "date" column is today or before to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function orWhereTodayOrBefore($columns)
    {
        return $this->whereTodayBeforeOrAfter($columns, '<=', 'or');
    }

    /**
     * Add an "or where date" clause to determine if a "date" column is after today.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function orWhereAfterToday($columns)
    {
        return $this->whereTodayBeforeOrAfter($columns, '>', 'or');
    }

    /**
     * Add an "or where date" clause to determine if a "date" column is today or after to the query.
     *
     * @param  array|string  $columns
     * @return $this
     */
    public function orWhereTodayOrAfter($columns)
    {
        return $this->whereTodayBeforeOrAfter($columns, '>=', 'or');
    }

    /**
     * Add a "where date" clause to determine if a "date" column is today or after to the query.
     *
     * @param  array|string  $columns
     * @param  string  $operator
     * @param  string  $boolean
     * @return $this
     */
    protected function whereTodayBeforeOrAfter($columns, $operator, $boolean)
    {
        $value = Carbon::today()->format('Y-m-d');

        foreach (Arr::wrap($columns) as $column) {
            $this->addDateBasedWhere('Date', $column, $operator, $value, $boolean);
        }

        return $this;
    }
}
