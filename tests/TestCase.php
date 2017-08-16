<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Set the currently logged in user for the application.
     *
     * @param  mixed  $user
     * @param string|null  $driver
     * @return $this
     */
    protected function signIn($user = null, $driver = null)
    {
        if (is_null($user)) {
            $user = factory(\App\User::class)->create();
        }

        return $this->actingAs($user, $driver);
    }
}
