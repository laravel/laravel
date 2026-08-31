<?php

namespace Illuminate\Foundation\Testing\Concerns;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;

trait InteractsWithAuthentication
{
    /**
     * Set the currently logged in user for the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string|null  $guard
     * @return $this
     */
    public function actingAs(UserContract $user, $guard = null)
    {
        return $this->be($user, $guard);
    }

    /**
     * Clear the currently logged in user for the application.
     *
     * @param  string|null  $guard
     * @return $this
     */
    public function actingAsGuest($guard = null)
    {
        $this->app['auth']->guard($guard)->forgetUser();

        $this->app['auth']->shouldUse($guard);

        return $this;
    }

    /**
     * Set the currently logged in user for the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string|null  $guard
     * @return $this
     */
    public function be(UserContract $user, $guard = null)
    {
        if (isset($user->wasRecentlyCreated) && $user->wasRecentlyCreated) {
            $user->wasRecentlyCreated = false;
        }

        $this->app['auth']->guard($guard)->setUser($user);

        $this->app['auth']->shouldUse($guard);

        return $this;
    }

    /**
     * Assert that the user is authenticated.
     *
     * @param  string|null  $guard
     * @return $this
     */
    public function assertAuthenticated($guard = null)
    {
        $this->assertTrue($this->isAuthenticated($guard), 'The user is not authenticated');

        return $this;
    }

    /**
     * Assert that the user is not authenticated.
     *
     * @param  string|null  $guard
     * @return $this
     */
    public function assertGuest($guard = null)
    {
        $this->assertFalse($this->isAuthenticated($guard), 'The user is authenticated');

        return $this;
    }

    /**
     * Return true if the user is authenticated, false otherwise.
     *
     * @param  string|null  $guard
     * @return bool
     */
    protected function isAuthenticated($guard = null)
    {
        return $this->app->make('auth')->guard($guard)->check();
    }

    /**
     * Assert that the user is authenticated as the given user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string|null  $guard
     * @return $this
     */
    public function assertAuthenticatedAs($user, $guard = null)
    {
        $expected = $this->app->make('auth')->guard($guard)->user();

        $this->assertNotNull($expected, 'The current user is not authenticated.');

        $this->assertInstanceOf(
            get_class($expected), $user,
            'The currently authenticated user is not who was expected'
        );

        $this->assertSame(
            $expected->getAuthIdentifier(), $user->getAuthIdentifier(),
            'The currently authenticated user is not who was expected'
        );

        return $this;
    }

    /**
     * Assert that the given credentials are valid.
     *
     * @param  array  $credentials
     * @param  string|null  $guard
     * @return $this
     */
    public function assertCredentials(array $credentials, $guard = null)
    {
        $this->assertTrue(
            $this->hasCredentials($credentials, $guard), 'The given credentials are invalid.'
        );

        return $this;
    }

    /**
     * Assert that the given credentials are invalid.
     *
     * @param  array  $credentials
     * @param  string|null  $guard
     * @return $this
     */
    public function assertInvalidCredentials(array $credentials, $guard = null)
    {
        $this->assertFalse(
            $this->hasCredentials($credentials, $guard), 'The given credentials are valid.'
        );

        return $this;
    }

    /**
     * Return true if the credentials are valid, false otherwise.
     *
     * @param  array  $credentials
     * @param  string|null  $guard
     * @return bool
     */
    protected function hasCredentials(array $credentials, $guard = null)
    {
        $provider = $this->app->make('auth')->guard($guard)->getProvider();

        $user = $provider->retrieveByCredentials($credentials);

        return $user && $provider->validateCredentials($user, $credentials);
    }
}
