<?php

namespace App\Infrastructure\Doctrine\Pagination;

use App\Infrastructure\Util\EntityPagination;
use Digbang\Utils\PaginationData;
use Doctrine\ORM\Query;

trait Paginator
{
    public function paginate(Query $query, PaginationData $paginationData, bool $fetchJoinCollection = true): EntityPagination
    {
        return (new PaginatorAdapter())->make(
            $query,
            $paginationData,
            $fetchJoinCollection
        );
    }
}
