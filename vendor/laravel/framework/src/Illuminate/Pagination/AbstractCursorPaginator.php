<?php

namespace Illuminate\Pagination;

use ArrayAccess;
use Closure;
use Exception;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Tappable;
use Illuminate\Support\Traits\TransformsToResourceCollection;
use Stringable;
use Traversable;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @mixin \Illuminate\Support\Collection<TKey, TValue>
 */
abstract class AbstractCursorPaginator implements Htmlable, Stringable
{
    use ForwardsCalls, Tappable, TransformsToResourceCollection;

    /**
     * All of the items being paginated.
     *
     * @var \Illuminate\Support\Collection<TKey, TValue>
     */
    protected $items;

    /**
     * The number of items to be shown per page.
     *
     * @var int
     */
    protected $perPage;

    /**
     * The base path to assign to all URLs.
     *
     * @var string
     */
    protected $path = '/';

    /**
     * The query parameters to add to all URLs.
     *
     * @var array
     */
    protected $query = [];

    /**
     * The URL fragment to add to all URLs.
     *
     * @var string|null
     */
    protected $fragment;

    /**
     * The cursor string variable used to store the page.
     *
     * @var string
     */
    protected $cursorName = 'cursor';

    /**
     * The current cursor.
     *
     * @var \Illuminate\Pagination\Cursor|null
     */
    protected $cursor;

    /**
     * The paginator parameters for the cursor.
     *
     * @var array
     */
    protected $parameters;

    /**
     * The paginator options.
     *
     * @var array
     */
    protected $options;

    /**
     * The current cursor resolver callback.
     *
     * @var \Closure
     */
    protected static $currentCursorResolver;

    /**
     * Get the URL for a given cursor.
     *
     * @param  \Illuminate\Pagination\Cursor|null  $cursor
     * @return string
     */
    public function url($cursor)
    {
        // If we have any extra query string key / value pairs that need to be added
        // onto the URL, we will put them in query string form and then attach it
        // to the URL. This allows for extra information like sortings storage.
        $parameters = is_null($cursor) ? [] : [$this->cursorName => $cursor->encode()];

        if (count($this->query) > 0) {
            $parameters = array_merge($this->query, $parameters);
        }

        return $this->path()
            .(str_contains($this->path(), '?') ? '&' : '?')
            .Arr::query($parameters)
            .$this->buildFragment();
    }

    /**
     * Get the URL for the previous page.
     *
     * @return string|null
     */
    public function previousPageUrl()
    {
        if (is_null($previousCursor = $this->previousCursor())) {
            return null;
        }

        return $this->url($previousCursor);
    }

    /**
     * The URL for the next page, or null.
     *
     * @return string|null
     */
    public function nextPageUrl()
    {
        if (is_null($nextCursor = $this->nextCursor())) {
            return null;
        }

        return $this->url($nextCursor);
    }

    /**
     * Get the "cursor" that points to the previous set of items.
     *
     * @return \Illuminate\Pagination\Cursor|null
     */
    public function previousCursor()
    {
        if (is_null($this->cursor) ||
            ($this->cursor->pointsToPreviousItems() && ! $this->hasMore)) {
            return null;
        }

        if ($this->items->isEmpty()) {
            return null;
        }

        return $this->getCursorForItem($this->items->first(), false);
    }

    /**
     * Get the "cursor" that points to the next set of items.
     *
     * @return \Illuminate\Pagination\Cursor|null
     */
    public function nextCursor()
    {
        if ((is_null($this->cursor) && ! $this->hasMore) ||
            (! is_null($this->cursor) && $this->cursor->pointsToNextItems() && ! $this->hasMore)) {
            return null;
        }

        if ($this->items->isEmpty()) {
            return null;
        }

        return $this->getCursorForItem($this->items->last(), true);
    }

    /**
     * Get a cursor instance for the given item.
     *
     * @param  \ArrayAccess|\stdClass  $item
     * @param  bool  $isNext
     * @return \Illuminate\Pagination\Cursor
     */
    public function getCursorForItem($item, $isNext = true)
    {
        return new Cursor($this->getParametersForItem($item), $isNext);
    }

