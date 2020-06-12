<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

/**
 * Set the currently logged in user for the application.
 */
function actingAs(Authenticatable $user, string $driver = null): TestCase
{
    return test()->actingAs($user, $driver);
}
