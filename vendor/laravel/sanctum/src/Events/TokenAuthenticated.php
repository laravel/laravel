<?php

namespace Laravel\Sanctum\Events;

class TokenAuthenticated
{
    /**
     * Create a new event instance.
     *
     * @param  \Laravel\Sanctum\PersonalAccessToken  $token  The personal access token that was authenticated.
     */
    public function __construct(public $token)
    {
    }
}
