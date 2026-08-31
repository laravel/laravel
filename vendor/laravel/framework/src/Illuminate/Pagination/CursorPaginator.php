<?php

namespace Illuminate\Pagination;

use ArrayAccess;
use Countable;
use Illuminate\Contracts\Pagination\CursorPaginator as PaginatorContract;
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
 * @extends AbstractCursorPaginator<TKey, TValue>
 *
 * @implements Arrayable<TKey, TValue>
 * @implements ArrayAccess<TKey, TValue>
 * @implements IteratorAggregate<TKey, TValue>
 * @implements PaginatorContract<TKey, TValue>
 */
class CursorPaginator extends AbstractCursorPaginator implements Arrayable, ArrayAccess, Countable, IteratorAggregate, Jsonable, JsonSerializable, PaginatorContract
{
    /**
     * Indicates whether there are more items in the data source.
     *
     * @return bool
     */
    protected $hasMore;

    /**
     * Create a new paginator instance.
     *
     * @param  Collection<TKey, TValue>|Arrayable<TKey, TValue>|iterable<TKey, TValue>|null  $items
     * @param  int  $perPage
     * @param  \Illuminate\Pagination\Cursor|null  $cursor
     * @param  array  $options  (path, query, fragment, pageName)
     */
    public function __construct($items, $perPage, $cursor = null, array $options = [])
    {
        $this->options = $options;

        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $this->perPage = (int) $perPage;
        $this->cursor = $cursor;
        $this->path = $this->path !== '/' ? rtrim($this->path, '/') : $this->path;

        $this->setItems($items);
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

        if (! is_null($this->cursor) && $this->cursor->pointsToPreviousItems()) {
            $this->items = $this->items->reverse()->values();
        }
    }

    /**
     * Render the paginator using the given view.
     *
     * @param  string|null  $view
     * @param  array  $data
     * @return \Illuminate\Contracts\Support\Htmlable
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
        return static::viewFactory()->make($view ?: Paginator::$defaultSimpleView, array_merge($data, [
            'paginator' => $this,
        ]));
    }

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages()
    {
        return (is_null($this->cursor) && $this->hasMore) ||
            (! is_null($this->cursor) && $this->cursor->pointsToNextItems() && $this->hasMore) ||
            (! is_null($this->cursor) && $this->cursor->pointsToPreviousItems());
    }

    /**
     * Determine if there are enough items to split into multiple pages.
     *
     * @return bool
     */
    public function hasPages()
    {
        return ! $this->onFirstPage() || $this->hasMorePages();
    }

    /**
     * Determine if the paginator is on the first page.
     *
     * @return bool
     */
    public function onFirstPage()
    {
        return is_null($this->cursor) || ($this->cursor->pointsToPreviousItems() && ! $this->hasMore);
    }

    /**
     * Determine if the paginator is on the last page.
     *
     * @return bool
     */
    public function onLastPage()
    {
        return ! $this->hasMorePages();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'data' => $this->items->toArray(),
            'path' => $this->path(),
            'per_page' => $this->perPage(),
            'next_cursor' => $this->nextCursor()?->encode(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_cursor' => $this->previousCursor()?->encode(),
            'prev_page_url' => $this->previousPageUrl(),
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
