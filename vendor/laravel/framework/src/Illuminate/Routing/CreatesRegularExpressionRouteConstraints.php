<?php

namespace Illuminate\Routing;

use Illuminate\Support\Collection;

use function Illuminate\Support\enum_value;

trait CreatesRegularExpressionRouteConstraints
{
    /**
     * Specify that the given route parameters must be alphabetic.
     *
     * @param  array|string  $parameters
     * @return $this
     */
    public function whereAlpha($parameters)
    {
        return $this->assignExpressionToParameters($parameters, '[a-zA-Z]+');
    }

    /**
     * Specify that the given route parameters must be alphanumeric.
     *
     * @param  array|string  $parameters
     * @return $this
     */
    public function whereAlphaNumeric($parameters)
    {
        return $this->assignExpressionToParameters($parameters, '[a-zA-Z0-9]+');
    }

    /**
     * Specify that the given route parameters must be numeric.
     *
     * @param  array|string  $parameters
     * @return $this
     */
    public function whereNumber($parameters)
    {
        return $this->assignExpressionToParameters($parameters, '[0-9]+');
    }

    /**
     * Specify that the given route parameters must be ULIDs.
     *
     * @param  array|string  $parameters
     * @return $this
     */
    public function whereUlid($parameters)
    {
        return $this->assignExpressionToParameters($parameters, '[0-7][0-9a-hjkmnp-tv-zA-HJKMNP-TV-Z]{25}');
    }

    /**
     * Specify that the given route parameters must be UUIDs.
     *
     * @param  array|string  $parameters
     * @return $this
     */
    public function whereUuid($parameters)
    {
        return $this->assignExpressionToParameters($parameters, '[\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}');
    }

    /**
     * Specify that the given route parameters must be one of the given values.
     *
     * @param  array|string  $parameters
     * @param  array  $values
     * @return $this
     */
    public function whereIn($parameters, array $values)
    {
        return $this->assignExpressionToParameters(
            $parameters,
            (new Collection($values))
                ->map(fn ($value) => enum_value($value))
                ->implode('|')
        );
    }

    /**
     * Apply the given regular expression to the given parameters.
     *
     * @param  array|string  $parameters
     * @param  string  $expression
     * @return $this
     */
    protected function assignExpressionToParameters($parameters, $expression)
    {
        return $this->where(Collection::wrap($parameters)
            ->mapWithKeys(fn ($parameter) => [$parameter => $expression])
            ->all());
    }
}
