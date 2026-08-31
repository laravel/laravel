<?php

namespace Laravel\Sanctum\Contracts;

interface HasAbilities
{
    /**
     * Determine if the token has a given ability.
     *
     * @param  string  $ability
     * @return bool
     */
    public function can($ability);

    /**
     * Determine if the token is missing a given ability.
     *
     * @param  string  $ability
     * @return bool
     */
    public function cant($ability);
}
