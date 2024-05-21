<?php

declare(strict_types=1);

namespace Lightit\Users\Domain\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Lightit\Users\Domain\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class ListUserAction
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function execute(): LengthAwarePaginator
    {
        return QueryBuilder::for(User::class)
            ->allowedFilters(['email'])
            ->allowedSorts('email')
            ->orderBy('id', 'desc')
            ->paginate();
    }
}
