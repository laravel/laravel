<?php

namespace Illuminate\Contracts\Container;

interface ContextualBindingBuilder
{
    /**
     * Define the abstract target that depends on the context.
     *
     * @param  string  $abstract
     * @return $this
     */
    public function needs($abstract);

    /**
     * Define the implementation for the contextual binding.
     *
     * @param  \Closure|string|array  $implementation
     * @return $this
     */
    public function give($implementation);

    /**
     * Define tagged services to be used as the implementation for the contextual binding.
     *
     * @param  string  $tag
     * @return $this
     */
    public function giveTagged($tag);

    /**
     * Specify the configuration item to bind as a primitive.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return $this
     */
    public function giveConfig($key, $default = null);
}
