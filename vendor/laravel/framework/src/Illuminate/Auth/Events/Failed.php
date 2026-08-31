<?php

namespace Illuminate\Auth\Events;

class Failed
{
    /**
     * Create a new event instance.
     *
     * @param  string  $guard  The authentication guard name.
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user  The user the attempter was trying to authenticate as.
     * @param  array  $credentials  The credentials provided by the attempter.
     */
    public function __construct(
        public $guard,
        public $user,
        #[\SensitiveParameter] public $credentials,
    ) {
    }
}
