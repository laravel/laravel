<?php

namespace Illuminate\Database\Concerns;

use Illuminate\Support\Collection;

trait ExplainsQueries
{
    /**
     * Explains the query.
     *
     * @return \Illuminate\Support\Collection
     */
    public function explain()
    {
        $sql = $this->toSql();

        $bindings = $this->getBindings();

        $explanation = $this->getConnection()->select('EXPLAIN '.$sql, $bindings);

        return new Collection($explanation);
    }
}
