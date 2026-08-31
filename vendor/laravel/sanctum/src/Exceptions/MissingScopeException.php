<?php

namespace Laravel\Sanctum\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;

/**
 * @deprecated
 * @see \Laravel\Sanctum\Exceptions\MissingAbilityException
 */
class MissingScopeException extends AuthorizationException
{
    /**
     * Create a new missing scope exception.
     *
     * @param  array|string  $scopes  The scopes that the user did not have.
     * @param  string  $message
     */
    public function __construct(protected $scopes = [], $message = 'Invalid scope(s) provided.')
    {
        parent::__construct($message);

        $this->scopes = Arr::wrap($scopes);
    }

    /**
     * Get the scopes that the user did not have.
     *
     * @return array
     */
    public function scopes()
    {
        return $this->scopes;
    }
}
