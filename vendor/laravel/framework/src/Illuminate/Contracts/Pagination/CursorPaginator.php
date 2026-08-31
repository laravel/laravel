<?php

namespace Illuminate\Contracts\Pagination;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @method $this through(callable(TValue): mixed $callback)
 */
interface CursorPaginator
{
    /**
     * Get the URL for a given cursor.
     *
     * @param  \Illuminate\Pagination\Cursor|null  $cursor
     * @return string
     */
    public function url($cursor);

    /**
     * Add a set of query string values to the paginator.
     *
     * @param  array|string|null  $key
     * @param  string|null  $value
     * @return $this
     */
    public function appends($key, $value = null);

    /**
     * Get / set the URL fragment to be appended to URLs.
     *
     * @param  string|null  $fragment
     * @return $this|string|null
     */
    public function fragment($fragment = null);

    /**
     * Add all current query string values to the paginator.
     *
     * @return $this
     */
    public function withQueryString();

    /**
     * Get the URL for the previous page, or null.
     *
     * @return string|null
     */
    public function previousPageUrl();

    /**
     * The URL for the next page, or null.
     *
     * @return string|null
     */
    public function nextPageUrl();

    /**
     * Get all of the items being paginated.
     *
     * @return array<TKey, TValue>
     */
    public function items();

    /**
     * Get the "cursor" of the previous set of items.
     *
     * @return \Illuminate\Pagination\Cursor|null
     */
    public function previousCursor();

    /**
     * Get the "cursor" of the next set of items.
     *
     * @return \Illuminate\Pagination\Cursor|null
     */
    public function nextCursor();

    /**
     * Determine how many items are being shown per page.
     *
     * @return int
     */
    public function perPage();

    /**
     * Get the current cursor being paginated.
     *
     * @return \Illuminate\Pagination\Cursor|null
     */
    public function cursor();

    /**
     * Determine if there are enough items to split into multiple pages.
     *
     * @return bool
     */
    public function hasPages();

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages();

    /**
     * Get the base path for paginator generated URLs.
     *
     * @return string|null
     */
    public function path();

    /**
     * Determine if the list of items is empty or not.
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Determine if the list of items is not empty.
     *
     * @return bool
     */
    public function isNotEmpty();

    /**
     * Render the paginator using a given view.
     *
     * @param  string|null  $view
     * @param  array  $data
     * @return string
     */
    public function render($view = null, $data = []);
}
