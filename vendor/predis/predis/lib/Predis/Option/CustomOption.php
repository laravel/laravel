<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Option;

/**
 * Implements a generic class used to dynamically define a client option.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class CustomOption implements OptionInterface
{
    private $filter;
    private $default;

    /**
     * @param array $options List of options
     */
    public function __construct(Array $options = array())
    {
        $this->filter = $this->ensureCallable($options, 'filter');
        $this->default = $this->ensureCallable($options, 'default');
    }

    /**
     * Checks if the specified value in the options array is a callable object.
     *
     * @param array  $options Array of options
     * @param string $key     Target option.
     */
    private function ensureCallable($options, $key)
    {
        if (!isset($options[$key])) {
            return;
        }

        if (is_callable($callable = $options[$key])) {
            return $callable;
        }

        throw new \InvalidArgumentException("The parameter $key must be callable");
    }

    /**
     * {@inheritdoc}
     */
    public function filter(ClientOptionsInterface $options, $value)
    {
        if (isset($value)) {
            if ($this->filter === null) {
                return $value;
            }

            return call_user_func($this->filter, $options, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(ClientOptionsInterface $options)
    {
        if (!isset($this->default)) {
            return;
        }

        return call_user_func($this->default, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ClientOptionsInterface $options, $value)
    {
        if (isset($value)) {
            return $this->filter($options, $value);
        }

        return $this->getDefault($options);
    }
}
