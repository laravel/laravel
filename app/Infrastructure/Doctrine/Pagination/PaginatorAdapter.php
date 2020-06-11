<?php

namespace App\Infrastructure\Doctrine\Pagination;

use App\Infrastructure\Util\EntityPagination;
use Digbang\Utils\PaginationData;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class PaginatorAdapter
{
    public function make(Query $query, PaginationData $paginationData, bool $fetchJoinCollection = true): EntityPagination
    {
        if ($paginationData->getLimit()) {
            $query->setFirstResult($paginationData->getOffset());
            $query->setMaxResults($paginationData->getLimit());

            $doctrinePaginator = new DoctrinePaginator($query, $fetchJoinCollection);

            $results = $this->getResults($doctrinePaginator);

            return new EntityPagination($results, $doctrinePaginator->count(), $paginationData);
        }

        // No limit
        $results = $query->getResult();
        $count = count($results);
        // if zero results, fake a limit so paging calculations don't explode with division by zero
        $paginationData = $paginationData->clone($count ?: 1, 1);

        return new EntityPagination($results, $count, $paginationData);
    }

    protected function getResults(DoctrinePaginator $doctrinePaginator): array
    {
        return iterator_to_array($doctrinePaginator);
    }
}
