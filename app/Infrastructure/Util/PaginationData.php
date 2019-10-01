<?php

namespace App\Infrastructure\Util;

use Illuminate\Support\Collection;

class PaginationData
{
    /**
     * @var int|null
     */
    private $page;
    /**
     * @var Collection
     */
    private $sorting;
    /**
     * @var int
     */
    private $limit;

    public function __construct(int $limit = null, int $page = 1)
    {
        $this->limit = $limit;
        $this->page = $page;
        $this->sorting = new Collection();
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->getLimit() * ($this->getPage() - 1);
    }

    /**
     * Set the page according to current limit.
     *
     * @param int $offset
     */
    public function setPageFromOffset(int $offset)
    {
        $this->page = floor($offset / $this->limit) + 1;
    }

    /**
     * @return Collection
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    public function addSort($orderBy, $orderSense = 'ASC')
    {
        $this->sorting->put($orderBy, $orderSense);
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    public function hasSortField(string $field): bool
    {
        return $this->sorting->has($field);
    }

    public function clone(int $limit = null, int $page = 1)
    {
        $new = new static($limit, $page);
        $new->sorting = $this->sorting;

        return $new;
    }
}