    /**
     * Get the cursor parameters for a given object.
     *
     * @param  \ArrayAccess|\stdClass  $item
     * @return array
     *
     * @throws \Exception
     */
    public function getParametersForItem($item)
    {
        return (new Collection($this->parameters))
            ->filter()
            ->flip()
            ->map(function ($_, $parameterName) use ($item) {
                if ($item instanceof JsonResource) {
                    $item = $item->resource;
                }

                if ($item instanceof Model &&
                    ! is_null($parameter = $this->getPivotParameterForItem($item, $parameterName))) {
                    return $parameter;
                } elseif ($item instanceof ArrayAccess || is_array($item)) {
                    return $this->ensureParameterIsPrimitive(
                        $item[$parameterName] ?? $item[Str::afterLast($parameterName, '.')]
                    );
                } elseif (is_object($item)) {
                    return $this->ensureParameterIsPrimitive(
                        $item->{$parameterName} ?? $item->{Str::afterLast($parameterName, '.')}
                    );
                }

                throw new Exception('Only arrays and objects are supported when cursor paginating items.');
            })->toArray();
    }

    /**
     * Get the cursor parameter value from a pivot model if applicable.
     *
     * @param  \ArrayAccess|\stdClass  $item
     * @param  string  $parameterName
     * @return string|null
     */
    protected function getPivotParameterForItem($item, $parameterName)
    {
        $table = Str::beforeLast($parameterName, '.');

        foreach ($item->getRelations() as $relation) {
            if ($relation instanceof Pivot && $relation->getTable() === $table) {
                return $this->ensureParameterIsPrimitive(
                    $relation->getAttribute(Str::afterLast($parameterName, '.'))
                );
            }
        }
    }

    /**
     * Ensure the parameter is a primitive type.
     *
     * This can resolve issues that arise the developer uses a value object for an attribute.
     *
     * @param  mixed  $parameter
     * @return mixed
     */
    protected function ensureParameterIsPrimitive($parameter)
    {
        return is_object($parameter) && method_exists($parameter, '__toString')
            ? (string) $parameter
            : $parameter;
    }

    /**
     * Get / set the URL fragment to be appended to URLs.
     *
     * @param  string|null  $fragment
     * @return $this|string|null
     */
    public function fragment($fragment = null)
    {
        if (is_null($fragment)) {
            return $this->fragment;
        }

        $this->fragment = $fragment;

        return $this;
    }

    /**
     * Add a set of query string values to the paginator.
     *
     * @param  array|string|null  $key
     * @param  string|null  $value
     * @return $this
     */
    public function appends($key, $value = null)
    {
        if (is_null($key)) {
            return $this;
        }

        if (is_array($key)) {
            return $this->appendArray($key);
        }

        return $this->addQuery($key, $value);
    }

    /**
     * Add an array of query string values.
     *
     * @param  array  $keys
     * @return $this
     */
    protected function appendArray(array $keys)
    {
        foreach ($keys as $key => $value) {
            $this->addQuery($key, $value);
        }

        return $this;
    }

    /**
     * Add all current query string values to the paginator.
     *
     * @return $this
     */
    public function withQueryString()
    {
        if (! is_null($query = Paginator::resolveQueryString())) {
            return $this->appends($query);
        }

        return $this;
    }

    /**
     * Add a query string value to the paginator.
     *
     * @param  string  $key
     * @param  string  $value
     * @return $this
     */
    protected function addQuery($key, $value)
    {
        if ($key !== $this->cursorName) {
            $this->query[$key] = $value;
        }

        return $this;
    }

    /**
     * Build the full fragment portion of a URL.
     *
     * @return string
     */
    protected function buildFragment()
    {
        return $this->fragment ? '#'.$this->fragment : '';
    }

    /**
     * Load a set of relationships onto the mixed relationship collection.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @return $this
     */
    public function loadMorph($relation, $relations)
    {
        $this->getCollection()->loadMorph($relation, $relations);

        return $this;
    }

    /**
     * Load a set of relationship counts onto the mixed relationship collection.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @return $this
     */
    public function loadMorphCount($relation, $relations)
    {
        $this->getCollection()->loadMorphCount($relation, $relations);

        return $this;
    }

