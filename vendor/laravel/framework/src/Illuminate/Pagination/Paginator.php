<?php

namespace Illuminate\Pagination;

use ArrayAccess;
use Countable;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use IteratorAggregate;
use JsonSerializable;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @extends AbstractPaginator<TKey, TValue>
 *
 * @implements Arrayable<TKey, TValue>
 * @implements ArrayAccess<TKey, TValue>
 * @implements IteratorAggregate<TKey, TValue>
 * @implements PaginatorContract<TKey, TValue>
 */
class Paginator extends AbstractPaginator implements Arrayable, ArrayAccess, Countable, IteratorAggregate, Jsonable, JsonSerializable, PaginatorContract
{
    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    protected $hasMore;

    /**
     * Create a new paginator instance.
     *
     * @param  Collection<TKey, TValue>|Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @param  int  $perPage
     * @param  int|null  $currentPage
     * @param  array  $options  (path, query, fragment, pageName)
     */
    public function __construct($items, $perPage, $currentPage = null, array $options = [])
    {
        $this->options = $options;

        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $this->perPage = $perPage;
        $this->currentPage = $this->setCurrentPage($currentPage);
        $this->path = $this->path !== '/' ? rtrim($this->path, '/') : $this->path;

        $this->setItems($items);
    }

    /**
     * Get the current page for the request.
     *
     * @param  int  $currentPage
     * @return int
     */
    protected function setCurrentPage($currentPage)
    {
        $currentPage = $currentPage ?: static::resolveCurrentPage();

        return $this->isValidPageNumber($currentPage) ? (int) $currentPage : 1;
    }

    /**
     * Set the items for the paginator.
     *
     * @param  Collection<TKey, TValue>|Arrayable<TKey, TValue>|iterable<TKey, TValue>|null  $items
     * @return void
     */
    protected function setItems($items)
    {
        $this->items = $items instanceof Collection ? $items : new Collection($items);

        $this->hasMore = $this->items->count() > $this->perPage;

        $this->items = $this->items->slice(0, $this->perPage);
    }

    /**
     * Get the URL for the next page.
     *
     * @return string|null
     */
    public function nextPageUrl()
    {
        if ($this->hasMorePages()) {
            return $this->url($this->currentPage() + 1);
        }
    }

    /**
     * Render the paginator using the given view.
     *
     * @param  string|null  $view
     * @param  array  $data
     * @return string
     */
    public function links($view = null, $data = [])
    {
        return $this->render($view, $data);
    }

    /**
     * Render the paginator using the given view.
     *
     * @param  string|null  $view
     * @param  array  $data
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function render($view = null, $data = [])
    {
        return static::viewFactory()->make($view ?: static::$defaultSimpleView, array_merge($data, [
            'paginator' => $this,
        ]));
    }

    /**
     * Manually indicate that the paginator does have more pages.
     *
     * @param  bool  $hasMore
     * @return $this
     */
    public function hasMorePagesWhen($hasMore = true)
    {
        $this->hasMore = $hasMore;

        return $this;
    }

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages()
    {
        return $this->hasMore;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'current_page' => $this->currentPage(),
            'current_page_url' => $this->url($this->currentPage()),
            'data' => $this->items->toArray(),
            'first_page_url' => $this->url(1),
            'from' => $this->firstItem(),
            'next_page_url' => $this->nextPageUrl(),
            'path' => $this->path(),
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
        ];
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object to pretty print formatted JSON.
     *
     * @params int $options
     *
     * @return string
     */
    public function toPrettyJson(int $options = 0)
    {
        return $this->toJson(JSON_PRETTY_PRINT | $options);
    }
}
