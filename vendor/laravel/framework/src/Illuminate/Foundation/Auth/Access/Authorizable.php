<?php

namespace Illuminate\Foundation\Auth\Access;

use Illuminate\Contracts\Auth\Access\Gate;

trait Authorizable
{
    /**
     * Determine if the entity has the given abilities.
     *
     * @param  iterable|\UnitEnum|string  $abilities
     * @param  mixed  $arguments
     * @return bool
     */
    public function can($abilities, $arguments = [])
    {
        return app(Gate::class)->forUser($this)->check($abilities, $arguments);
    }

    /**
     * Determine if the entity has any of the given abilities.
     *
     * @param  iterable|\UnitEnum|string  $abilities
     * @param  mixed  $arguments
     * @return bool
     */
    public function canAny($abilities, $arguments = [])
    {
        return app(Gate::class)->forUser($this)->any($abilities, $arguments);
    }

    /**
     * Determine if the entity does not have the given abilities.
     *
     * @param  iterable|\UnitEnum|string  $abilities
     * @param  mixed  $arguments
     * @return bool
     */
    public function cant($abilities, $arguments = [])
    {
        return ! $this->can($abilities, $arguments);
    }

    /**
     * Determine if the entity does not have the given abilities.
     *
     * @param  iterable|\UnitEnum|string  $abilities
     * @param  mixed  $arguments
     * @return bool
     */
    public function cannot($abilities, $arguments = [])
    {
        return $this->cant($abilities, $arguments);
    }
}
