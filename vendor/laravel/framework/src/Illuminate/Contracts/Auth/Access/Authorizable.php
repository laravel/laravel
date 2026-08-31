<?php

namespace Illuminate\Contracts\Auth\Access;

interface Authorizable
{
    /**
     * Determine if the entity has a given ability.
     *
     * @param  iterable|string  $abilities
     * @param  mixed  $arguments
     * @return bool
     */
    public function can($abilities, $arguments = []);
}
