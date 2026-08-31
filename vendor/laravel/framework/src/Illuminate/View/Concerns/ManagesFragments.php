<?php

namespace Illuminate\View\Concerns;

use InvalidArgumentException;

trait ManagesFragments
{
    /**
     * All of the captured, rendered fragments.
     *
     * @var array
     */
    protected $fragments = [];

    /**
     * The stack of in-progress fragment renders.
     *
     * @var array
     */
    protected $fragmentStack = [];

    /**
     * Start injecting content into a fragment.
     *
     * @param  string  $fragment
     * @return void
     */
    public function startFragment($fragment)
    {
        if (ob_start()) {
            $this->fragmentStack[] = $fragment;
        }
    }

    /**
     * Stop injecting content into a fragment.
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function stopFragment()
    {
        if (empty($this->fragmentStack)) {
            throw new InvalidArgumentException('Cannot end a fragment without first starting one.');
        }

        $last = array_pop($this->fragmentStack);

        $this->fragments[$last] = ob_get_clean();

        return $this->fragments[$last];
    }

    /**
     * Get the contents of a fragment.
     *
     * @param  string  $name
     * @param  string|null  $default
     * @return mixed
     */
    public function getFragment($name, $default = null)
    {
        return $this->getFragments()[$name] ?? $default;
    }

    /**
     * Get the entire array of rendered fragments.
     *
     * @return array
     */
    public function getFragments()
    {
        return $this->fragments;
    }

    /**
     * Flush all of the fragments.
     *
     * @return void
     */
    public function flushFragments()
    {
        $this->fragments = [];
        $this->fragmentStack = [];
    }
}
