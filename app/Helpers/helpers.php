<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

if (! function_exists('paginate_collection')) {
    /**
     * @param  $items
     * @param  int  $perPage
     * @param  null  $page
     * @param  array  $options
     * @return LengthAwarePaginator
     */
    function paginate_collection($items, int $perPage = 15, $page = null, array $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
