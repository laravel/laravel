<?php

namespace Illuminate\Pagination;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use UnexpectedValueException;

/** @implements Arrayable<array-key, mixed> */
class Cursor implements Arrayable
{
    /**
     * The parameters associated with the cursor.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Determine whether the cursor points to the next or previous set of items.
     *
     * @var bool
     */
    protected $pointsToNextItems;

    /**
     * Create a new cursor instance.
     *
     * @param  array  $parameters
     * @param  bool  $pointsToNextItems
     */
    public function __construct(array $parameters, $pointsToNextItems = true)
    {
        $this->parameters = $parameters;
        $this->pointsToNextItems = $pointsToNextItems;
    }

    /**
     * Get the given parameter from the cursor.
     *
     * @param  string  $parameterName
     * @return string|null
     *
     * @throws \UnexpectedValueException
     */
    public function parameter(string $parameterName)
    {
        if (! array_key_exists($parameterName, $this->parameters)) {
            throw new UnexpectedValueException("Unable to find parameter [{$parameterName}] in pagination item.");
        }

        return $this->parameters[$parameterName];
    }

    /**
     * Get the given parameters from the cursor.
     *
     * @param  array  $parameterNames
     * @return array
     */
    public function parameters(array $parameterNames)
    {
        return (new Collection($parameterNames))
            ->map(fn ($parameterName) => $this->parameter($parameterName))
            ->toArray();
    }

    /**
     * Determine whether the cursor points to the next set of items.
     *
     * @return bool
     */
    public function pointsToNextItems()
    {
        return $this->pointsToNextItems;
    }

    /**
     * Determine whether the cursor points to the previous set of items.
     *
     * @return bool
     */
    public function pointsToPreviousItems()
    {
        return ! $this->pointsToNextItems;
    }

    /**
     * Get the array representation of the cursor.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->parameters, [
            '_pointsToNextItems' => $this->pointsToNextItems,
        ]);
    }

    /**
     * Get the encoded string representation of the cursor to construct a URL.
     *
     * @return string
     */
    public function encode()
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($this->toArray())));
    }

    /**
     * Get a cursor instance from the encoded string representation.
     *
     * @param  string|null  $encodedString
     * @return static|null
     */
    public static function fromEncoded($encodedString)
    {
        if (! is_string($encodedString)) {
            return null;
        }

        $parameters = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $encodedString)), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        $pointsToNextItems = $parameters['_pointsToNextItems'];

        unset($parameters['_pointsToNextItems']);

        return new static($parameters, $pointsToNextItems);
    }
}
