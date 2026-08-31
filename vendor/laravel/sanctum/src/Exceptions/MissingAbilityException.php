<?php

namespace Laravel\Sanctum\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;

class MissingAbilityException extends AuthorizationException
{
    /**
     * Create a new missing scope exception.
     *
     * @param  array|string  $abilities  The abilities that the user did not have.
     * @param  string  $message
     */
    public function __construct(protected $abilities = [], $message = 'Invalid ability provided.')
    {
        parent::__construct($message);

        $this->abilities = Arr::wrap($abilities);
    }

    /**
     * Get the abilities that the user did not have.
     *
     * @return array
     */
    public function abilities()
    {
        return $this->abilities;
    }
}
