<?php

namespace Illuminate\Contracts\Routing;

interface UrlGenerator
{
    /**
     * Get the current URL for the request.
     *
     * @return string
     */
    public function current();

    /**
     * Get the URL for the previous request.
     *
     * @param  mixed  $fallback
     * @return string
     */
    public function previous($fallback = false);

    /**
     * Generate an absolute URL to the given path.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    public function to($path, $extra = [], $secure = null);

    /**
     * Generate a secure, absolute URL to the given path.
     *
     * @param  string  $path
     * @param  array  $parameters
     * @return string
     */
    public function secure($path, $parameters = []);

    /**
     * Generate the URL to an application asset.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    public function asset($path, $secure = null);

    /**
     * Get the URL to a named route.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function route($name, $parameters = [], $absolute = true);

    /**
     * Create a signed route URL for a named route.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  \DateTimeInterface|\DateInterval|int|null  $expiration
     * @param  bool  $absolute
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function signedRoute($name, $parameters = [], $expiration = null, $absolute = true);

    /**
     * Create a temporary signed route URL for a named route.
     *
     * @param  string  $name
     * @param  \DateTimeInterface|\DateInterval|int  $expiration
     * @param  array  $parameters
     * @param  bool  $absolute
     * @return string
     */
    public function temporarySignedRoute($name, $expiration, $parameters = [], $absolute = true);

    /**
     * Generate an absolute URL with the given query parameters.
     *
     * @param  string  $path
     * @param  array  $query
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    public function query($path, $query = [], $extra = [], $secure = null);

    /**
     * Get the URL to a controller action.
     *
     * @param  string|array  $action
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    public function action($action, $parameters = [], $absolute = true);

    /**
     * Get the root controller namespace.
     *
     * @return string
     */
    public function getRootControllerNamespace();

    /**
     * Set the root controller namespace.
     *
     * @param  string  $rootNamespace
     * @return $this
     */
    public function setRootControllerNamespace($rootNamespace);
}