    /**
     * Get the slice of items being paginated.
     *
     * @return array<TKey, TValue>
     */
    public function items()
    {
        return $this->items->all();
    }

    /**
     * Transform each item in the slice of items using a callback.
     *
     * @template TThroughValue
     *
     * @param  callable(TValue, TKey): TThroughValue  $callback
     * @return $this
     *
     * @phpstan-this-out static<TKey, TThroughValue>
     */
    public function through(callable $callback)
    {
        $this->items->transform($callback);

        return $this;
    }

    /**
     * Get the number of items shown per page.
     *
     * @return int
     */
    public function perPage()
    {
        return $this->perPage;
    }

    /**
     * Get the current cursor being paginated.
     *
     * @return \Illuminate\Pagination\Cursor|null
     */
    public function cursor()
    {
        return $this->cursor;
    }

    /**
     * Get the query string variable used to store the cursor.
     *
     * @return string
     */
    public function getCursorName()
    {
        return $this->cursorName;
    }

    /**
     * Set the query string variable used to store the cursor.
     *
     * @param  string  $name
     * @return $this
     */
    public function setCursorName($name)
    {
        $this->cursorName = $name;

        return $this;
    }

    /**
     * Set the base path to assign to all URLs.
     *
     * @param  string  $path
     * @return $this
     */
    public function withPath($path)
    {
        return $this->setPath($path);
    }

    /**
     * Set the base path to assign to all URLs.
     *
     * @param  string  $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the base path for paginator generated URLs.
     *
     * @return string|null
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Resolve the current cursor or return the default value.
     *
     * @param  string  $cursorName
     * @param  \Illuminate\Pagination\Cursor|null  $default
     * @return \Illuminate\Pagination\Cursor|null
     */
    public static function resolveCurrentCursor($cursorName = 'cursor', $default = null)
    {
        if (isset(static::$currentCursorResolver)) {
            return call_user_func(static::$currentCursorResolver, $cursorName);
        }

        return $default;
    }

    /**
     * Set the current cursor resolver callback.
     *
     * @param  \Closure  $resolver
     * @return void
     */
    public static function currentCursorResolver(Closure $resolver)
    {
        static::$currentCursorResolver = $resolver;
    }

    /**
     * Get an instance of the view factory from the resolver.
     *
     * @return \Illuminate\Contracts\View\Factory
     */
    public static function viewFactory()
    {
        return Paginator::viewFactory();
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        return $this->items->getIterator();
    }

    /**
     * Determine if the list of items is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->items->isEmpty();
    }

    /**
     * Determine if the list of items is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return $this->items->isNotEmpty();
    }

    /**
     * Get the number of items for the current page.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Get the paginator's underlying collection.
     *
     * @return \Illuminate\Support\Collection<TKey, TValue>
     */
    public function getCollection()
    {
        return $this->items;
    }

    /**
     * Set the paginator's underlying collection.
     *
     * @template TSetKey of array-key
     * @template TSetValue
     *
     * @param  \Illuminate\Support\Collection<TSetKey, TSetValue>  $collection
     * @return $this
     *
     * @phpstan-this-out static<TSetKey, TSetValue>
     */
    public function setCollection(Collection $collection)
    {
        $this->items = $collection;

        return $this;
    }

    /**
     * Get the paginator options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Determine if the given item exists.
     *
     * @param  TKey  $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->items->has($key);
    }

    /**
     * Get the item at the given offset.
     *
     * @param  TKey  $key
     * @return TValue|null
     */
    public function offsetGet($key): mixed
    {
        return $this->items->get($key);
    }

    /**
     * Set the item at the given offset.
     *
     * @param  TKey|null  $key
     * @param  TValue  $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        $this->items->put($key, $value);
    }

    /**
     * Unset the item at the given key.
     *
     * @param  TKey  $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        $this->items->forget($key);
    }

    /**
     * Render the contents of the paginator to HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return (string) $this->render();
    }

    /**
     * Make dynamic calls into the collection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->getCollection(), $method, $parameters);
    }

    /**
     * Render the contents of the paginator when casting to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->render();
    }
}
